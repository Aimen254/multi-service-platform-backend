<?php

namespace Modules\RealEstate\Http\Controllers\Dashboard;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Business;
use Stripe\StripeClient;
use App\Traits\StripePayment;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Support\Renderable;
use Modules\RealEstate\Http\Requests\AgentRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use LDAP\Result;

class AgentController extends Controller
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
        $agents = User::with('businesses')->whereUserType('agent')->where('business_id', $this->business->id)->orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"])
                        ->orWhere('email', $keyword);
                }
            })->paginate($limit);
        return Inertia::render('RealEstate::Agent/Index', [
            'agentsList' => $agents,
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
        return Inertia::render('RealEstate::Agent/Create', [
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param AgentRequest $request
     * @return Renderable
     */
    public function store(AgentRequest $request, $moduleId, $businessUuid)
    {
        try {
            DB::beginTransaction();
            $stripeCustomerId = $this->getStripeCustomerId($request); //creating stripe customerId
            $validated = array_merge($request->validated(), [
                'password' => Hash::make($request->password),
                'user_type' => 'agent',
                'stripe_customer_id' => $stripeCustomerId,
                'business_id' => $this->business->id
            ]);

            $agent = User::create($validated);

            $address_data = $request->addresses;
            foreach ($address_data as $key => $value) {
                if ($value['name'] != null && $value['address'] != null) {
                    $agent->addresses()->create($value);
                }
            }

            DB::commit();
            flash('Real estate agent created succesfully', 'success');
            return \redirect()->route('real-estate.dashboard.agents.index', [$moduleId, $businessUuid]);
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \back();
        }
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
            $agent = User::with('addresses')->whereUserType('agent')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this agent', 'danger');
            return \redirect()->route('real-estate.dashboard.agents.index');
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        }

        return Inertia::render('RealEstate::Agent/Edit', [
            'agent' => $agent,
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param AgentRequest $request
     * @param int $id
     * @return Renderable
     */
    public function update(AgentRequest $request, $moduleId, $businessUuid, $id)
    {
        try {
            DB::beginTransaction();
            $agent = User::whereUserType('agent')->findOrFail($id);
            $validated = array_merge($request->validated(), [
                'password' => $request->input('password')
                    ? Hash::make($request->password) : $agent->password,
                'business_id' => $this->business->id
            ]);

            if ($request->hasFile('avatar')) {
                deleteFile($agent->avatar);
            }

            $agent->update($validated);

            // creating agent address
            $address_data = $request->addresses;
            $ids = array();
            foreach ($address_data as $value) {
                if (array_key_exists('id', $value)) {
                    $ids[] = $value['id'];
                    if ($value['name'] != null && $value['address'] != null) {
                        $agent->addresses()->where('id', $value['id'])->update([
                            'name' => $value['name'],
                            'latitude' => $value['latitude'],
                            'longitude' => $value['longitude'],
                            'address' => $value['address'],
                            'note' => $value['note'],
                            'is_default' => $value['is_default']
                        ]);
                    }
                } else {
                    if ($value['name'] != null && $value['address'] != null) {
                        $address = $agent->addresses()->create($value);
                        $ids[] = $address->id;
                    }
                }
            }
            $agent->addresses()->whereNotIn('id', $ids)->where('user_id',  $agent->id)->delete();
            DB::commit();
            flash('Real estate agent updated succesfully', 'success');
            return \redirect()->route('real-estate.dashboard.agents.index', [$moduleId, $businessUuid]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this agent', 'danger');
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
                flash('Real estate agent deleted succesfully', 'success');
                if ($currentCount > 1) {
                    return redirect()->back();
                } else {
                    $previousPage = max(1, $currentPage - 1);
                    return Redirect::route('real-estate.dashboard.agents.index', [$moduleId,$businessUuid, 'page' => $previousPage]);
                }
            } else {
                flash('Unable to delete this agent', 'danger');
            }
            return \back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this agent', 'danger');
            return \back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        }
    }

    /**
     * Change status of the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($moduleId, $businessUuid, $id)
    {
        try {
            $agent = User::findOrFail($id);
            $agent->statusChanger()->save();
            flash('Real estate agent status changed succesfully', 'success');
            return \back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this agent', 'danger');
            return \back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        }
    }
}
