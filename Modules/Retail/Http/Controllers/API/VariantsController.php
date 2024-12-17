<?php

namespace Modules\Retail\Http\Controllers\API;

use App\Transformers\ProductVariantTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Models\Product;
use Modules\Retail\Entities\ProductVariant;
use Modules\Retail\Http\Requests\ProductVariantRequest;
use Modules\Retail\Http\Requests\ProductVariantUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VariantsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($module, $uuid)
    {
        try {
            $limit = request()->limit;
            $product = Product::whereUuid($uuid)->firstOrFail();
            $productImageSizes = \config()->get('retail.media.product');
            $variants = ProductVariant::with(['color', 'size', 'image'])
                ->where('product_id', $product->id)
                ->where(function ($query) {
                    if (request()->input('keyword')) {
                        $keyword = request()->keyword;
                        $query->where('title', 'like', '%' . $keyword . '%')
                            ->orWhere('price', 'like', '%' . $keyword . '%')
                            ->orWhere('quantity', 'like', '%' . $keyword . '%')
                            ->orWhereHas('color', function ($subQuery) use ($keyword) {
                                $subQuery->whereRaw('title like ?', ["%{$keyword}%"]);
                            })
                            ->orWhereHas('size', function ($subQuery) use ($keyword) {
                                $subQuery->whereRaw('title like ?', ["%{$keyword}%"]);
                            });
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate($limit);
            $paginate = apiPagination($variants, $limit);
            $business = $product->business;
            $colors = $business->colors()->select(['id', 'title as text'])->get();
            $sizes = $business->sizes()->select(['id', 'title as text'])->get();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => [
                    'product' => $product,
                    'variantsList' => (new ProductVariantTransformer)->transformCollection($variants),
                    'productImageSizes' => $productImageSizes,
                    'sizes' => $sizes,
                    'colors' => $colors,
                    'meta' => $paginate,
                ],
            ], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductVariantRequest $request, $module, $uuid): JsonResponse
    {
        try {
            $product = Product::whereUuid($uuid)->firstOrFail();
            $variantExist = $product->variants()
                ->where('size_id', $request->size)
                ->where('color_id', $request->color)
                ->exists();

            if ($variantExist) {
                return response()->json([
                    'status' => JsonResponse::HTTP_CONFLICT,
                    'message' => 'Variant Already Present.',
                ], JsonResponse::HTTP_CONFLICT); 
            }

            $variant = ProductVariant::create([
                'title'=> $request->title ?? '',
                'product_id' => $product->id ?? '',
                'color_id'=> $request->color ?? null,
                'size_id' => $request->size ?? null,
                'price' => $request->price ?? '',
                'quantity' => $request->quantity ?? null,
            ]);

            $this->uploadImage($request, $variant);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Variant Created Successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show()
    {
       //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
       //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductVariantRequest $request, $module, $uuid, $id ): JsonResponse
    {
        try {
            $product = Product::whereUuid($uuid)->firstOrFail();
            $variantExist = $product->variants()
                ->where('size_id', $request->size)
                ->where('color_id', $request->color)->where('id','!=', $id)
                ->exists();

            if ($variantExist) {
                return response()->json([
                    'status' => JsonResponse::HTTP_CONFLICT,
                    'message' => 'Variant Already Present.',
                ], JsonResponse::HTTP_CONFLICT); 
            }

            $variant = ProductVariant::findOrFail($id);

            $variant->update([
                'title'=> $request->title ?? '',
                'color_id'=> $request->color ?? null,
                'size_id' => $request->size ?? null,
                'price' => $request->price ?? '',
                'quantity' => $request->quantity ?? null,
            ]);

            $this->uploadImage($request, $variant);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Variant Updated Successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($module, $uuid, $id): JsonResponse
    {
        try {
            $variant = ProductVariant::findOrFail($id);
            $variant->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Product variant deleted successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function uploadImage($request, $variant)
    {
        if ($request->hasFile('image')) {
            $product = config()->get('retail.media.product');
            $width = $product['width'];
            $height = $product['height'];
            $extension = request()->image->extension();
            $filePath = saveResizeImage(request()->image, "products", $width, $height,  $extension);
            if ($variant->image) {
                \deleteFile($variant->image->path);
                $variant->image()->update([
                    'path' => $filePath,
                    'size' => request()->file('image')->getSize(),
                    'mime_type' => request()->file('image')->extension(),
                    'type' => 'image'
                ]);
            } else {
                $variant->image()->create([
                    'path' => $filePath,
                    'size' => request()->file('image')->getSize(),
                    'mime_type' => request()->file('image')->extension(),
                    'type' => 'image'
                ]);
            }
        }
        return true;
    }

    public function updateStatus($moduleId, $id)
    {
        try {
            $variant = ProductVariant::whereId($id)->firstOrFail();
            $previousStatus = $variant->status;
            if (request()->input('status')) {
                $variant->status = request()->input('status');
            } else {
                if ($variant->status == 'active') {
                    $variant->status = 'inactive';
                } else {
                    $variant->status = 'active';
                }
            }
            $variant->previous_status = $previousStatus;
            $variant->saveQuietly();

            return response()->json([
                'message' => "Status updated successfully!"
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
