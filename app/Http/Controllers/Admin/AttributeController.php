<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = \config()->get('settings.pagination_limit');
        $attributes = Attribute::with(['moduleTags' => function ($query) {
            $query->whereType('module')->select(['id', 'name as text', 'type', 'status']);
        }])->where(function ($query) use ($request) {
            $query->when($request->input('keyword'), function ($subQuery) use ($request) {
                $subQuery->where('name', 'like', '%' . $request->input('keyword') . '%');
            });
        })->orderBy('id', 'desc')->paginate($limit);
        $moduleTags = StandardTag::whereType('module')->where('status', 'active')->select(['id', 'name as text', 'type', 'status'])->get();
        return Inertia::render('Attributes/Index', [
            'attributes' => $attributes,
            'modules' => $moduleTags,
            'searchedKeyword' => $request->input('keyword'),
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
    public function store(AttributeRequest $request)
    {
        try {
            DB::beginTransaction();
            $modules = Arr::flatten(array_column($request->input('module'), 'id'));
            $attribute = Attribute::create($request->all());
            $attribute->moduleTags()->syncWithoutDetaching($modules);
            DB::commit();
            flash('Attribute updated', 'success');
            return \redirect()->route('dashboard.attributes.index');
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeRequest $request, Attribute $attribute)
    {
        try {
            DB::beginTransaction();
            if (!$attribute->manual_position && $request->input('manual_position')) {
                $attribute->standardTags->sortBy([['created_at', 'asc']])->each(function ($val, $key) use ($attribute) {
                    $attribute->standardTagPosition()->sync([$val->id => ['position' => $key + 1]], false);
                });
            }
            $modules = Arr::flatten(array_column($request->input('module'), 'id'));
            $attribute->update($request->all());
            $attribute->moduleTags()->syncWithoutDetaching($modules);
            DB::commit();
            flash('Attribute updated succesfully', 'success');
            return \redirect()->route('dashboard.attributes.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this attribute.', 'danger');
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
    public function destroy(Attribute $attribute, Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            if ($attribute->standardTags()->where('type','!=','module')->count() > 0) {
                flash('this attribute is related to tag and cannot be deleted', 'danger');
                return redirect()->back();
            }
            DB::beginTransaction();
            $attribute->moduleTags()->detach();
            $attribute->delete();
            DB::commit();
            flash('Attribute deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('dashboard.attributes.index', ['page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this attribute', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function changeStatus($id)
    {
        try {
            $attribute = Attribute::findOrFail($id);
            $attribute->statusChanger()->save();
            flash('Attribute status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Attribute Tag', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
