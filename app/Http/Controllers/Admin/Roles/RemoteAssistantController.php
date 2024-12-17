<?php

namespace App\Http\Controllers\Admin\Roles;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RemoteAssistantRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RemoteAssistantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = \config()->get('settings.pagination_limit');
        $assistants = User::whereUserType('remote_assistant')->orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"])
                        ->orWhere('email', $keyword);
                }
            })->paginate($limit);
        return Inertia::render('Users/RemoteAssistants/Index', [
            'assistantsList' => $assistants,
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
        return Inertia::render('Users/RemoteAssistants/Create', [
            'mediaSizes' => $mediaSizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RemoteAssistantRequest $request)
    {
        try {
            DB::beginTransaction();
            $request->merge([
                'password' => Hash::make($request->password),
                'user_type' => 'remote_assistant'
            ]);
            $assistant = User::create($request->all());
            
            $address_data = $request->addresses;
            foreach($address_data as $key => $value)
            {
                if($value['name'] != null && $value['address'] !=null){
                    $assistant->addresses()->create($value);
                }
            }

            DB::commit();
            flash('Remote assistants created succesfully', 'success');
            return \redirect()->route('dashboard.assistants.index');
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
            $assistant = User::with('addresses')->whereUserType('remote_assistant')
                ->findOrFail($id);
            return Inertia::render('Users/RemoteAssistants/Edit', [
                'assistant' => $assistant,
                'mediaSizes' => $mediaSizes,
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this remote assistant', 'danger');
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
    public function update(RemoteAssistantRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $assistant = User::whereUserType('remote_assistant')->findOrFail($id);
            $request->merge([
                'password' => $request->input('password')
                    ? Hash::make($request->password) : $assistant->password
            ]);

            if ($request->hasFile('avatar')) {
                deleteFile($assistant->avatar);
            }
            $assistant->update($request->all());

            $address_data = $request->addresses;
            $ids = array();
            foreach($address_data as $key => $value)
            {   
                if(array_key_exists('id', $value)){
                    $ids[] = $value['id'];
                    if($value['name'] != null && $value['address'] !=null){
                        $assistant->addresses()->where('id', $value['id'])->update([
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
                        $address = $assistant->addresses()->create($value);
                        $ids[] = $address->id;
                    }
                }
            }
            $assistant->addresses()->whereNotIn('id', $ids)->where('user_id', $assistant->id)->delete();
            DB::commit();
            flash('Remote assistant updated succesfully', 'success');
            return \redirect()->route('dashboard.assistants.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this remote assistant', 'danger');
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
    public function destroy($id)
    {
        try {
            User::findOrFail($id)->delete();
            flash('Remote assistant deleted succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this remote assistant', 'danger');
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
            $assistant = User::findOrFail($id);
            $assistant->statusChanger()->save();
            flash('Remote assistant status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this assistant', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
