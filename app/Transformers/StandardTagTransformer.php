<?php

namespace App\Transformers;

use stdClass;
use App\Models\Wishlist;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use App\Transformers\Transformer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Transformers\AttributeTypeTransformer;

class StandardTagTransformer extends Transformer
{
    public function transform($standardTag, $options = null)
    {
        $user = auth('sanctum')->user();
        $standardTagResponse = [
            'id' => (int) $standardTag->id,
            'name' => resetStringFormat((string) $standardTag->name),
            'slug' => (string) $standardTag->slug,
            'type' => (string) $standardTag->type,
            'icon' => $standardTag->icon ? getImage($standardTag->icon, 'icon')
                : getImage(NULL, 'icon'),
            'in_wishlist' => $user ? $standardTag->wishList()->where('user_id', $user->id)->exists() : false,
            'is_body_style' => Arr::has(\config()->get('automotive.body_styles'), $standardTag->name) ? true : false,
            'can_chat' => (boolean) $standardTag->can_chat
        ];
        $productOptions = [
            'withMinimumData' => \true,
            'withVariants' => \true
        ];
        $levelTwoFlag = request()->input('levelTwoFlag');
        if (!isset($options['withFilters'])) {
            if (request()->input('favoriteProducts')) {
                $products = $standardTag->productTags->sortByDesc(function ($productTag) {
                    $wishlist = $productTag->wishList; // Assuming a one-to-one relationship with wishlist
                    if (count($wishlist) > 0) {
                        return $wishlist[0]->created_at;
                    }
                })->unique('id')->values();
                $standardTagResponse['products'] = count($products) > 0  ? (new ProductTransformer)->transformCollection($products->unique('id')->unique('id'), $productOptions) : [];
            } else {
                $standardTagResponse['products'] = $standardTag->relationLoaded('productTags')
                    ? (new ProductTransformer)->transformCollection($standardTag->productTags->unique('id')->values(), $productOptions)
                    : [];
            }
        }
        if (isset($options['withChildrens']) && $options['withChildrens']) {
            $standardTagResponse['childrens'] = (new StandardTagTransformer)->transformCollection($this->getLevelThree($standardTag, $options));
        }

        if (isset($options['level-four-count']) && $options['level-four-count']) {
            $standardTagResponse['level_four_tag'] = $this->getLevelFourCount($standardTag->id, $options);
        }

        return $standardTagResponse;
    }


    private function getLevelThree($tag, $options)
    {
        $module = $options['levelOne'];

        $tags = StandardTag::when(
            in_array($module->slug, ['retail', 'automotive', 'boats']),
            function ($query) {
                $query->filterProducts();
            },
            function ($query) use ($module, $tag) {
                $query->whereHas('productTags', function ($subQuery) use ($module, $tag) {
                    $subQuery->whereRelation('standardTags', 'id', $module->id)->whereRelation('standardTags', 'id', $tag->id);
                    $subQuery->where('status', 'active');
                });
            }
        )->whereHas('levelThree', function ($subQuery) use ($tag, $module) {
            $subQuery->where('L1', $module->id)->where('L2', $tag->id);
        })->active()->get();

        return $tags;
    }

    private function getLevelFourTag($id, $options)
    {
        $module_id = (int) $options['moduleId'];
        $business = $options['business'];
        $levelTwo = $options['level_two_tags'];
        $productTags = StandardTag::whereRelation('productTags.business', function ($query) use ($business) {
            $query->where('uuid', $business)->orWhere('slug', $business);
        })->where(function ($query) use ($module_id, $levelTwo, $id) {
            $query->whereHas('LevelFour', function ($subQuery) use ($module_id, $levelTwo, $id) {
                $subQuery->where('L1', $module_id)->where('L3', $id)->whereIn('L2', $levelTwo);
            })->orWhereHas('tagHierarchies', function ($subQuery) use ($module_id, $levelTwo, $id) {
                $subQuery->where('L1', $module_id)->where('level_type', 4)->where('L3', $id)->whereIn('L2', $levelTwo);
            });
        })->whereType('product')->active()->get();

        return $productTags;
    }

    // check count of level four tags
    private function getLevelFourCount($tag, $options)
    {
        $levelOneSlug = $options['levelOneTag']->slug ?? $options['levelOne']->slug;
        $levelOneId = $options['levelOneTag']->id ?? $options['levelOne']->id;
        $levelFourTags = StandardTag::whereHas('productTags', function ($query) use ($levelOneSlug, $levelOneId, $options, $tag) {
            $query->when(in_array($levelOneSlug, ['retail', 'automotive', 'boats', 'employment', 'services', 'government']), function ($innerQuery) {
                $innerQuery->active();
            }, function ($innerQuery) use ($options, $tag) {
                $innerQuery->where('status', 'active');
            });
            $query->whereHas('standardTags', function ($subQuery) use ($levelOneId, $options, $tag) {
                $subQuery->whereIn('id', [$levelOneId, $options['levelTwoTag'], $tag])
                    ->select('*', DB::raw('count(*) as total'))
                    ->having('total', '>=', 3);
            });
        })->whereHas('tagHierarchies', function ($query) use ($levelOneId, $tag, $options) {
            $query->where('level_type', 4)->where('L1', $levelOneId)->where('L2', $options['levelTwoTag'])->where('L3', $tag);
        })->get();
        return $levelFourTags->count() === 1 ? $levelFourTags[0] : null;
    }
}
