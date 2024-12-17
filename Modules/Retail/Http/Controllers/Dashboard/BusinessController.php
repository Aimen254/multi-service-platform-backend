<?php

namespace Modules\Retail\Http\Controllers\Dashboard;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\BusinessStreetAddress;
use Illuminate\Contracts\Support\Renderable;
use App\Traits\BusinessProducts;
use Modules\Retail\Http\Requests\BusinessRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class BusinessController extends Controller
{
    use StripeSubscription;
    public StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
        $this->middleware('can:edit_business')->only('edit');
        $this->middleware('can:add_business')->only('create');
        $this->middleware('can:delete_business')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
        $sortByOrder = request()->form && isset(request()->form['sortByOrder']) ? request()->form['sortByOrder'] : 'desc';
        $user = auth()->user();
        $limit = \config()->get('settings.pagination_limit');
        $moduleId = $request->route('moduleId');

        $businesses = Business::with(['logo', 'thumbnail', 'banner', 'businessOwner', 'reviews', 'orders', 'standardTags' => function ($query) {
            $query->select(['id', 'name as text', 'type'])->where('type', 'module');
        }])->when($user->user_type === 'business_owner' || $user->user_type === 'newspaper' || $user->user_type === 'customer', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->where(function ($query) use ($moduleId) {
            if ($moduleId && $moduleId != 'settings') {
                $query->whereHas('standardTags', function ($query) use ($moduleId) {
                    $query->where('id', $moduleId);
                });
            }
            if (request()->form && isset(request()->form['status'])) {
                $query->where('status', request()->form['status']);
            }
            if (request()->keyword) {
                $keyword = request()->keyword;
                $query->where('name', 'like', '%' . $keyword . '%');
            }
            if (request()->form && isset(request()->form['reviewRating'])) {
                $rating = request()->form['reviewRating'];
                $query->whereHas('reviews', function ($subQuery) use ($rating) {
                    $subQuery->havingRaw('round(avg(rating)) = ?', ["{$rating}"]);
                });
            }
        })->withCount(['reviews as reviews_avg' => function ($query) {
            $query->select(DB::raw('avg(rating)'));
        }])->withCount('orders')
            ->orderBy('orders_count', $sortByOrder)
            ->orderBy('id', $orderBy)
            ->paginate($limit);

        $businessOwners = User::select(['id', DB::raw('CONCAT(id, \' - \', last_name) as text')])
            ->where('user_type', 'business_owner')->orWhere('user_type', 'newspaper')->orWhere('user_type', 'customer')->get();
        $moduleTags = StandardTag::select(['id', 'name as text', 'type'])->where('type', 'module')->active()->get();

        return Inertia::render('Retail::Business/Index', [
            'businessList' => $businesses,
            'searchedKeyword' => request()->keyword,
            'businessOwners' => $businessOwners,
            'moduleTags' => $moduleTags,
            'orderBy' => request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc',
            'status' => request()->form && isset(request()->form['status']) ? request()->form['status'] : null,
            'rating' => request()->form && isset(request()->form['reviewRating']) ? request()->form['reviewRating'] : null,
            'noOfOrders' => request()->form && isset(request()->form['sortByOrder']) ? request()->form['sortByOrder'] : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $businessOwners = User::select(['id', DB::raw('CONCAT(id, \' - \', last_name) as text')])
            ->where('user_type', 'business_owner')->where('status', 'active')->get();
            return Inertia::render('Retail::Business/Create', [
                'businessOwners' => $businessOwners,
                'is_admin' => request()->user()->user_type == 'admin' ? true : false,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request,$moduleId)
    {
        try {
            DB::beginTransaction();
            $business = Business::create($request->all());
            $business->standardTags()->syncWithoutDetaching($moduleId);
            DB::commit();
            flash('Business Added Sucessfully!', 'success');
            return \redirect()->route('retail.dashboard.businesses.index', $moduleId);
        } catch (\Exception $e) {
            Db::rollBack();
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
    public function edit($moduleId, $businessUuid)
    {
        try {
            $mediaLogoSizes = \config()->get('retail.media.logo');
            $mediaThumbnailSizes = \config()->get('retail.media.thumbnail');
            $mediaBannerSizes = \config()->get('retail.media.banner');
            $mediaSecondaryBannerSizes = \config()->get('image.media.secondaryBanner'); 
            // Ensure this returns a valid value
            $businessOwners = User::select([
                'id',
                DB::raw('CONCAT(id, \' - \', last_name) as text')
            ])->where('user_type', 'business_owner')->orWhere('user_type', 'newspaper')->orWhere('user_type', 'customer')->get();
    
            $business = Business::with(['logo', 'thumbnail', 'banner', 'secondaryImages', 'secondaryBanner'])
                ->orderBy('created_at', 'desc')
                ->where('uuid', $businessUuid)
                ->first();
            return Inertia::render('Retail::Business/Edit', [
                'business' => $business,
                'businessOwners' => $businessOwners,
                'mediaLogoSizes' => $mediaLogoSizes,
                'mediaThumbnailSizes' => $mediaThumbnailSizes,
                'mediaBannerSizes' => $mediaBannerSizes,
                'mediaSecondaryBannerSizes' => $mediaSecondaryBannerSizes,
                'token' => csrf_token()
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }
    

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(BusinessRequest $request, $moduleId, $businessUuid)
    {
        try {
            DB::beginTransaction();
            $business = Business::where('uuid', $businessUuid)->first();
            $business->update($request->all());
            BusinessStreetAddress::streetAddress($business);
            DB::commit();
            flash('Business Updated Successfully.', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this business', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $businessUuid,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $business = Business::whereUuid($businessUuid)->first();
            if (BusinessProducts::getProductCount($business)) {
                $business->delete();
                flash('Dealership deleted succesfully', 'success');
                if ($currentCount > 1) {
                    return redirect()->back();
                } else {
                    $previousPage = max(1, $currentPage - 1);
                    return Redirect::route('retail.dashboard.businesses.index', [$moduleId,  $businessUuid, 'page' => $previousPage]);
                }
            } else {
                flash("We're unable to delete this dealership, as it has products associated with it.", 'danger');
            }
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Dealership', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function changeStatus($moduleId, $id)
    {
        try {
            DB::beginTransaction();
            $business = Business::findOrFail($id);
            // checking if business owner has the permission to activate this business or not
            if ($business->status == 'inactive') {
                $permission = $this->checkActiveBusinesses($business, 'check_active_businesses', $moduleId);
                if (!$permission) {
                    DB::rollBack();
                    if (auth()->user()->user_type == 'admin') {
                        flash('Business can not be activated due to subscription limitations.', 'danger');
                        return redirect()->back();
                    }
                    flash('You have activated maximum no of businesses according to your subscription plan.', 'danger', 'dashboard.subscription.subscribe.index');
                    return redirect()->back();
                }
            }
            if (!(auth()->user()->user_type == 'admin' || \auth()->user()->user_type == 'newspaper') && ($business->status == 'inactive') && ($business->status_updated_by && $business->status_updated_by != auth()->user()->id)) {
                $message = "Business is deactivated by administrators.";
                $status = "danger";
                DB::rollBack();
            }
            //            elseif (!$business->businessOwner->completed_stripe_onboarding && $business->status == 'inactive') {
            //                $message = "Business owner's stripe on-boarding process in incomplete. You can not active this business.";
            //                $status = "danger";
            //                DB::rollBack();
            //            }
            else {
                $business->status = $business->status == 'active' ? 'inactive' : 'active';
                $business->status_updated_by = auth()->user()->id;
                $business->save();
                DB::commit();
                $message = "Business status changed succesfully.";
                $status = "success";
            }
            flash($message, $status);
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
