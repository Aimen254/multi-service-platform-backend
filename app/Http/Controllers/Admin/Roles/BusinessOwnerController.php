<?php

namespace App\Http\Controllers\Admin\Roles;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Business;;

use Stripe\StripeClient;
use App\Traits\StripePayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\BusinessOwnerRequest;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class BusinessOwnerController extends Controller
{
    use StripePayment;
    protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = \config()->get('settings.pagination_limit');
        $businessOwners = User::with('businesses')->whereUserType('business_owner')->orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"])
                        ->orWhere('email', $keyword);
                }
            })->paginate($limit);
        return Inertia::render('Users/BusinessOwners/Index', [
            'businessOwnersList' => $businessOwners,
            'searchedKeyword' => request()->keyword
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mediaSizes = \config()->get('image.media.avatar');
        return Inertia::render('Users/BusinessOwners/Create', [
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BusinessOwnerRequest $request)
    {
        try {
            DB::beginTransaction();
            $stripeCustomerId = $this->getStripeCustomerId($request); //creating stripe customerId
            $request->merge([
                'password' => Hash::make($request->password),
                'user_type' => 'business_owner',
                'stripe_customer_id' => $stripeCustomerId,
            ]);
            $businessOwner = User::create($request->all());

            $address_data = $request->addresses;
            foreach ($address_data as $key => $value) {
                if ($value['name'] != null && $value['address'] != null) {
                    $businessOwner->addresses()->create($value);
                }
            }
            DB::commit();
            flash('Business Owner created succesfully', 'success');
            return \redirect()->route('dashboard.owners.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
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
        try {
            $mediaSizes = \config()->get('image.media.avatar');
            $businessOwner = User::with('addresses')->whereUserType('business_owner')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business owner', 'danger');
            return \redirect()->route('dashboard.owners.index');
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }

        return Inertia::render('Users/BusinessOwners/Edit', [
            'businessOwner' => $businessOwner,
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BusinessOwnerRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $businessOwner = User::whereUserType('business_owner')->findOrFail($id);
            $request->merge([
                'password' => $request->input('password')
                    ? Hash::make($request->password) : $businessOwner->password
            ]);

            if ($request->hasFile('avatar')) {
                deleteFile($businessOwner->avatar);
            }

            $businessOwner->update($request->all());

            // creating bsuiness owner address
            $address_data = $request->addresses;
            $ids = array();
            foreach ($address_data as $key => $value) {
                if (array_key_exists('id', $value)) {
                    $ids[] = $value['id'];
                    if ($value['name'] != null && $value['address'] != null) {
                        $businessOwner->addresses()->where('id', $value['id'])->update([
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
                        $address = $businessOwner->addresses()->create($value);
                        $ids[] = $address->id;
                    }
                }
            }
            $businessOwner->addresses()->whereNotIn('id', $ids)->where('user_id',  $businessOwner->id)->delete();
            DB::commit();
            flash('Business Owner updated succesfully', 'success');
            return \redirect()->route('dashboard.owners.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this business owner', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $user = User::findOrFail($id);
            if ($user->businesses->count() == 0) {
                $user->delete();
                flash('Business owner deleted succesfully', 'success');
            } else {
                flash('Unable to delete this business', 'danger');
            }
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('dashboard.owners.destroy', [ 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business owner', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    /**
     * change the specified resource status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($id)
    {
        try {
            $businessOwner = User::findOrFail($id);
            $businessOwner->statusChanger()->save();
            flash('business owner status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business owner', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
