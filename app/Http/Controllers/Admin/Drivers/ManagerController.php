<?php

namespace App\Http\Controllers\Admin\Drivers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\DriverManagerRequest;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = \config()->get('settings.pagination_limit');
        $managers = User::whereUserType('driver_manager')->orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"])
                        ->orWhere('email', $keyword);
                }
            })->paginate($limit);
        return Inertia::render('Drivers/Managers/Index', [
            'managersList' => $managers,
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
        return Inertia::render('Drivers/Managers/Create', [
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\DriverManagerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverManagerRequest $request)
    {
        try {
            DB::beginTransaction();
            $request->merge([
                'password' => Hash::make($request->password),
                'user_type' => 'driver_manager'
            ]);
            $manager = User::create($request->all());
            $address_data = $request->addresses;
            foreach($address_data as $key => $value)
            {
                if($value['name'] != null && $value['address'] !=null){
                    $manager->addresses()->create($value);
                }
            }
            DB::commit();
            flash('Driver Manager created succesfully', 'success');
            return \redirect()->route('dashboard.driver.managers.index');
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
            $manager = User::with('addresses')->whereUserType('driver_manager')->findOrFail($id);
            return Inertia::render('Drivers/Managers/Edit', [
                'manager' => $manager,
                'mediaSizes' => $mediaSizes,
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this driver manager', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\DriverManagerRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DriverManagerRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $manager = User::whereUserType('driver_manager')->findOrFail($id);
            $request->merge([
                'password' => $request->input('password')
                    ? Hash::make($request->password) : $manager->password
            ]);
            if ($request->hasFile('avatar')) {
                deleteFile($manager->avatar);
            }
            $manager->update($request->all());
            
            $address_data = $request->addresses;
            $ids = array();
            foreach($address_data as $key => $value) {
                if(array_key_exists('id', $value)) {
                    $ids[] = $value['id'];
                    if($value['name'] != null && $value['address'] !=null) {
                        $manager->addresses()->where('id', $value['id'])->update([
                            'name' => $value['name'],
                            'latitude' => $value['latitude'],
                            'longitude' => $value['longitude'],
                            'address' => $value['address'],
                            'note' => $value['note']
                        ]); 
                    }          
                } else {
                    if($value['name'] != null && $value['address'] !=null) {
                        $address = $manager->addresses()->create($value);
                        $ids[] = $address->id;
                    }
                }
            }
            $manager->addresses()->whereNotIn('id', $ids)->where('user_id', $manager->id)->delete();
            DB::commit();
            flash('Driver manager updated succesfully', 'success');
            return \redirect()->route('dashboard.driver.managers.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this driver manager', 'danger');
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
            flash('Driver manager deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('dashboard.driver.managers.index', [ 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this manager', 'danger');
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
            $manager = User::findOrFail($id);
            $manager->statusChanger()->save();
            flash('Driver manager status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this driver manager', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
