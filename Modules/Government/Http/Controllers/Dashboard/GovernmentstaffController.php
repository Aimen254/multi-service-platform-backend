<?php

namespace Modules\Government\Http\Controllers\Dashboard;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Business;
use Stripe\StripeClient;
use App\Traits\StripePayment;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Government\Http\Requests\GovernmentStaffRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class GovernmentstaffController extends Controller
{
    use StripePayment;
    protected StripeClient $stripeClient;
    public $business;
    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
        $this->business = Business::where('uuid', Route::current()->parameters['business_uuid'])->first();
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $limit = \config()->get('settings.pagination_limit');
        $staffs = User::with('businesses')->whereUserType('government_staff')->where('business_id', $this->business->id)->orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"])
                        ->orWhere('email', $keyword);
                }
            })->paginate($limit);
        return Inertia::render('Government::Staff/Index', [
            'staffsList' => $staffs,
            'searchedKeyword' => request()->keyword
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $mediaSizes = \config()->get('image.media.avatar');
        return Inertia::render('Government::Staff/Create', [
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(GovernmentStaffRequest $request, $moduleId, $businessUuid)
    {
        try {
            DB::beginTransaction();
            $stripeCustomerId = $this->getStripeCustomerId($request); //creating stripe customerId
            $validated = array_merge($request->validated(), [
                'password' => Hash::make($request->password),
                'user_type' => 'government_staff',
                'stripe_customer_id' => $stripeCustomerId,
                'business_id' => $this->business->id
            ]);

            $agent = User::create($validated);

            DB::commit();
            flash('Government Staff created succesfully', 'success');
            return \redirect()->route('government.dashboard.department.staffs.index', [$moduleId, $businessUuid]);
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \back();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('government::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($moduleId, $businessUuid, $id)
    {
        try {
            $mediaSizes = \config()->get('image.media.avatar');
            $staff = User::whereUserType('government_staff')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this government staff', 'danger');
            return \redirect()->route('government.dashboard.department.staffs.index');
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        }

        return Inertia::render('Government::Staff/Edit', [
            'staff' => $staff,
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(GovernmentStaffRequest $request, $moduleId, $businessUuid, $id)
    {
        try {
            DB::beginTransaction();
            $staff = User::whereUserType('government_staff')->findOrFail($id);
            $validated = array_merge($request->validated(), [
                'password' => $request->input('password')
                    ? Hash::make($request->password) : $staff->password,
                'business_id' => $this->business->id
            ]);

            if ($request->hasFile('avatar')) {
                deleteFile($staff->avatar);
            }

            $staff->update($validated);
            DB::commit();
            flash('Government staff updated succesfully', 'success');
            return \redirect()->route('government.dashboard.department.staffs.index', [$moduleId, $businessUuid]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this government staff', 'danger');
            return \back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \back();
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
            $user = User::findOrFail($id);
            if ($user->businesses->count() == 0) {
                $user->forceDelete();
                flash('Government staff deleted succesfully', 'success');
                if ($currentCount > 1) {
                    return redirect()->back();
                } else {
                    $previousPage = max(1, $currentPage - 1);
                    return Redirect::route('government.dashboard.department.staffs.index', [$moduleId,$businessUuid, 'page' => $previousPage]);
                }
            } else {
                flash('Unable to delete this government staff ', 'danger');
            }
            return \back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this government staff', 'danger');
            return \back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        }
    }

    public function changeStatus($moduleId, $businessUuid, $id)
    {
        try {
            $staff = User::findOrFail($id);
            $staff->statusChanger()->save();
            flash('Government staff status changed succesfully', 'success');
            return \back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this government staff', 'danger');
            return \back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        }
    }
}
