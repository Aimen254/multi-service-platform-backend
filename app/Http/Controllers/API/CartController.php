<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Product;;

use App\Models\Setting;
use App\Models\CartItem;
use Modules\Retail\Entities\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Transformers\CartTransformer;
use App\Http\Requests\API\CartRequest;
use App\Transformers\CouponTransformer;
use App\Enums\Business\Settings\TaxType;
use App\Http\Requests\API\CouponRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user = request()->user();
            $cart = Cart::with('items')->whereHas('items.business', function ($query) {
                $query->where('status', 'active');
            })->where('user_id', $user->id)->first();
            $isDelete = false;
            if ($cart) {
                $isDelete = $cart->items()->whereNotNull('product_variant_id')->whereDoesntHave('productVariant')->delete();
                if (!request()->has('business_id') && !request()->input('business')) {
                    removeCoupon($cart);
                }
                if ($cart->coupon) {
                    $couponApplicable = \isCouponApplicable($cart, $cart->coupon, true);
                }
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'message' => isset($couponApplicable) && !$couponApplicable['status']
                        ? $couponApplicable['message'] : 'coupon is removed',
                    'isDelete' => $isDelete != 0 ? true : false,
                    'data' => (new CartTransformer)->transform($cart, [
                        'withMailing' => request()->withMailing == 'true' ? \true : \false
                    ]),
                ], JsonResponse::HTTP_OK);
            } else {
                return response()->json([
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                    'message' => 'Cart is empty.'
                ], JsonResponse::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CartRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = request()->user();
            $isDelete = 0;
            $cart = $user->cart ? $user->cart : $user->cart()->create();
            $existingCartItem = $cart->items()->first();
            $existingBusinessId = $existingCartItem ? $existingCartItem->business_id : null;
            $isDelete = $cart->items()->whereNotNull('product_variant_id')->whereDoesntHave('productVariant')->delete();
            $product = Product::where('uuid', $request->product_uuid)->first();
            if ($existingBusinessId && $existingBusinessId != $product->business_id) {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => "You can't add products from different stores. Please checkout or remove the items in your cart."
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            if ($product->stock_status == 'out_of_stock' || $product->stock == 0) {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Out of stock!'
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $alreadyExist = $cart->items()->where(function ($query) use ($product) {
                $query->where('product_id', $product->id);
                if (request()->input('variant_id')) {
                    $query->where('product_variant_id', request()->variant_id);
                }
            })->first();
            // if variant is selected
            if ($request->input('variant_id')) {
                $product = ProductVariant::findOrFail($request->input('variant_id'));
                if ($product->stock_status == 'out_of_stock' || $product->quantity == 0) {
                    return response()->json([
                        'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => 'Out of stock!'
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                $existingStock = $alreadyExist ? $alreadyExist->quantity : 0;
                if ($product->stock_status == 'in_stock' && $product->quantity > 0 && (($request->quantity + $existingStock) > $product->quantity)) {
                    if ($alreadyExist && $existingStock == $product->quantity) {
                        return response()->json([
                            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                            'message' => 'Out of stock!'
                        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                    }
                    return response()->json([
                        'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => 'Only ' . $product->quantity . ' items left in stock!'
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                $variants = $product->variants()->activeAndInStock()->get();
                if ($variants->count() > 0) {
                    return response()->json([
                        'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => 'Please select product variants!'
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                $existingStock = $alreadyExist ? $alreadyExist->quantity : 0;
                if ($product->stock >= 0 && (($request->quantity + $existingStock) > $product->stock)) {
                    if ($alreadyExist && $existingStock == $product->stock) {
                        return response()->json([
                            'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                            'message' => 'Out of stock!'
                        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                    }
                    $errorMessage = $product->stock == 0
                        ? 'Out of stock' :  'Only ' . $product->stock . ' items left in stock!';
                    return response()->json([
                        'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => $errorMessage
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            if ($alreadyExist) {
                $data = $this->cartItem($product, $request->quantity, request()->input('variant_id') ? true : false, $alreadyExist ? true : false, $alreadyExist);
                $alreadyExist->update($data);
            } else {
                $data = $this->cartItem($product, $request->quantity, request()->input('variant_id') ? true : false);
                $cart->items()->create($data);
            }

            $this->taxCalculation($cart->items);
            DB::commit();
            $cart = (new CartTransformer)->transform($this->updatedCart());
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Product is added to cart.',
                'data' => $cart,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Variant Not Found'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage() . '/ ' . $e->getFile() . '/ ' . $e->getLine()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CartRequest $request, $uuid)
    {
        try {
            DB::beginTransaction();
            $cartItem = CartItem::where('uuid', $request->item_uuid)->firstOrfail();
            if ($cartItem && !$cartItem->product) {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Product is removed from the system',
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $stock = $cartItem->productVariant
                ? $cartItem->productVariant->quantity : $cartItem->product->stock;
            if (($cartItem->product->status == 'out_of_stock') || ($cartItem->productVariant && $cartItem->productVariant->stock_status == 'out_of_stock')) {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Product is out of stock',
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (!empty($stock) && $stock >= 0 && $request->quantity > $stock) {
                if ($stock == 0) {
                    $errorMessage = 'Product is out of stock';
                } else {
                    $errorMessage = 'Only ' . $stock . ' items left in stock!';
                }
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $errorMessage
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $item = $cartItem->productVariant
                ? $this->cartItem($cartItem->productVariant, $request->quantity, true)
                : $this->cartItem($cartItem->product, $request->quantity);

            $cartItem->update($item);
            $this->taxCalculation($cartItem, true);
            $cart = $cartItem->cart;
            if ($cart->coupon) {
                $couponApplicable = \isCouponApplicable($cart, $cart->coupon, true);
            }
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => isset($couponApplicable) && !$couponApplicable['status']
                    ? $couponApplicable['message'] : 'Quantity updated successfully',
                'data' => (new CartTransformer)->transform($this->updatedCart())
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Cart item not found.'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CartRequest $request, $uuid)
    {
        try {
            DB::beginTransaction();
            $user = request()->user();
            $cart = $user->cart;
            if (request()->item_uuid) {
                CartItem::where('uuid', request()->item_uuid)->delete();
                if ($cart->items()->count() == 0) {
                    removeCoupon($cart);
                    $cart->delete();
                } else {
                    if ($cart->coupon) {
                        $couponApplicable = \isCouponApplicable($cart, $cart->coupon, true);
                    }
                }
            } else {
                $cart->delete();
            };
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Cart item removed successfully!',
                'data' => (new CartTransformer)->transform($cart, [
                    'withMailing' => request()->withMailing == 'true' ? \true : \false
                ])
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyCoupon(CouponRequest $request)
    {
        try {
            $cart = Cart::where('uuid', $request->uuid)->firstOrfail();
            
            $businsesId = $cart->items()->where('business_id', $request->business_id)->select('business_id')->first();
           

            if (!$businsesId) {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Coupon not applicable'
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $coupon = Coupon::where('code', $request->code)->where('model_id', $request->business_id)
                ->whereStatus('active')->where('end_date', '>=', Carbon::today())->firstOrFail();
                $response = isCouponApplicable($cart, $coupon);
            if (!$response['status']) {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $response['message']
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $cart->update(['coupon_id' => $coupon->id]);
            $coupon = (new CouponTransformer)->transform($coupon);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Coupon verified',
                'data' => $coupon,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'The coupon code you entered is invalid.'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function updateCartQuantity($item, $product, $quantity)
    {
        return $item->update(
            $this->cartItem($product, $quantity)
        );
    }

    private function updatedCart()
    {
        $user = User::with('cart')->findOrFail(request()->user()->id);
        return $user->cart;
    }

    // tax calculation
    private function taxCalculation($item, $update = false)
    {
        if (!$update) {
            $item = $item->first();
        }
        $product = $item->product;
        $businsessTax = $product->business->settings()->where('key', 'tax_percentage')->first();
        $taxApplicable = $product->business->settings()->where('key', 'tax_apply')->first();
        $taxModel = Setting::where('key', 'tax_model')->first();
        $taxModelValue = TaxType::coerce(str_replace(' ', '', ucwords($taxModel->value)))->value;
        if ($taxApplicable->value) {
            if ($taxModelValue == TaxType::TaxNotIncludedOnPrice) {
                if ($product->tax_percentage) {
                    $this->taxCalculater($item, $product->tax_percentage);
                } else {
                    $this->taxCalculater($item, $businsessTax->value);
                }
            }
        }
    }

    function getTopParent($category)
    {
        $parent = $category;
        if ($category->parent_id) {
            $parent = $category->parent;
            self::getTopParent($parent);
        }
        return $parent;
    }

    function taxCalculater($item, $tax_percentage)
    {
        $tax = ($item->total * $tax_percentage) / 100;
        $total = $item->total + $tax;
        $item->update([
            'tax' => $tax,
            'total' => $total
        ]);
    }

    private function cartItem($product, $quantity = null, $hasVariant = false, $update = false, $existingProduct = NULL)
    {

        $productPrice=null;
        $existingQuantity = $update ? $existingProduct->quantity : 0;
        $date = Carbon::now()->format('Y-m-d');
        if ($hasVariant) {

            if ($product?->discount_price && $product?->product?->discount_end_date?->gte($date) && $product?->product?->discount_start_date?->lte($date)) {
                $unitPrice = $product->discount_price;
                $actualPrice = $product->discount_price * ($quantity + $existingQuantity);
                $totalPrice = $product->discount_price * ($quantity + $existingQuantity);
                $productPrice = $product->price * $quantity;
            }
        } else {

            if ($product?->discount_price && $product?->discount_end_date?->gte($date) && $product?->discount_start_date?->lte($date)) {
                $unitPrice = $product->discount_price;
                $actualPrice = $product->discount_price * ($quantity + $existingQuantity);
                $totalPrice = $product->discount_price * ($quantity + $existingQuantity);
                $productPrice = $product->price * ($quantity + $existingQuantity);
            }
        }

        $data = [
            'product_id' => $hasVariant ? $product->product_id : $product->id,
            'quantity' => ($quantity + $existingQuantity),
            'product_variant_id' => $hasVariant ? $product->id : NULL,
            'unit_price' => isset($unitPrice) ? $unitPrice : $product->price,
            'actual_price' => isset($actualPrice)
                ? $actualPrice : $product->price * ($quantity + $existingQuantity),
            'total' => isset($totalPrice)
                ? $totalPrice : $product->price * ($quantity + $existingQuantity),
            'business_id' => $hasVariant ? $product->product->business_id : $product->business_id,
            'product_price' => $productPrice > 0 ? $productPrice : (isset($actualPrice)
            ? $actualPrice : $product->price * ($quantity + $existingQuantity))
        ];
        return $data;
    }
   
}
