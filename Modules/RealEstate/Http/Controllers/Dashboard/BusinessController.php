<?php

namespace Modules\RealEstate\Http\Controllers\Dashboard;

use App\Enums\Business\BusinessStatus;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Business;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Traits\BusinessProducts;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\RealEstate\Http\Requests\BusinessRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\RealEstate\Http\Requests\BusinessRejectRequest;
use App\Traits\ModuleSessionManager;
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
    public function index($moduleId)
    {
        $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
        $user = auth()->user();
        $limit = \config()->get('settings.pagination_limit');
        $business = Business::with(['businessOwner', 'reviews', 'logo'])->when($user->user_type === 'business_owner' ||  $user->user_type === 'customer', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->where(function ($query) use ($moduleId) {
            if ($moduleId != 'settings') {
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
        }])->orderBy('id', $orderBy)->paginate($limit);

        $businessOwners = User::select(['id', DB::raw('CONCAT(id, \' - \', last_name) as text')])
            ->where('user_type', 'business_owner')->orWhere('user_type', 'customer')->get();
        $statusOptions = BusinessStatus::asSelectArray();
        return Inertia::render('RealEstate::Business/Index', [
            'businessList' => $business,
            'searchedKeyword' => request()->keyword,
            'businessOwners' => $businessOwners,
            'orderBy' => request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc',
            'status' => request()->form && isset(request()->form['status']) ? request()->form['status'] : null,
            'rating' => request()->form && isset(request()->form['reviewRating']) ? request()->form['reviewRating'] : null,
            'statusOptions' => $statusOptions,
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
        return Inertia::render('RealEstate::Business/Create', [
            'businessOwners' => $businessOwners,
            'is_admin' => request()->user()->user_type == 'admin' ? true : false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(BusinessRequest $request, $moduleId)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('real-estate');
            $business = Business::create($request->all());
            $business->standardTags()->syncWithoutDetaching($moduleId);
            DB::commit();
            flash('Broker Added Sucessfully!', 'success');
            return \redirect()->route('real-estate.dashboard.brokers.index', $moduleId);
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
    public function show($moduleId, $businessUuid)
    {
        $mediaLogoSizes = \config()->get('realestate.media.logo');
        $mediaThumbnailSizes = \config()->get('realestate.media.thumbnail');
        $mediaBannerSizes = \config()->get('realestate.media.banner');
        $business = Business::with(['logo', 'thumbnail', 'banner', 'secondaryImages'])->where('uuid', $businessUuid)->first();
        $businessOwner = $business->businessOwner;
        return Inertia::render('RealEstate::Business/View', [
            'is_admin' => request()->user()->user_type == 'admin' ? true : false,
            'business' => $business,
            'mediaLogoSizes' => $mediaLogoSizes,
            'mediaThumbnailSizes' => $mediaThumbnailSizes,
            'mediaBannerSizes' => $mediaBannerSizes,
            'businessOwner' => $businessOwner,
            'token' => csrf_token()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($moduleId, $businessUuid)
    {
        $mediaLogoSizes = \config()->get('realestate.media.logo');
        $mediaThumbnailSizes = \config()->get('realestate.media.thumbnail');
        $mediaBannerSizes = \config()->get('realestate.media.banner');
        $business = Business::with(['logo', 'thumbnail', 'banner', 'secondaryImages'])->where('uuid', $businessUuid)->first();
        $businessOwners = User::select(['id', DB::raw('CONCAT(id, \' - \', last_name) as text')])
            ->where('user_type', 'business_owner')->orWhere('user_type', 'customer')->where('status', 'active')->get();
        return Inertia::render('RealEstate::Business/Edit', [
            'businessOwners' => $businessOwners,
            'is_admin' => request()->user()->user_type == 'admin' ? true : false,
            'business' => $business,
            'mediaLogoSizes' => $mediaLogoSizes,
            'mediaThumbnailSizes' => $mediaThumbnailSizes,
            'mediaBannerSizes' => $mediaBannerSizes,
            'token' => csrf_token()
        ]);
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
            ModuleSessionManager::setModule('real-estate');
            $business = Business::where('uuid', $businessUuid)->first();
            if (!$business->isDirty() && $business->status == 'rejected') {
                flash('No changes were made.', 'info');
                return redirect()->back();
            }
            $business->update($request->all());
            DB::commit();
            flash('Broker Updated Successfully.', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this broker', 'danger');
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
    public function destroy($moduleId, $id, Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $business = Business::whereUuid($id)->first();
            if (BusinessProducts::getProductCount($business)) {
                $business->delete();
                flash('Broker deleted succesfully', 'success');
                if ($currentCount > 1) {
                    return redirect()->back();
                } else {
                    $previousPage = max(1, $currentPage - 1);
                    return Redirect::route('real-estate.dashboard.brokers.index', [$moduleId, 'page' => $previousPage]);
                }
            } else {
                flash("We're unable to delete this broker, as it has property list associated with it.", 'danger');
            }
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Broker', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function changeStatus(Request $request, $moduleId, $id)
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
                        flash('Broker can not be activated due to subscription limitations.', 'danger');
                        return redirect()->back();
                    }
                    flash('You have activated maximum no of brokers according to your subscription plan.', 'danger', 'dashboard.subscription.subscribe.index');
                    return redirect()->back();
                }
            }
            if (!(auth()->user()->hasRole(['admin', 'newspaper'])) && ($business->status == 'inactive') && ($business->status_updated_by && $business->status_updated_by != auth()->user()->id)) {
                $message = "Broker is deactivated by administrators.";
                $status = "danger";
                DB::rollBack();
            } else {
                $business->status = $request->input('status');
                $business->status_updated_by = auth()->user()->id;
                $business->save();
                DB::commit();
                $message = "Broker status changed succesfully.";
                $status = "success";
            }
            flash($message, $status);
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Broker', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function activeStatus($moduleId, $id)
    {
        try {
            DB::beginTransaction();
            $business = Business::findOrFail($id);
            $business->status = BusinessStatus::ACTIVE;
            $business->status_updated_by = auth()->user()->id;
            $business->saveQuietly();
            DB::commit();
            $message = "Broker have been approved succesfully.";
            $status = "success";
            flash($message, $status);
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Broker', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function BrokerRequest($moduleId)
    {

        $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
        $user = auth()->user();
        $limit = \config()->get('settings.pagination_limit');
        $waiting_approval = BusinessStatus::WAITING_APPROVAL;
        $business = Business::with(['businessOwner', 'reviews', 'logo'])->where('status', $waiting_approval)->when($user->user_type === 'business_owner' ||  $user->user_type === 'customer', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->where(function ($query) use ($moduleId) {
            if ($moduleId != 'settings') {
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
        }])->orderBy('id', $orderBy)->paginate($limit);

        $businessOwners = User::select(['id', DB::raw('CONCAT(id, \' - \', last_name) as text')])
            ->where('user_type', 'business_owner')->orWhere('user_type', 'customer')->get();
        return Inertia::render('RealEstate::Business/BrokerRequest', [
            'businessList' => $business,
            'searchedKeyword' => request()->keyword,
            'businessOwners' => $businessOwners,
            'orderBy' => request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc',
            'status' => request()->form && isset(request()->form['status']) ? request()->form['status'] : null,
            'rating' => request()->form && isset(request()->form['reviewRating']) ? request()->form['reviewRating'] : null,
        ]);
    }

    public function rejectBroker(BusinessRejectRequest $request)
    {
        try {
            DB::beginTransaction();
            $business = Business::findOrFail($request->id);
            $business->status = BusinessStatus::REJECTED;
            $business->status_updated_by = auth()->user()->id;
            $business->rejection_reason = $request->reason;
            $business->saveQuietly();
            DB::commit();
            flash('Broker reject request submitted successfully.', 'success');
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
}
