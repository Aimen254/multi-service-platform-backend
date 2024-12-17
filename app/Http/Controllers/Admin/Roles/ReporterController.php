<?php

namespace App\Http\Controllers\Admin\Roles;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\ReporterRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class ReporterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = \config()->get('settings.pagination_limit');
        $reporters = User::whereUserType('reporter')->orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"])
                        ->orWhere('email', $keyword);
                }
            })->paginate($limit);
        return Inertia::render('Users/Reporters/Index', [
            'reportersList' => $reporters,
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
        return Inertia::render('Users/Reporters/Create', [
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReporterRequest $request)
    {
        try {
            DB::beginTransaction();
            $request->merge([
                'password' => Hash::make($request->password),
                'user_type' => 'reporter'
            ]);
            $reporter = User::create($request->all());
            
            $address_data = $request->addresses;
            foreach($address_data as $key => $value)
            { 
                if($value['name'] != null && $value['address'] !=null){
                    $reporter->addresses()->create($value);
                }
            }

            DB::commit();
            flash('Reporter created succesfully', 'success');
            return \redirect()->route('dashboard.reporters.index');
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
            $reporter = User::with('addresses')->whereUserType('reporter')
                ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this reporter', 'danger');
            return \redirect()->route('dashboard.reporters.index');
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }

        return Inertia::render('Users/Reporters/Edit', [
            'reporter' => $reporter,
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
    public function update(ReporterRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $reporter = User::whereUserType('reporter')->findOrFail($id);
            $request->merge([
                'password' => $request->input('password')
                    ? Hash::make($request->password) : $reporter->password
            ]);

            if ($request->hasFile('avatar')) {
                deleteFile($reporter->avatar);
            }
            $reporter->update($request->all());

            $address_data = $request->addresses;
            $ids = array();
            foreach($address_data as $key => $value)
            {   
                if(array_key_exists('id', $value)){
                    $ids[] = $value['id'];
                    if($value['name'] != null && $value['address'] !=null){
                        $reporter->addresses()->where('id', $value['id'])->update([
                            'name' => $value['name'],
                            'latitude' => $value['latitude'],
                            'longitude' => $value['longitude'],
                            'address' => $value['address'],
                            'note' => $value['note'],
                            'is_default' => $value['is_default'],
                        ]);  
                    }                   
                }
                else{
                    if($value['name'] != null && $value['address'] !=null){
                        $address = $reporter->addresses()->create($value);
                        $ids[] = $address->id;
                    }
                }
            }
            $reporter->addresses()->whereNotIn('id', $ids)->where('user_id', $reporter->id)->delete();
            DB::commit();
            flash('Reporter updated succesfully', 'success');
            return \redirect()->route('dashboard.reporters.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this reporter', 'danger');
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
            flash('Reporter deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('dashboard.reporters.index', ['page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this reporter', 'danger');
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
            $reporter = User::findOrFail($id);
            $reporter->statusChanger()->save();
            flash('Reporter status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this reporter', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
