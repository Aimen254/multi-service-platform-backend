<?php

namespace App\Http\Controllers\Admin\StandardTags;

use Inertia\Inertia;
use App\Models\StandardTag;
use App\Traits\StandardTags;
use Illuminate\Http\Request;
use App\Jobs\ProductTagsManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndustryTagRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IndustryTagController extends Controller
{
    use StandardTags;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = \config()->get('settings.pagination_limit');
        $mediaIconSizes = \config()->get('image.media.icon');

        $productTags = StandardTag::where(function ($query) use ($request) {
            $query->when($request->input('keyword'), function ($subQuery) use ($request) {
                $subQuery->where('name', 'like', '%' . $request->input('keyword') . '%');
            });
            $query->whereNotIn('type', ['module']);
        })->orderBy('id', 'desc')->paginate($limit);

        return Inertia::render('IndustryTags/Index', [
            'productTags' => $productTags,
            'mediaIconSizes' => $mediaIconSizes,
            'searchedKeyword' => $request->input('keyword')
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
    public function store(IndustryTagRequest $request)
    {
        try {
            StandardTag::create($request->all());
            // flash('Tag created succesfully', 'success');
            return \redirect()->route('dashboard.tag.index');
        } catch (\Exception $e) {
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
    public function update(IndustryTagRequest $request, $id)
    {
        try {
            $standardTag = StandardTag::findOrFail($id);
            $request->hasFile('icon')
                ? deleteFile($standardTag->icon) : $request->merge(['icon' => $standardTag->icon]);
            $standardTag->update($request->all());
            ProductTagsManager::dispatch($standardTag, 'standard_tag');
            flash('Tag updated succesfully', 'success');
            return \redirect()->route('dashboard.tag.index');
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this industry tag.', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
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
            $industryTag = StandardTag::findOrFail($id);
            if (!$this->checkRelation($industryTag)) {
                if ($industryTag->icon) {
                    deleteFile($industryTag->icon);
                }
                $industryTag->delete();
                flash('tag deleted succesfully', 'success');
            } else {
                flash('Can not delete tag. It is linked to product or business.', 'danger');
            }
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this tag', 'danger');
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
            $industryTag = StandardTag::findOrFail($id);
            $industryTag->statusChanger()->save();
            flash('Industry Tag status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Industry Tag', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
