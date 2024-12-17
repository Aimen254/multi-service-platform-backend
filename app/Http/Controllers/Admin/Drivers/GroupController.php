<?php

namespace App\Http\Controllers\Admin\Drivers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Group;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\DriverGroupRequest;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = \config()->get('settings.pagination_limit');
        $managers = User::select(['id', DB::raw('CONCAT(first_name, \' \', last_name) as text')])
            ->whereUserType('driver_manager')->where('status', 'active')->get();
        $drivers = User::select(['id', DB::raw('CONCAT(first_name, \' \', last_name) as text')])
            ->whereUserType('driver')->where('status', 'active')->get();
        $groups = Group::with(['manager', 'drivers' => function ($query) {
            $query->select('id');
        }])->where(function ($query) {
            if (\request()->keyword) {
                $keyword = \request()->keyword;
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhereHas('manager', function ($subQuery) use ($keyword) {
                        $subQuery->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"]);
                    });
            }
        })->withCount('drivers')->with('drivers', function($query){
            $query->select(['id', DB::raw('CONCAT(first_name, \' \', last_name) as text')]);
        })->orderBy('id', 'desc')->paginate($limit);
        return Inertia::render('Drivers/Groups/Index', [
            'searchedKeyword' => request()->keyword,
            'managers' => $managers,
            'groupsList' => $groups,
            'drivers' => $drivers
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
     * @param  \Illuminate\Http\DriverGroupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverGroupRequest $request)
    {
        try {
            DB::beginTransaction();
            $group = Group::create($request->all());
            $drivers = Arr::flatten(array_column($request->input('driver_ids'), 'id'));
            $group->drivers()->sync($drivers);
            DB::commit();
            flash('Driver Group created succesfully', 'success');
            return \redirect()->route('dashboard.driver.groups.index');
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\DriverGroupRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DriverGroupRequest $request, $id)
    {
        try {

            $group = Group::findOrFail($id);
            $group->update($request->all());
            $drivers = Arr::flatten(array_column($request->input('driver_ids'), 'id'));
            $group->drivers()->sync($drivers);
            DB::commit();
            flash('Driver group updated succesfully', 'success');
            return \redirect()->route('dashboard.driver.groups.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this driver group', 'danger');
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
            Group::findOrFail($id)->delete();
            flash('Driver group deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('dashboard.driver.groups.index', [ 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this group', 'danger');
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
            $group = Group::findOrFail($id);
            $group->statusChanger()->save();
            flash('Driver group status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this driver group', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
