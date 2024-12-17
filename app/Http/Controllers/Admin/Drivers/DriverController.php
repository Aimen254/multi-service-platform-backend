<?php

namespace App\Http\Controllers\Admin\Drivers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = \config()->get('settings.pagination_limit');
        $drivers = User::whereUserType('driver')->orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"])
                        ->orWhere('email', $keyword);
                }
            })->paginate($limit);
        return Inertia::render('Drivers/Index', [
            'driversList' => $drivers,
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
        return Inertia::render('Drivers/Create', [
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\DriverRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverRequest $request)
    {
        try {
            DB::beginTransaction();
            $request->merge([
                'password' => Hash::make($request->password),
                'user_type' => 'driver'
            ]);
            $driver = User::create($request->all());
            $address_data = $request->addresses;
            foreach($address_data as $key => $value)
            {
                if($value['name'] != null && $value['address'] !=null){
                    $driver->addresses()->create($value);
                }
            }
            DB::commit();
            flash('Driver created succesfully', 'success');
            return \redirect()->route('dashboard.drivers.index');
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
            $driver = User::with('addresses')->whereUserType('driver')->findOrFail($id);
            return Inertia::render('Drivers/Edit', [
                'driver' => $driver,
                'mediaSizes' => $mediaSizes,
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this driver', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\DriverRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DriverRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $driver = User::whereUserType('driver')->findOrFail($id);
            $request->merge([
                'password' => $request->input('password')
                    ? Hash::make($request->password) : $driver->password
            ]);
            if ($request->hasFile('avatar')) {
                deleteFile($driver->avatar);
            }
            $driver->update($request->all());

            $address_data = $request->addresses;
            $ids = array();
            foreach($address_data as $key => $value) {   
                if (array_key_exists('id', $value)) {
                    $ids[] = $value['id'];
                    if ($value['name'] != null && $value['address'] !=null) {
                        $driver->addresses()->where('id', $value['id'])->update([
                            'name' => $value['name'],
                            'latitude' => $value['latitude'],
                            'longitude' => $value['longitude'],
                            'address' => $value['address'],
                            'note' => $value['note']
                        ]); 
                    }          
                } else {
                    if ($value['name'] != null && $value['address'] !=null) {
                        $address = $driver->addresses()->create($value);
                        $ids[] = $address->id;
                    }
                }
            }
            $driver->addresses()->whereNotIn('id', $ids)->where('user_id', $driver->id)->delete();
            DB::commit();
            flash('Driver updated succesfully', 'success');
            return \redirect()->route('dashboard.drivers.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this driver', 'danger');
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
            flash('Driver deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('obituaries.dashboard.obituaries.index', ['page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this driver', 'danger');
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
            
            $driver = User::findOrFail($id);
            $driver->statusChanger()->save();
            flash('Driver status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this driver', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
