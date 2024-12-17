<?php

namespace Modules\Automotive\Http\Controllers\Dashboard\Vehicle;

use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VehicleTagsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {
        try {
            $product = Product::whereUuid($uuid)->firstOrFail();
            $orphanTags = $product->tags()->asTag()->where(function ($query) {
                $query->where('is_category', true)->whereHas('standardTags_', function ($query) {
                    $query->where('priority', 1);
                });
            })->get()->reject(function ($record) {
                $tag = $record->whereHas('standardTags_', function ($query) use ($record) {
                    $query->where('priority', 1)->where('slug', $record->slug);
                })->first();
                return $tag ? true : false;
            });
            $extraOrphanTags = $product->tags()->asTag()->whereDoesntHave('standardTags_')->get();
            $extraStandardTags = $product->standardTags()->asTag()->wherePriority(4)->whereHas('tags_')->get();
            $extraTags = Arr::collapse([$extraOrphanTags, $extraStandardTags]);

            $ignoredOrphanTags = $product->tags()->asTag()->whereHas('standardTags_', function ($query) {
                $query->where('priority', '<>', 1);
            })->orWhereDoesntHave('standardTags_')->where('product_id', $product->id)->get()->reject(function ($record) {
                $tag = $record->whereHas('standardTags_', function ($query) use ($record) {
                    $query->where('priority', '<>', 1)->where('slug', $record->slug);
                })->first();
                return $tag ? true : false;
            });

            $IgnoredTags = $product->ignoredTags()->first();
            $productIgnoredTags = [];
            if ($IgnoredTags) {
                $data = json_decode($IgnoredTags->tags);
                $productIgnoredTags = array_map(function ($item) {
                    return [
                        "text" => $item,
                    ];
                }, $data);
            }
            $ignoredStandardTags = $product->standardTags()->asTag()->where('type', '<>', 'module')->where('priority', '<>', 1)->get();

            return Inertia::render('Automotive::Vehicles/Settings/AssignTags', [
                'product' => $product,
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

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('automotive::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('automotive::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('automotive::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function assignTags($moduleId, $uuid)
    {
        try {
            $product = Product::whereUuid($uuid)->firstOrFail();
            DB::beginTransaction();
            $tags = $this->handleTags($moduleId, request()->input('tags'), $product);
            $categoryTags = collect(json_decode(request()->input('categoryTags')))->pluck('id')->toArray();
            $ignoredTagIds = $this->productIgnoredTags(request()->ignoredTags, $product);
            $productCategoryTags = $product->tags()->asTag()->where(function ($query) {
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
            $product->tags()->sync($orphanTags);
            $standardTagIds =  $product->standardTags()->whereNotIn('type', ['module', 'attribute'])->wherePriority(4)->pluck('id')->toArray();
            $standardTags = Arr::collapse([$tags['standardTags']]);
            if (request()->input('removeStandardTags')) {
                $standardTags = array_diff($standardTags, request()->input('removeStandardTags'));
                $product->standardTags()->detach(request()->input('removeStandardTags'));
            }
            $removeTags = array_diff($standardTagIds, $standardTags);
            $product->standardTags()->detach($removeTags);
            $business = $product->business()->first();
            foreach ($standardTags as $id) {
                $standardTag = StandardTag::find($id);
                $existInBothLevels = $standardTag->whereHas('levelThree')->whereHas('tagHierarchies')->where('id', $standardTag->id)->first();
                if ($existInBothLevels) {
                    $productLevelThreeTag = $product->standardTags()->where('id', '<>', $standardTag->id)->whereHas('levelTHree')->first();
                    if ($productLevelThreeTag) {
                        $exsistInlevelFour = $standardTag->whereHas('tagHierarchies', function ($query) use ($productLevelThreeTag) {
                            $query->where('L3', $productLevelThreeTag->id);
                        })->where('id', $standardTag->id)->first();
                        if ($exsistInlevelFour) {
                            $product->standardTags()->syncWithOutDetaching($standardTag->id);
                        }
                    } else {
                        $checkBusinessLevelThreeTags = $business->standardTags()->where('id', $standardTag->id)->whereHas('levelThree')->first();
                        if ($checkBusinessLevelThreeTags) {
                            $product->standardTags()->syncWithOutDetaching($standardTag->id);
                        }
                    }
                } else {
                    $existInLevelThree = $standardTag->whereHas('levelThree')->where('id', $standardTag->id)->first();
                    if ($existInLevelThree) {
                        $productLevelThreeTag = $product->standardTags()->whereHas('levelThree')->first();
                        if ($productLevelThreeTag) {
                            $checkBusinessLevelThreeTags = $business->standardTags()->where('id', $standardTag->id)->whereHas('levelThree')->first();
                            if (!$checkBusinessLevelThreeTags) {
                                $product->standardTags()->syncWithOutDetaching($standardTag->id);
                            }
                        }
                    } else {
                        $product->standardTags()->syncWithOutDetaching($standardTag->id);
                    }
                }
            }

            $product->tags()->detach($ignoredTagIds['ignoredOrphanTagIds']);
            $product->standardTags()->detach($ignoredTagIds['ignoredStandardTagIds']);
            ProductTagsLevelManager::checkProductTagsLevel($product);
            ProductTagsLevelManager::priorityOneTags($product, $removeCategoryTags, 'product_tag');
            ProductTagsLevelManager::priorityFour($product, $tags['orphanTags']);
            ProductTagsLevelManager::priorityTwoTags($product);
            DB::commit();
            flash('Vehicle tags updated successfully.', 'success');
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

    /*
        return array of 2 item one for extra tags which are orphan tags and one for standard tags
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

    // get and store ignored tags of product
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
