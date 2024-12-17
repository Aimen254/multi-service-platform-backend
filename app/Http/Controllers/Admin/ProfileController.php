<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Address;
use App\Http\Requests\Profile\PersonalInformationRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\AddressRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $mediaAvatarSizes = \config()->get('image.media.avatar');
            $id = auth()->user()->id;
            $user = User::with('addresses')->findOrFail($id);
            return Inertia::render('Profile/UpdateProfile', [
                'profileinformation' => $user,
                'mediaAvatarSizes' => $mediaAvatarSizes
            ]);
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PersonalInformationRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->update($request->all());

            $user->addresses()->updateOrCreate(['user_id' => $user->id], [
                'zipcode' => $request->zipcode,
            ]);

            DB::commit();
            flash('Profile updated succesfully', 'success');
            return \redirect()->route('dashboard.profile.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this user', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
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
        //
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            DB::beginTransaction();
            $id = auth()->user()->id;
            $user = User::findOrFail($id);
            $user->update([
                'password' => Hash::make($request->password)
            ]);
            DB::commit();
            flash('Password changed succesfully', 'success');
            return \redirect()->route('dashboard.profile.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function updateAvatar(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = auth()->user()->id;
            $user = User::findOrFail($id);
            if ($request->avatar != null) {
                $request->validate([
                    'avatar' => 'required|mimes:png,jpg,jpeg|max:1024',
                ]);
                if ($request->hasFile('avatar')) {
                    deleteFile($user->avatar);
                }
            } 
            $user->update([
                'avatar' => $request->avatar
            ]);
            DB::commit();
            flash('Avatar updated succesfully', 'success');
            return \redirect()->route('dashboard.profile.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }
    public function updateAddress(AddressRequest $request)
    {   
        try {
            DB::beginTransaction();
            $id = auth()->user()->id;
            $user = User::findOrFail($id);
            $address_data = $request->addresses;
            $ids = array();
            foreach($address_data as $key => $value)
            {   
                if(array_key_exists('id', $value)) {
                    $ids[] = $value['id'];
                    $user->addresses()->where('id', $value['id'])->update([
                        'name' => $value['name'],
                        'latitude' => $value['latitude'],
                        'longitude' => $value['longitude'],
                        'address' => $value['address'],
                        'note' => $value['note'],
                        'is_default' => $value['is_default'],
                    ]); 
                } else {
                    $address = $user->addresses()->create($value);
                    $ids[] = $address->id;
                }
            }
            $user->addresses()->whereNotIn('id', $ids)->where('user_id', $id)->delete();
            DB::commit();
            flash('Address updated succesfully', 'success');
            return \redirect()->route('dashboard.profile.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }
}
