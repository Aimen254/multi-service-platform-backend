<?php

namespace App\Http\Controllers\Admin\StandardTags;

use App\Models\Tag;
use Inertia\Inertia;
use App\Jobs\TagsPriority;
use App\Models\StandardTag;
use App\Traits\StandardTags;
use Illuminate\Http\Request;
use App\Jobs\changePriorityToOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Jobs\MoveTagsToProductPriority;
use App\Http\Requests\StandardTagRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BrandTagController extends Controller
{
    use StandardTags;
    /**
     * Display a listing of the resource
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = \config()->get('settings.pagination_limit');
        $brandTags = StandardTag::where(function ($query) use ($request){
                $query->where('type', 'brand');
                $query->when($request->input('keyword'), function ($subQuery) use ($request) {
                    $subQuery->where('name', 'like', '%' . $request->input('keyword') . '%');
            });
        })->with('tags', function ($query) {
            $query->asTag();
        })->orderBy('id','desc')->paginate($limit);
        $standardTags = StandardTag::whereNotIn('type', ['module', 'attribute', 'brand'])->where(function ($query){
            $query->whereDoesntHave('levelOne')
            ->whereDoesntHave('levelTwo')
            ->whereDoesntHave('levelThree')
            ->whereDoesntHave('levelFour')
            ->whereDoesntHave('tagHierarchies');
        })->asTag()->get();
        return Inertia::render('StandardTags/BrandTags/Index', [
            'brandTags' => $brandTags,
            'standardTags' => $standardTags,
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
    public function store(Request $request)
    {   
        try {
            DB::beginTransaction();
            $tags = collect($request->input('tags'))->pluck('id')->toArray();
            foreach ($tags as $id) {
                $standardTag = StandardTag::findOrFail($id);
                $standardTag->type = 'brand';
                $standardTag->priority = 3;
                $standardTag->saveQuietly();
                TagsPriority::dispatch($standardTag);
                changePriorityToOne::dispatch($standardTag);
            }
            DB::commit();
            flash('Tags mapped succesfully', 'success');
            return \redirect()->route('dashboard.brandTag.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this tag.', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StandardTag  $standardTag
     * @return \Illuminate\Http\Response
     */
    public function show(StandardTag $standardTag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StandardTag  $standardTag
     * @return \Illuminate\Http\Response
     */
    public function edit(StandardTag $standardTag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StandardTag  $standardTag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $standardTag = StandardTag::findOrFail($id);
            $standardTag->type = 'product';
            $standardTag->priority = 4;
            $standardTag->saveQuietly();
            DB::commit();
            flash('Brand Tag removed succesfully', 'success');
            return \redirect()->route('dashboard.brandTag.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this Brand tag.', 'danger');
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
     * @param  \App\Models\StandardTag  $standardTag
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $standardTag = StandardTag::findOrFail($id);
            $standardTag->type = 'product';
            $standardTag->priority = 4;
            $standardTag->saveQuietly();
            TagsPriority::dispatch($standardTag);
            // MoveTagsToProductPriority::dispatch($standardTag);
            changePriorityToOne::dispatch($standardTag, true);
            DB::commit();
            flash('Brand Tag removed succesfully', 'success');
            return \redirect()->route('dashboard.brandTag.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this Brand tag.', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
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
            $standardTag = StandardTag::findOrFail($id);
            $standardTag->type = 'product';
            $standardTag->saveQuietly();
            DB::commit();
            flash('Brand Tag removed succesfully', 'success');
            return \redirect()->route('dashboard.brandTag.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this Brand tag.', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        } 
    }
}
