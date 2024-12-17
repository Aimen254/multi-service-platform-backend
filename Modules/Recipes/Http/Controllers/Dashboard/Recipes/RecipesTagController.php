<?php

namespace Modules\Recipes\Http\Controllers\Dashboard\Recipes;

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

class RecipesTagController extends Controller
{
    public function index($moduleId, $uuid)
    {
        try {
            $recipes = Product::whereUuid($uuid)->firstOrFail();
            // Getting orphan tags
            $orphanTags = $recipes->tags()->asTag()->where(function ($query) {
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
            $extraOrphanTags = $recipes->tags()->asTag()->whereDoesntHave('standardTags_')->get();
            // Getting extra standard tags
            $extraStandardTags = $recipes->standardTags()->asTag()->wherePriority(4)->whereHas('tags_')->get();
            // Getting tags
            $extraTags = Arr::collapse([$extraOrphanTags, $extraStandardTags]);
            // Getting ignored orphan tags
            $ignoredOrphanTags = $recipes->tags()->asTag()->whereHas('standardTags_', function ($query) {
                $query->where('priority', '<>', 1);
            })->orWhereDoesntHave('standardTags_')->where('product_id', $recipes->id)->get()->reject(function ($record) {
                $tag = $record->whereHas('standardTags_', function ($query) use ($record) {
                    $query->where('priority', '<>', 1)->where('slug', $record->slug);
                })->first();
                return $tag ? true : false;
            });
            // Getting ingnored tags
            $ignoredTags = $recipes->ignoredTags()->first();
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
            $ignoredStandardTags = $recipes->standardTags()->asTag()->where('type', '<>', 'module')->where('priority', '<>', 1)->get();

            return inertia('Recipes::Recipes/Settings/AssignTags', [
                'recipes' => $recipes,
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

    private function storeStandardTags($recipes, $standardTags): void
    {
        foreach ($standardTags as $id) {
            $standardTag = StandardTag::find($id);
            $existInBothLevels = $standardTag->whereHas('levelThree')->whereHas('tagHierarchies')->where('id', $standardTag->id)->first();
            if ($existInBothLevels) {
                $productLevelThreeTag = $recipes->standardTags()->where('id', '<>', $standardTag->id)->whereHas('levelTHree')->first();
                if ($productLevelThreeTag) {
                    $exsistInlevelFour = $standardTag->whereHas('tagHierarchies', function ($query) use ($productLevelThreeTag) {
                        $query->where('L3', $productLevelThreeTag->id);
                    })->where('id', $standardTag->id)->first();
                    if ($exsistInlevelFour) {
                        $recipes->standardTags()->syncWithOutDetaching($standardTag->id);
                    }
                }
            } else {
                $recipes->standardTags()->syncWithOutDetaching($standardTag->id);
            }
        }
    }

    public function assignTags($moduleId, $uuid)
    {
        try {
            ModuleSessionManager::setModule('recipes');
            $recipes = Product::whereUuid($uuid)->firstOrFail();
            DB::beginTransaction();
            $tags = $this->handleTags($moduleId, request()->input('tags'), $recipes);
            $categoryTags = collect(json_decode(request()->input('categoryTags')))->pluck('id')->toArray();
            $ignoredTagIds = $this->productIgnoredTags(request()->ignoredTags, $recipes);
            $productCategoryTags = $recipes->tags()->asTag()->where(function ($query) {
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
            $recipes->tags()->sync($orphanTags);
            $standardTagIds =  $recipes->standardTags()->whereNotIn('type', ['module', 'attribute'])->wherePriority(4)->pluck('id')->toArray();
            $standardTags = Arr::collapse([$tags['standardTags']]);

            if (request()->input('removeStandardTags')) {
                $standardTags = array_diff($standardTags, request()->input('removeStandardTags'));
                $recipes->standardTags()->detach(request()->input('removeStandardTags'));
            }

            $removeTags = array_diff($standardTagIds, $standardTags);
            $recipes->standardTags()->detach($removeTags);

            $this->storeStandardTags($recipes, $standardTags);

            $recipes->tags()->detach($ignoredTagIds['ignoredOrphanTagIds']);
            $recipes->standardTags()->detach($ignoredTagIds['ignoredStandardTagIds']);
            ProductTagsLevelManager::checkProductTagsLevel($recipes);
            ProductTagsLevelManager::priorityOneTags($recipes, $removeCategoryTags, 'product_tag');
            ProductTagsLevelManager::priorityFour($recipes, $tags['orphanTags']);
            ProductTagsLevelManager::priorityTwoTags($recipes);
            DB::commit();
            flash('Recipes tags updated successfully.', 'success');
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
    // get and store ignored tags of recipes
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
