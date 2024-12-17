<?php

namespace App\Http\Controllers\Admin\Settings;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::with('users')->where('name', '!=', 'admin')->orderBy('id', 'asc')->get();
        return Inertia::render('Settings/Roles/Index', [
            'rolesList' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        try {  
            $role = Role::find($id);
            $permissions = Permission::get();
            $permissionGroups = $this->formatPermissions($permissions);
            $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
                ->pluck('role_has_permissions.permission_id');
            return Inertia::render('Settings/Roles/Edit', [
                'role' => $role,
                'permissionGroups' => $permissionGroups,
                'rolePermissions' => $rolePermissions
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Role', 'danger');
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
    public function update(Request $request, $id)
    {
        try {
            $role = Role::find($id);
        
            $role->syncPermissions($request->input('rolePermission'));
            session()->flash('flash.type', 'success');
            session()->flash('flash.message', 'Permissions Updated!');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Role', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            session()->flash('flash.type', 'error');
            session()->flash('flash.message', $e->getMessage());
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
        //
    }
    /**
     * @param $permissions
     *
     * @return array
     */
    public static function formatPermissions($permissions)
    {
        $permissionGroups = [];
        foreach ($permissions as $permission) {
            $permission->group = str_replace('_', ' ', $permission->group);
            $permissionGroups[$permission->group][] = $permission;
        }
        return $permissionGroups;
    }
}
