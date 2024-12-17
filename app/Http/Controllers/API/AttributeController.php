<?php

namespace App\Http\Controllers\API;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\ProductTagsLevelManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class AttributeController extends Controller
{
    public function attributes($moduleId)
    {
        $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first();
        $attributes = Attribute::where('slug', '<>', 'year')->with(['standardTags' => function ($query) {
            $query->where('type', 'attribute');
        }])->active()->whereHas('moduleTags', function ($query) use ($module) {
            $query->where('id', $module?->id);
        })->get();

        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => $attributes,
        ], JsonResponse::HTTP_OK);
    }

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
            $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'product' => $product,
                'assignedTag' => $assignedTags,
                'attributes' => Attribute::where('slug', '<>', 'year')->with(['standardTags' => function ($query) {
                }])->active()->whereHas('moduleTags', function ($query) use ($module) {
                    $query->where('id', $module?->id);
                })->get(),
                // 'data' => $attributes,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function assignTags($moduleId, $uuid)
    {
        try {
            DB::beginTransaction();
            $product = Product::whereUuid($uuid)->firstOrFail();
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
            if (count($removingAttributes) > 0) {
                ProductTagsLevelManager::priorityTwoTags($product, null, $removingAttributes, 'attribute');
            }
            ProductTagsLevelManager::priorityTwoTags($product);
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Tags Added Successfully',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function searchTags(Request $request)
    {
        try {
            $standardTags = StandardTag::whereType('attribute')->asTag()->with(['attribute' =>  function ($query) {
                $query->where('id', request()->attribute_id);
            }])->whereHas('attribute', function ($query) {
                $query->where('id', request()->attribute_id);
            })->active()->get();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'standardTags' => $standardTags
            ], JsonResponse::HTTP_OK);
            // return $standardTags;
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
