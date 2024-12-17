<?php

namespace App\Http\Controllers\Admin\Roles;

use App\Models\User;
use Inertia\Inertia;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Traits\StripePayment;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Traits\CustomerStreetAddress;
use App\Http\Requests\CustomerRequest;
use Spatie\Permission\Traits\HasRoles;
use App\Http\Requests\ChangeRoleRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class CustomerController extends Controller
{
    use StripePayment, HasRoles;

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
        $customers = User::whereUserType('customer')->orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"])
                        ->orWhere('email', $keyword);
                }
            })->paginate($limit);
        return Inertia::render('Users/Customers/Index', [
            'customersList' => $customers,
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
        return Inertia::render('Users/Customers/Create', [
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        try {
            DB::beginTransaction();
            $stripeCustomerId = $this->getStripeCustomerId($request); //creating stripe customerId
            $request->merge([
                'password' => Hash::make($request->password),
                'user_type' => 'customer',
                'stripe_customer_id' => $stripeCustomerId,
            ]);
            $customer = User::create($request->all());
            $address_data = $request->addresses;
            foreach ($address_data as $key => $value) {
                if ($value['name'] != null && $value['address'] != null) {
                    $customer->addresses()->create($value);
                    $latestAddress = $customer->addresses()->latest()->first();
                    CustomerStreetAddress::streetAddress($latestAddress);
                }
            }
            DB::commit();
            flash('Customer created succesfully', 'success');
            return \redirect()->route('dashboard.customers.index');
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
            $customer = User::with('addresses')->whereUserType('customer')
                ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this customer', 'danger');
            return \redirect()->route('dashboard.customers.index');
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }

        return Inertia::render('Users/Customers/Edit', [
            'customer' => $customer,
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
    public function update(CustomerRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $customer = User::whereUserType('customer')->findOrFail($id);
            $request->merge([
                'password' => $request->input('password')
                    ? Hash::make($request->password) : $customer->password
            ]);

            if ($request->hasFile('avatar')) {
                deleteFile($customer->avatar);
            }
            $customer->update($request->all());

            $address_data = $request->addresses;
            $ids = array();
            foreach ($address_data as $key => $value) {
                if (array_key_exists('id', $value)) {
                    $ids[] = $value['id'];
                    if ($value['name'] != null && $value['address'] != null) {
                        $customer->addresses()->where('id', $value['id'])->update([
                            'name' => $value['name'],
                            'latitude' => $value['latitude'],
                            'longitude' => $value['longitude'],
                            'address' => $value['address'],
                            'note' => $value['note'],
                            'is_default' => $value['is_default']
                        ]);

                        $address = $customer->addresses()->where('id', $value['id'])->first();
                        CustomerStreetAddress::streetAddress($address);
                    }
                } else {
                    if ($value['name'] != null && $value['address'] != null) {
                        $address = $customer->addresses()->create($value);
                        CustomerStreetAddress::streetAddress($address);
                        $ids[] = $address->id;
                    }
                }
            }
            $customer->addresses()->whereNotIn('id', $ids)->where('user_id', $customer->id)->delete();
            DB::commit();
            flash('Customer updated succesfully', 'success');
            return \redirect()->route('dashboard.customers.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this customer', 'danger');
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
            User::findOrFail($id)->delete();
            flash('Customer deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('dashboard.customers.index', ['page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this customer', 'danger');
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
            $customer = User::findOrFail($id);
            $customer->statusChanger()->save();
            flash('Customer status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this customer', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }



    public function changeRole(ChangeRoleRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $role =  Role::where('name',  $request->user_type)->first();
            $user->update($request->all());
            $user->roles()->sync([$role->id]);
            flash('User role updated succesfully', 'success');
            return \redirect()->route('dashboard.customers.index');
        } catch (ModelNotFoundException $e) {
            flash('Unable to find User', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }
}
