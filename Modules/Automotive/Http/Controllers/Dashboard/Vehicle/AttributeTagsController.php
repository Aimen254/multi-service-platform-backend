<?php

namespace Modules\Automotive\Http\Controllers\Dashboard\Vehicle;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttributeTagsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {
        try {
            $product = Product::whereUuid($uuid)->with('tags', function ($query) {
                $query->asTag()->active()->where('is_show', 1)->with('standardTags');
            })->firstOrFail();
            $standardTags = StandardTag::whereType('attribute')->asTag()->with('attribute')->active()->get();
            $allAttributeTags = Arr::collapse([$standardTags]);
            $assignedStandards = $product->standardTags()->with(['tags_' => function ($query) use ($product) {
                $query->whereHas('products', function ($subQuery) use ($product) {
                    $subQuery->where('id', $product->id);
                });
            }])->withPivot(['attribute_id'])->with('attribute')->asTag()->active()->whereType('attribute')->get();

            $assignedTags = $assignedStandards;
            return Inertia::render('Automotive::Vehicles/Settings/AttributeTags', [
                'product' => $product,
                'assignedTag' => $assignedTags,
                // 'allAttributeTags' => $allAttributeTags,
                'attributes' => Attribute::with(['standardTags' => function ($query) {
                }])->active()->whereHas('moduleTags', function ($query) use ($moduleId) {
                    $query->where('id', $moduleId);
                })->get(),
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
            DB::beginTransaction();
            $product = Product::whereUuid($uuid)->firstOrFail();
            // $standardTags = Arr::collapse([$product->standardTags()->where('type', '!=', 'attribute')->pluck('id')->toArray(), collect(json_decode(request()->input('tags')))->pluck('id')->toArray()]);
            // $standardTags = collect(json_decode(request()->input('tags')))->toArray();
            // foreach ($standardTags as $standardTag) {
            //     if ($standardTag->pivot) {
            //         dd('here');
            //         $attribute = Attribute::where('id', $standardTag->pivot->attribute_id)->firstOrFail();
            //         if ($attribute && ($attribute->slug == 'interior-color' || $attribute->slug == 'exterior-color' || $attribute->slug == 'color')) {
            //             $isExist = $product->standardTags()->withPivot(['attribute_id'])
            //                 ->wherePivot('attribute_id', $attribute->id)
            //                 ->wherePivot('standard_tag_id', $standardTag->id)
            //                 ->where('product_id', $product->id)
            //                 ->first();
            //             if (!$isExist) {
            //                 $product->standardTags()->attach($standardTag->id, [
            //                     'attribute_id' => $attribute->id,
            //                     'product_id' => $product->id
            //                 ]);
            //             } else {
            //                 $product->standardTags()->syncWithOutDetaching($standardTag->id);
            //             }
            //         } else {
            //             $product->standardTags()->syncWithOutDetaching($standardTag->id);
            //         }
            //     } else {
            //         dd($standardTag);
            //     }
            // }
            $standardTags = collect(json_decode(request()->input('tags')))->toArray();
            foreach ($standardTags as $standardTag) {
                if (isset($standardTag->pivot) && $standardTag->pivot->attribute_id) {
                    $attribute = Attribute::where('id', $standardTag->pivot->attribute_id)->firstOrFail();
                    if ($attribute && ($attribute->slug == 'interior-color' || $attribute->slug == 'exterior-color')) {
                        $isExist = $product->standardTags()
                            ->withPivot(['attribute_id'])
                            ->wherePivot('attribute_id', $attribute->id)
                            ->wherePivot('standard_tag_id', $standardTag->id)
                            ->where('product_id', $product->id)
                            ->first();

                        if (!$isExist) {
                            $product->standardTags()->attach($standardTag->id, [
                                'attribute_id' => $attribute->id,
                                'product_id' => $product->id
                            ]);
                        }
                    } else {
                        $product->standardTags()->syncWithoutDetaching($standardTag->id);
                    }
                } else {
                    if ($standardTag->attribute[0]->slug == 'interior-color' || $standardTag->attribute[0]->slug == 'exterior-color') {
                        $isExist = $product->standardTags()
                            ->withPivot(['attribute_id'])
                            ->wherePivot('attribute_id', $standardTag->attribute[0]->id)
                            ->wherePivot('standard_tag_id', $standardTag->id)
                            ->where('product_id', $product->id)
                            ->first();

                        if (!$isExist) {
                            $product->standardTags()->attach($standardTag->id, [
                                'attribute_id' => $standardTag->attribute[0]->id,
                                'product_id' => $product->id
                            ]);
                        }
                    } else {
                        $product->standardTags()->syncWithoutDetaching($standardTag->id);
                    }
                }
            }

            // removed tags
            $removedTags = collect(json_decode(request()->input('removedTags')))->toArray();
            foreach ($removedTags as $removedTag) {
                if ($removedTag->pivot->attribute_id) {
                    $attribute = Attribute::where('id', $removedTag->pivot->attribute_id)->firstOrFail();
                    if ($attribute && ($attribute->slug == 'interior-color' || $attribute->slug == 'exterior-color')) {
                        $product->standardTags()
                            ->wherePivot('attribute_id', $attribute->id)
                            ->wherePivot('standard_tag_id', $removedTag->id)
                            ->where('product_id', $product->id)
                            ->detach();
                    } else {
                        $product->standardTags()->detach($removedTag->id);
                    }
                } else {
                    $product->standardTags()->detach($removedTag->id);
                }
            }
            $removingAttributes = collect(json_decode(request()->input('removedTags')))->pluck('id')->toArray();
            // $product->standardTags()->sync($standardTags);
            if (count($removingAttributes) > 0) {
                ProductTagsLevelManager::priorityThree($product, null, $removingAttributes, 'attribute');
            }
            ProductTagsLevelManager::priorityThree($product);
            DB::commit();
            flash('Vehicle attribute tags updated successfully.', 'success');
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

    // search attribute tags

    public function searchTags(Request $request)
    {
        $standardTags = StandardTag::whereType('attribute')->asTag()->with(['attribute' =>  function ($query) {
            $query->where('id', request()->attribute_id);
        }])->whereHas('attribute', function ($query) {
            $query->where('id', request()->attribute_id);
        })->active()->get();
        return $standardTags;
    }
}
