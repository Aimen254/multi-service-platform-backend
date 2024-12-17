<?php

namespace Modules\Retail\Http\Controllers\Dashboard\Products;

use Inertia\Inertia;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Models\Business;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Http\Requests\ProductCouponRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;
use Modules\Retail\Http\Requests\ProductDiscountRequest;

class DiscountController extends Controller
{
    protected $product;

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
     * @return Renderable
     */
    public function index($uuid)
    {
        try {
            $limit = \config()->get('settings.pagination_limit');
            $coupons = $this->product->coupons()->paginate($limit);
            $business = Business::findOrFail($this->product->business_id);
            $codesList = $business->coupons()
                ->select(['id', DB::raw('CONCAT(code, \' - \', discount_value) as text'), 'discount_type', 'discount_value', 'status', 'end_date'])
                ->where('coupon_type', 'product')->whereDoesntHave('products', function ($q) {
                    $q->where('product_id', $this->product->id);
                })->orderBy('id', 'desc')
                ->get();
            return Inertia::render('Retail::Products/Coupons/Index', [
                'product' => $this->product,
                'couponsList' => $coupons,
                'codesList' => $codesList
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
        return view('retail::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductCouponRequest $request, $uuid)
    {

        try {
            $business = getBusinessDetails($request->business_uuid, 'retail');
            $this->product->coupons()->syncWithoutDetaching($request->coupon_id);
            $business->coupons()->findOrFail($request->coupon_id)->update([
                'created_by' => auth()->user()->id,
            ]);

            flash('Coupon assinged successfully.', 'success');
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($moduleId, $uuid, $id,Request $request)
    {

        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $this->product->coupons()->detach($id);
            flash('Coupon deleted successfully.', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('etail.dashboard.product.coupons.index', [$moduleId,$uuid, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this coupon', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }
    /**
     * change status of specified resource from storage.
     *
     * @param  int $uuid, $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($moduleId, $uuid, $id)
    {
        try {
            $product = Coupon::findOrfail($id)->products()->wherePivot('product_id', $this->product->id)->firstOrfail();
            $coupon = $product->coupons()->first();
            if ($coupon->status == 'inactive') {
                flash('Status not changed! The main coupon status is inactive', 'danger');
                return redirect()->back();
            }
            $status = $product->pivot->status == 'active' ? 'inactive' : 'active';
            $attribute = ['status' => $status, 'previous_status' => $status];
            $this->product->coupons()->updateExistingPivot($id, $attribute);
            flash('Coupon status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this coupon', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function productDiscount($uuid)
    {
        try {
            return Inertia::render('Retail::Products/Discounts/Index', [
                'product' => $this->product,
                'type' => 'discount',
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function discountUpdate(ProductDiscountRequest $request, $moduleId, $uuid)
    {
        try {
            DB::beginTransaction();
            $discount = null;
            if ($request->discount_type == 'percentage') {
                $discount = ($this->product->price * $request->discount_value) / 100;
                $discount = $this->product->price - $discount;
            } else {
                $discount = $this->product->price - $request->discount_value;
            }
            $request->merge([
                'discount_price' => numberFormat($discount)
            ]);
            $this->product->update($request->all());
            flash('Product Discount information updated successfully.', 'success');
            DB::commit();
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product.', 'danger');
            DB::rollBack();
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            DB::rollBack();
            return \redirect()->back();
        }
    }
}
