<?php

namespace Modules\Classifieds\Http\Controllers\Dashboard\Classifieds;

use App\Models\Tag;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClassifiedTagController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {
        try {
            $classified = Product::whereUuid($uuid)->firstOrFail();
            // Getting orphan tags
            $orphanTags = $classified->tags()->asTag()->where(function ($query) {
                $query->where('is_category', true)->whereHas('standardTags_', function ($query) {
                    $query->where('priority', 1);
                });
            })->get()->reject(function ($record) {
                $tag = $record->whereHas('standardTags_', function ($query) use ($record) {
                    $query->where('priority', 1)->where('slug', $record->slug);
                })->first();
                return $tag ? true : false;
            });
            // Getting extra orphan tags
            $extraOrphanTags = $classified->tags()->asTag()->whereDoesntHave('standardTags_')->get();
            // Getting extra standard tags
            $extraStandardTags = $classified->standardTags()->asTag()->wherePriority(4)->whereHas('tags_')->get();
            // Getting tags
            $extraTags = Arr::collapse([$extraOrphanTags, $extraStandardTags]);
            // Getting ignored orphan tags
            $ignoredOrphanTags = $classified->tags()->asTag()->whereHas('standardTags_', function ($query) {
                $query->where('priority', '<>', 1);
            })->orWhereDoesntHave('standardTags_')->where('product_id', $classified->id)->get()->reject(function ($record) {
                $tag = $record->whereHas('standardTags_', function ($query) use ($record) {
                    $query->where('priority', '<>', 1)->where('slug', $record->slug);
                })->first();
                return $tag ? true : false;
            });
            // Getting ingnored tags
            $ignoredTags = $classified->ignoredTags()->first();
            $productIgnoredTags = [];
            if ($ignoredTags) {
                $data = json_decode($ignoredTags->tags);
                $productIgnoredTags = array_map(function ($item) {
                    return [
                        "text" => $item,
                    ];
                }, $data);
            }
            // Getting ignored standard tags
            $ignoredStandardTags = $classified->standardTags()->asTag()->where('type', '<>', 'module')->where('priority', '<>', 1)->get();

            return inertia('Classifieds::Classifieds/Settings/AssignTags', [
                'classified' => $classified,
                'allproductTags' => Arr::collapse([$orphanTags]),
                'allTags' => StandardTag::asTag()->active()->where('type', '!=', 'module')->get(),
                'allproductBrandTags' => $classified->standardTags()->asTag()->where('type', 'brand')->get(),
                'allBrandTags' =>  StandardTag::asTag()->active()->where('type', 'brand')->get(),
                'extraTags' => $extraTags,
                'allIgnoredAutocomplete' => Arr::collapse([$ignoredOrphanTags, $ignoredStandardTags]),
                'productIgnoredTags' => $productIgnoredTags
            ]);
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    private function storeStandardTags($classified, $standardTags): void
    {
        foreach ($standardTags as $id) {
            $standardTag = StandardTag::find($id);
            $existInBothLevels = $standardTag->whereHas('levelThree')->whereHas('tagHierarchies')->where('id', $standardTag->id)->first();
            if ($existInBothLevels) {
                $productLevelThreeTag = $classified->standardTags()->where('id', '<>', $standardTag->id)->whereHas('levelTHree')->first();
                if ($productLevelThreeTag) {
                    $exsistInlevelFour = $standardTag->whereHas('tagHierarchies', function ($query) use ($productLevelThreeTag) {
                        $query->where('L3', $productLevelThreeTag->id);
                    })->where('id', $standardTag->id)->first();
                    if ($exsistInlevelFour) {
                        $classified->standardTags()->syncWithOutDetaching($standardTag->id);
                    }
                }
            } else {
                $classified->standardTags()->syncWithOutDetaching($standardTag->id);
            }
        }
    }

    public function assignTags($moduleId, $uuid)
    {
        try {
            ModuleSessionManager::setModule(Product::MODULE_MARKETPLACE);
            $classified = Product::whereUuid($uuid)->firstOrFail();
            DB::beginTransaction();
            $tags = $this->handleTags($moduleId, request()->input('tags'), $classified);
            $categoryTags = collect(json_decode(request()->input('categoryTags')))->pluck('id')->toArray();
            $ignoredTagIds = $this->productIgnoredTags(request()->ignoredTags, $classified);
            $productCategoryTags = $classified->tags()->asTag()->where(function ($query) {
                $query->where('is_category', true)->whereHas('standardTags_', function ($query) {
                    $query->where('priority', 1);
                });
            })->get()->reject(function ($record) {
                $tag = $record->whereHas('standardTags_', function ($query) use ($record) {
                    $query->where('priority', 1)->where('slug', $record->slug);
                })->first();
                return $tag ? true : false;
            })->pluck('id')->toArray();

            $removeCategoryTags = array_diff($productCategoryTags, $categoryTags);
            if (request()->input('removeOrphans')) {
                $tags['orphanTags'] = array_diff($tags['orphanTags'], request()->input('removeOrphans'));
            }

            $orphanTags = Arr::collapse([$tags['orphanTags'], $categoryTags]);
            $orphanTags = array_diff($orphanTags, $removeCategoryTags);
            $classified->tags()->sync($orphanTags);
            $standardTagIds =  $classified->standardTags()->whereNotIn('type', ['module', 'attribute'])->wherePriority(4)->pluck('id')->toArray();
            $brandTags = collect(json_decode(request()->input('brandTags')))->pluck('id')->toArray();
            $standardTags = Arr::collapse([$tags['standardTags'], $brandTags]);
            // $standardTags = Arr::collapse([$tags['standardTags']]);

            if (request()->input('removeStandardTags')) {
                $standardTags = array_diff($standardTags, request()->input('removeStandardTags'));
                $classified->standardTags()->detach(request()->input('removeStandardTags'));
            }

            $removeTags = array_diff($standardTagIds, $standardTags);
            $classified->standardTags()->detach($removeTags);

            $this->storeStandardTags($classified, $standardTags);

            $previousbrandTags = $classified->standardTags()->where('type', 'brand')->pluck('id')->toArray();
            $removeBrandTags = array_diff($previousbrandTags, $brandTags);

            $classified->standardTags()->detach($removeBrandTags);
            $classified->tags()->detach($ignoredTagIds['ignoredOrphanTagIds']);
            $classified->standardTags()->detach($ignoredTagIds['ignoredStandardTagIds']);
            ProductTagsLevelManager::checkProductTagsLevel($classified);
            ProductTagsLevelManager::priorityOneTags($classified, $removeCategoryTags, 'product_tag');
            ProductTagsLevelManager::priorityFour($classified, $tags['orphanTags']);
            ProductTagsLevelManager::priorityTwoTags($classified);
            ProductTagsLevelManager::priorityThree($classified, null, false, $brandTags, null, count($removeBrandTags) > 0 ? $removeBrandTags : null);
            DB::commit();
            flash('Classified tags updated successfully.', 'success');
            return back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->withErrors([
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Return array of 2 item one for extra tags
     * which are orphan tags and one for standard tags
     */
    public function handleTags($moduleId, $extraTags, $product)
    {
        $newTags = collect(json_decode($extraTags))->pluck('text')->toArray();
        $orphanTags = [];
        $tags = [];
        foreach ($newTags as $newTag) {
            $standardTag = StandardTag::where('slug', orphanTagSlug($newTag))->first();
            // checking if tag is in orphan tags
            if ($standardTag) {
                // tag is in standard tag grab its id for product_standard_tag sync
                \array_push($tags, $standardTag->id);
                //Creating orphan tags
                if ($standardTag->type != 'module' && $standardTag->type != 'industry') {
                    $new = Tag::updateOrCreate([
                        'slug' => Str::slug($newTag)
                    ], [
                        'name' => $newTag,
                        'global_tag_id' => $moduleId,
                        'type' => $standardTag ? $standardTag->type : null,
                        'attribute_id' => $standardTag ? $standardTag->attribute_id : null,
                        'priority' => $standardTag ? $standardTag->priority : null,
                    ]);
                    \array_push($orphanTags, $new->id);
                    $new->standardTags_()->syncWithOutDetaching($standardTag->id);
                }
            } else {

                $tag = Tag::updateOrCreate(['slug' => orphanTagSlug($newTag), 'name' => $newTag], [
                    'priority' => 4,
                ]);
                //if tag is mapped get its mapped id
                if ($tag) {
                    $standardTags = $tag->standardTags_()->whereHas('productTags', function ($query) use ($product) {
                        $query->where('id', $product->id);
                    })->pluck('id');
                    $tags = Arr::collapse([$tags, $standardTags]);
                    \array_push($orphanTags, $tag->id);
                }
            }
        }
        $productTags = $product->standardTags()->pluck('id')->toArray();
        $tags = Arr::collapse([$tags, $productTags]);
        $tags = array_unique($tags);
        $orphan = $product->tags()->pluck('id')->toArray();
        $orphanTags = Arr::collapse([$orphanTags, $orphan]);
        $orphanTags = array_unique($orphanTags);
        return [
            'orphanTags' => $orphanTags,
            'standardTags' => Arr::collapse([$tags])
        ];
    }

    // get and store ignored tags of classified
    public function productIgnoredTags($tags, $product)
    {
        $tags = collect(json_decode($tags))->pluck('text')->toArray();
        $product->ignoredTags()->updateOrCreate(
            ['product_id' => $product->id],
            ['tags' => json_encode($tags)]
        );
        $standardTagIds = $product->standardTags()->where('priority', '<>', 1)->whereIn('name', $tags)->pluck('id')->toArray();
        $orphanTagIds = $product->tags()->where('priority', '<>', 1)->whereIn('name', $tags)->pluck('id')->toArray();
        return [
            'ignoredOrphanTagIds' => $orphanTagIds,
            'ignoredStandardTagIds' => $standardTagIds
        ];
    }
}
