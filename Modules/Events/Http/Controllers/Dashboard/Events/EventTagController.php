<?php

namespace Modules\Events\Http\Controllers\Dashboard\Events;

use Illuminate\Contracts\Support\Renderable;
use App\Models\Tag;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;


class EventTagController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    
    public function index($moduleId, $uuid)
    {
        try {
            $event = Product::whereUuid($uuid)->firstOrFail();
            // Getting orphan tags
            $orphanTags = $event->tags()->asTag()->where(function ($query) {
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
            $extraOrphanTags = $event->tags()->asTag()->whereDoesntHave('standardTags_')->get();
            // Getting extra standard tags
            $extraStandardTags = $event->standardTags()->asTag()->wherePriority(4)->whereHas('tags_')->get();
            // Getting tags
            $extraTags = Arr::collapse([$extraOrphanTags, $extraStandardTags]);
            // Getting ignored orphan tags
            $ignoredOrphanTags = $event->tags()->asTag()->whereHas('standardTags_', function ($query) {
                $query->where('priority', '<>', 1);
            })->orWhereDoesntHave('standardTags_')->where('product_id', $event->id)->get()->reject(function ($record) {
                $tag = $record->whereHas('standardTags_', function ($query) use ($record) {
                    $query->where('priority', '<>', 1)->where('slug', $record->slug);
                })->first();
                return $tag ? true : false;
            });
            // Getting ingnored tags
            $ignoredTags = $event->ignoredTags()->first();
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
            $ignoredStandardTags = $event->standardTags()->asTag()->where('type', '<>', 'module')->where('priority', '<>', 1)->get();

            return inertia('Events::Settings/AssignTags', [
                'event' => $event,
                'allproductTags' => Arr::collapse([$orphanTags]),
                'allTags' => StandardTag::asTag()->active()->where('type', '!=', 'module')->get(),
                'extraTags' => $extraTags,
                'allIgnoredAutocomplete' => Arr::collapse([$ignoredOrphanTags, $ignoredStandardTags]),
                'productIgnoredTags' => $productIgnoredTags
            ]);
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    private function storeStandardTags($blog, $standardTags): void
    {
        foreach ($standardTags as $id) {
            $standardTag = StandardTag::find($id);
            $existInBothLevels = $standardTag->whereHas('levelThree')->whereHas('tagHierarchies')->where('id', $standardTag->id)->first();
            if ($existInBothLevels) {
                $productLevelThreeTag = $blog->standardTags()->where('id', '<>', $standardTag->id)->whereHas('levelTHree')->first();
                if ($productLevelThreeTag) {
                    $exsistInlevelFour = $standardTag->whereHas('tagHierarchies', function ($query) use ($productLevelThreeTag) {
                        $query->where('L3', $productLevelThreeTag->id);
                    })->where('id', $standardTag->id)->first();
                    if ($exsistInlevelFour) {
                        $blog->standardTags()->syncWithOutDetaching($standardTag->id);
                    }
                }
            } else {
                $blog->standardTags()->syncWithOutDetaching($standardTag->id);
            }
        }
    }

    public function assignTags($moduleId, $uuid)
    {
        try {
            ModuleSessionManager::setModule('blogs');
            $blog = Product::whereUuid($uuid)->firstOrFail();
            DB::beginTransaction();
            $tags = $this->handleTags($moduleId, request()->input('tags'), $blog);
            $categoryTags = collect(json_decode(request()->input('categoryTags')))->pluck('id')->toArray();
            $ignoredTagIds = $this->productIgnoredTags(request()->ignoredTags, $blog);
            $productCategoryTags = $blog->tags()->asTag()->where(function ($query) {
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
            $blog->tags()->sync($orphanTags);
            $standardTagIds =  $blog->standardTags()->whereNotIn('type', ['module', 'attribute'])->wherePriority(4)->pluck('id')->toArray();
            $standardTags = Arr::collapse([$tags['standardTags']]);

            if (request()->input('removeStandardTags')) {
                $standardTags = array_diff($standardTags, request()->input('removeStandardTags'));
                $blog->standardTags()->detach(request()->input('removeStandardTags'));
            }

            $removeTags = array_diff($standardTagIds, $standardTags);
            $blog->standardTags()->detach($removeTags);

            $this->storeStandardTags($blog, $standardTags);

            $blog->tags()->detach($ignoredTagIds['ignoredOrphanTagIds']);
            $blog->standardTags()->detach($ignoredTagIds['ignoredStandardTagIds']);
            ProductTagsLevelManager::checkProductTagsLevel($blog);
            ProductTagsLevelManager::priorityOneTags($blog, $removeCategoryTags, 'product_tag');
            ProductTagsLevelManager::priorityFour($blog, $tags['orphanTags']);
            ProductTagsLevelManager::priorityTwoTags($blog);
            DB::commit();
            flash('Blog tags updated successfully.', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return \redirect()->back()->withErrors([
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return \redirect()->back()->withErrors([
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

    // get and store ignored tags of blog
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
