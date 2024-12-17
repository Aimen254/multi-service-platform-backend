<?php

namespace Modules\Employment\Http\Controllers\Dashboard;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Traits\BusinessProducts;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\Employment\Http\Requests\EmployerRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class EmployerController extends Controller
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

        $business = Business::with(['businessOwner', 'reviews', 'logo', 'thumbnail'])->when($user->user_type === 'business_owner', function ($query) use ($user) {
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
        $moduleTags = StandardTag::select(['id', 'name as text', 'type'])->where('type', 'module')->active()->get();
        return Inertia::render('Employment::Employers/Index', [
            'businessList' => $business,
            'searchedKeyword' => request()->keyword,
            'businessOwners' => $businessOwners,
            'orderBy' => request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc',
            'status' => request()->form && isset(request()->form['status']) ? request()->form['status'] : null,
            'rating' => request()->form && isset(request()->form['reviewRating']) ? request()->form['reviewRating'] : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($moduleId)
    {
        $businessOwners = User::select(['id', DB::raw('CONCAT(id, \' - \', last_name) as text')])
            ->where('user_type', 'business_owner')->orWhere('user_type', 'customer')->where('status', 'active')->get();
        return Inertia::render('Employment::Employers/Create', [
            'businessOwners' => $businessOwners,
            'is_admin' => request()->user()->user_type == 'admin' ? true : false,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(EmployerRequest $request, $moduleId)
    {
        try {
            DB::beginTransaction();
            $business = Business::create($request->all());
            $business->standardTags()->syncWithoutDetaching($moduleId);
            DB::commit();
            flash('Employer Added Sucessfully!', 'success');
            return \redirect()->route('employment.dashboard.employers.index', $moduleId);
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
        return view('employment::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($moduleId, $businessUuid)
    {
        $mediaLogoSizes = \config()->get('employment.media.logo');
        $mediaThumbnailSizes = \config()->get('employment.media.thumbnail');
        $mediaBannerSizes = \config()->get('employment.media.banner');

        $business = Business::with(['logo', 'thumbnail', 'banner', 'secondaryImages'])->where('uuid', $businessUuid)->first();
        $businessOwners = User::select(['id', DB::raw('CONCAT(id, \' - \', last_name) as text')])
            ->where('user_type', 'business_owner')->orWhere('user_type', 'customer')->where('status', 'active')->get();
        return Inertia::render('Employment::Employers/Edit', [
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
    public function update(EmployerRequest $request, $moduleId, $businessUuid)
    {
        try {
            DB::beginTransaction();
            $business = Business::where('uuid', $businessUuid)->first();
            $business->update($request->all());
            DB::commit();
            flash('Employer Updated Successfully.', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this Employer', 'danger');
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
    public function destroy($moduleId, $id,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $business = Business::whereUuid($id)->first();
            if (BusinessProducts::getProductCount($business)) {
                $business->delete();
                flash('Employer deleted succesfully', 'success');
                if ($currentCount > 1) {
                    return redirect()->back();
                } else {
                    $previousPage = max(1, $currentPage - 1);
                    return Redirect::route('employment.dashboard.employers.index', [$moduleId, 'page' => $previousPage]);
                }
            } else {
                flash("We're unable to delete this employer, as it has job posts associated with it.", 'danger');
            }
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Employer', 'danger');
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
                $permission = $this->checkActiveBusinesses($business, 'check_active_businesses');
                if (!$permission) {
                    DB::rollBack();
                    if (auth()->user()->user_type == 'admin') {
                        flash('Employer can not be activated due to subscription limitations.', 'danger');
                        return redirect()->back();
                    }
                    flash('You have activated maximum no of employers according to your subscription plan.', 'danger', 'dashboard.subscription.subscribe.index');
                    return redirect()->back();
                }
            }
            if (!(auth()->user()->hasRole(['admin', 'newspaper'])) && ($business->status == 'inactive') && ($business->status_updated_by && $business->status_updated_by != auth()->user()->id)) {
                $message = "Employer is deactivated by administrators.";
                $status = "danger";
                DB::rollBack();
            } else {
                $business->status = $business->status == 'active' ? 'inactive' : 'active';
                $business->status_updated_by = auth()->user()->id;
                $business->save();
                DB::commit();
                $message = "Employer status changed succesfully.";
                $status = "success";
            }
            flash($message, $status);
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Employer', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
