<?php

namespace App\Http\Controllers\Admin\Roles;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AdministratorRequest;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class AdministratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = \config()->get('settings.pagination_limit');
        $administrators = User::whereUserType('admin')->orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"])
                        ->orWhere('email', $keyword);
                }
            })->paginate($limit);
        return Inertia::render('Users/Administrators/Index', [
            'administratorsList' => $administrators,
            'searchedKeyword' => request()->keyword,
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
        return Inertia::render('Users/Administrators/Create', [
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\AdministratorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdministratorRequest $request)
    {
        try {
            DB::beginTransaction();
            $request->merge([
                'password' => Hash::make($request->password),
                'user_type' => 'admin'
            ]);
            $administrator = User::create($request->all());
            $address_data = $request->addresses;
            foreach($address_data as $key => $value)
            {   
                if($value['name'] != null && $value['address'] !=null){
                    $administrator->addresses()->create($value);
                }
            }
            DB::commit();
            flash('Administrator created succesfully', 'success');
            return \redirect()->route('dashboard.administrators.index');
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
            $administrator = User::with('addresses')->whereUserType('admin')->findOrFail($id);
            return Inertia::render('Users/Administrators/Edit', [
                'administrator' => $administrator,
                'mediaSizes' => $mediaSizes,
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this administrator', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdministratorRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $administrator = User::whereUserType('admin')->findOrFail($id);
            $request->merge([
                'password' => $request->input('password')
                    ? Hash::make($request->password) : $administrator->password
            ]);
            if ($request->hasFile('avatar')) {
                deleteFile($administrator->avatar);
            }
            $administrator->update($request->all());
            
            $address_data = $request->addresses;
            $ids = array();
            foreach($address_data as $key => $value)
            {   
                if(array_key_exists('id', $value)){
                    $ids[] = $value['id'];
                    if($value['name'] != null && $value['address'] !=null){
                        $administrator->addresses()->where('id', $value['id'])->update([
                            'name' => $value['name'],
                            'latitude' => $value['latitude'],
                            'longitude' => $value['longitude'],
                            'address' => $value['address'],
                            'note' => $value['note'],
                            'is_default' => $value['is_default']
                        ]); 
                    }          
                }
                else{
                    if($value['name'] != null && $value['address'] !=null){
                        $address = $administrator->addresses()->create($value);
                        $ids[] = $address->id;
                    }
                }
            }
            $administrator->addresses()->whereNotIn('id', $ids)->where('user_id', $administrator->id)->delete();
            DB::commit();
            flash('Administrator updated succesfully', 'success');
            return \redirect()->route('dashboard.administrators.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this administrator', 'danger');
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
            // check if the administrator will be the last
            if (User::where('user_type', 'admin')->count() == 1) {
                flash('You cannot delete the last administrator', 'danger');
                return redirect()->back();
            }
            User::findOrFail($id)->delete();
            flash('Administrator deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('dashboard.administrators.index', [ 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this administrator', 'danger');
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
            // check if the administrator will be the last
            if (User::where('user_type', 'admin')->count() == 1) {
                flash('You cannot change the status of last administrator', 'danger');
                return redirect()->back();
            }
            $administrator = User::findOrFail($id);
            $administrator->statusChanger()->save();
            flash('Administrator status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this administrator', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
