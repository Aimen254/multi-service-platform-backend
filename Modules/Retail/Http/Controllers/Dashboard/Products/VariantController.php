<?php

namespace Modules\Retail\Http\Controllers\Dashboard\Products;

use Exception;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\QueryException;
use Modules\Retail\Entities\ProductVariant;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Http\Requests\VariantRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;
use Modules\Retail\Http\Requests\BulkImageUploadRequest;

class VariantController extends Controller
{
    protected $product;
    private $newVariantCount = 0;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->product = Product::whereUuid(Route::current()->parameters['uuid'])->firstOrFail();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($moduleId, $uuid)
    {
        try {
            $limit = \config()->get('settings.pagination_limit');
            $productImageSizes = \config()->get('retail.media.product');
            $variants = ProductVariant::with(['color', 'size', 'image'])
                ->where('product_id', $this->product->id)
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
            $business = $this->product->business;
            $colors = $business->colors()->select(['id', 'title as text'])->get();
            $sizes = $business->sizes()->select(['id', 'title as text'])->get();
            return Inertia::render('Retail::Products/Variants/Index', [
                'product' => $this->product,
                'variantsList' => $variants,
                'productImageSizes' => $productImageSizes,
                'sizes' => $sizes,
                'colors' => $colors,
                'searchedKeyword' => request()->keyword,
            ]);
        } catch (Exception $e) {
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
        return view('retail::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\VariantRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VariantRequest $request, $uuid)
    {
        try {
            $variantExist = $this->product->variants()->where('size_id', $request->size_id)->where('custom_size', $request->custom_size)->where('color_id', $request->color_id)->where('custom_color', $request->custom_color)->first();
            if (!$variantExist) {
                $request->merge([
                    'product_id' => $this->product->id,
                ]);
                $variant = ProductVariant::create($request->all());
                $this->uploadImage($request, $variant);
                flash('Variant Created Successfully.', 'success');
                return \redirect()->back();
            } else {
                $msg = 'Variant Already Present.';
                flash($msg, 'danger');
                return \redirect()->back();
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this variant.', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('retail::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('retail::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VariantRequest $request, $moduleId, $uuid, $id)
    {
        try {
            $variantExist = $this->product->variants()->where('size_id', $request->size_id)->where('custom_size', $request->custom_size)->where('color_id', $request->color_id)->where('custom_color', $request->custom_color)->whereNotIn('id', [$id])->first();
            if (!$variantExist) {
                $variant = ProductVariant::findOrFail($id);
                $variant->update($request->all());
                $this->uploadImage($request, $variant);
                flash('Variant updated successfully.', 'success');
                return \redirect()->back();
            } else {
                flash('Variant Already Present.', 'danger');
                return \redirect()->back();
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this variant.', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($moduleId, $productUuid, $id,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $variant = ProductVariant::findOrFail($id);
            $variant->delete();
            flash('Product Variant deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('retail.dashboard.product.variants.index', [$moduleId,$productUuid, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product variant', 'danger');
            return redirect()->back();
        } catch (QueryException $e) {
            flash('Cannot delete product variant because they have associations', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    private function uploadImage($request, $variant)
    {
        if (request()->image) {
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


    public function changeStatus($moduleId, $productUuid, $id)
    {
        try {
            $variant = ProductVariant::findOrFail($id);
            $variant->statusChanger()->save();
            flash('Product variant status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function updateQuantity($moduleId, $productUuid, $id)
    {
        try {
            $qty = request()->variantQuantity;
            if ($qty >= 0) {
                $variant = ProductVariant::with('product')->findOrFail($id);
                if ($variant->stock_status == 'out_of_stock' || ($variant->product && $variant->product->stock_status == 'out_of_stock')) {
                    return \response()->json([
                        'status' => 'error',
                        'message' => 'You Cannot Update Quantity, Product stock status is out of stock'
                    ], JsonResponse::HTTP_FORBIDDEN);
                }
                $variant->update([
                    'quantity' => $qty,
                ]);
                return \response()->json([
                    'status' => 'success',
                    'message' => 'Quantity updated!'
                ], JsonResponse::HTTP_OK);
            } else {
                return \response()->json([
                    'status' => 'error',
                    'message' => 'Product Quantity Must Be Equal Or Greater Than 0'
                ], JsonResponse::HTTP_FORBIDDEN);
            }
        } catch (ModelNotFoundException $e) {
            return \response()->json([
                'status' => 'error',
                'message' => 'Unable to find this product'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return \response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * generate specified resource variants.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function autoGenerateVariants(Request $request, $moduleId, $uuid)
    {
        try {
            DB::beginTransaction();
            $variants = [];
            $sizes = collect(request()->sizes)->pluck('id')->toArray();
            $colors = collect(request()->colors)->pluck('id')->toArray();
            if (empty($sizes) && empty($colors)) {
                $business = $this->product->business;
                $sizes = $business->sizes;
                $colors = $business->colors;
            }

            if (!empty($sizes) || !empty($colors)) {
                $variants = $this->generateVariants($sizes, $colors);
                $this->product->variants()->createMany($variants);
                flash($this->newVariantCount . ' Variants generated successfully.', 'success');
            } else {
                flash(' No Variant Found.', 'error');
            }

            DB::commit();
            return redirect()->route('retail.dashboard.product.variants.index', [$moduleId, $this->product->uuid]);
        } catch (Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }
    private function generateVariants($sizes, $colors)
    {
        $variants = [];
        if (empty($colors)) {
            $variants = $this->generatePossileVariants($sizes, 'size');
        } else if (empty($sizes)) {
            $variants = $this->generatePossileVariants($colors, 'color');
        } else {
            foreach ($sizes as $key => $size) {
                $sizeVariants = $this->generatePossileVariants($colors, 'both', $size);
                \array_push($variants, $sizeVariants);
            }
            $variants = Arr::collapse($variants);
        }
        return $variants;
    }


    public function generatePossileVariants($variants, $type, $secondaryVariant = null)
    {
        $newVariants = [];
        foreach ($variants as $key => $variant) {
            if ($type == 'both') {
                $colorId = empty(request()->sizes) && empty(request()->colors) ? $variant->id : $variant;
                $sizeId = empty(request()->sizes) && empty(request()->colors) ? $secondaryVariant->id : $secondaryVariant;
            } else {
                $colorId = $type == 'color' ? $variant : null;
                $sizeId = $type == 'size' ? $variant : null;
            }
            $variantExist = $this->product->variants()->where('color_id', $colorId)->where('size_id', $sizeId)->where('custom_size', null)->where('custom_color', null)->first();
            if (!$variantExist) {
                $this->newVariantCount += 1;
                $newVariants[] = [
                    'color_id' => $colorId,
                    'size_id' => $sizeId,
                    'title' => $this->product->name,
                    'price' => $this->product->price,
                    'product_id' => $this->product->id,
                    'created_at' => Carbon::now()
                ];
            }
        }
        return $newVariants;
    }


    public function deleteAll($moduleId, $productUuid, $ids)
    {
        $ids = json_decode($ids);
        try {
            ProductVariant::whereIn('id', $ids)->delete();
            flash('Product Variant deleted succesfully', 'success');
            return redirect()->route('retail.dashboard.product.variants.index', [$moduleId, $this->product->uuid]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product variant', 'danger');
            return redirect()->back();
        } catch (QueryException $e) {
            flash('Cannot delete product variant because they have associations', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }


    public function bulkImageUpload(BulkImageUploadRequest $request, $moduleId, $productUuid)
    {
        try {
            $variants = $request->input('variants');
            foreach ($variants as $key => $variant) {
                $productVariant = ProductVariant::findOrFail($variant);
                $this->uploadImage($request, $productVariant);
            }
            flash('Image Uploaded Successfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product variant', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
