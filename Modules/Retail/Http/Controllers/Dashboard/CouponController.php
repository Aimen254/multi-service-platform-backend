<?php

namespace Modules\Retail\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use App\Models\Business;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Http\Requests\CouponRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class CouponController extends Controller
{
    protected $business;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $business = getBusinessDetails(Route::current()->parameters['business_uuid'], 'retail');
        $this->business = $business;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $businessUuid)
    {
        $limit = \config()->get('settings.pagination_limit');
        $business = Business::with(['settings', 'banner', 'logo', 'thumbnail'])
            ->where('uuid', $businessUuid)->first();

        $delivery_zone = $business->deliveryZone;

        $coupons = Coupon::where(function ($query) {
            if (request()->keyword) {
                $keyword = request()->keyword;
                $query->where('code', 'like', '%' . $keyword . '%');
            }
        })->where('model_type', 'App\Models\Business')
            ->where('model_id', $this->business->id)->orderBy('id', 'desc')->paginate($limit);
        return Inertia::render('Retail::Business/Coupons/Index', [
            'business' => $business,
            'couponsList' => $coupons,
            'searchedKeyword' => request()->keyword,
            'delivery_zone' => $delivery_zone,
        ]);
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
     * @param CouponRequest $request
     * @return Renderable
     */
    public function store(CouponRequest $request)
    {
        try {
            $this->business->coupons()->create([
                'code' => $request->code,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'coupon_type' => $request->coupon_type,
                'created_by' => auth()->user()->id,
            ]);
            flash('Coupon added successfully', 'success');
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
     * @param CouponRequest $request
     * @param int $id
     * @return Renderable
     */
    public function update(CouponRequest $request, $moduleId, $businessUuid, $id)
    {
        try {
            $coupon = Coupon::findOrfail($id);
            $coupon->update([
                'code' => $request->code,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'coupon_type' => $request->coupon_type,
            ]);
            flash('Coupon Updated successfully', 'success');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $businessUuid, $id,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $coupon = Coupon::findOrFail($id);
            foreach ($coupon->carts as $cart) {
                $cart->update([
                    'coupon_id'  => null
                ]);
            }
            $coupon->delete();
            flash('Coupon Deleted!', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('retail.dashboard.business.coupons.index', [$moduleId,$businessUuid, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Coupon', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function changeStatus($moduleId, $business_id, $id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->statusChanger()->save();
            flash('Coupon status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Coupon', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
