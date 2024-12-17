<?php

namespace App\Http\Controllers\Admin\StandardTags;

use App\Models\Tag;
use Inertia\Inertia;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StandardTagRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\StandardTags;

class ProductTagController extends Controller
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
        //getting all product tags
        $standardTags = StandardTag::where(function ($query) use ($request){
                $query->where('type', 'product');
                $query->when($request->input('keyword'), function ($subQuery) use ($request) {
                    $subQuery->where('name', 'like', '%' . $request->input('keyword') . '%');
            });
        })->with('tags', function ($query) {
            $query->asTag();
        })->orderBy('id','desc')->paginate($limit);
        return Inertia::render('StandardTags/ProductTags/Index', [
            'standardTags' => $standardTags,
            'searchedKeyword' => $request->input('keyword'),
            'orphanTags' => Tag::asTag()->active()->whereNull('mapped_to')->whereType('product')->get(),
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
    public function store(StandardTagRequest $request)
    {   
        try {
            DB::beginTransaction();
            $tags = collect($request->input('tags'))->pluck('id')->toArray();
            $standardTag = StandardTag::create($request->all());
            Tag::whereIn('id', $tags)->update(['mapped_to' => $standardTag->id]);
            $productTags = DB::table('product_tag')->whereIn('tag_id', $tags)->pluck('product_id');
            $standardTag->productTags()->sync($productTags);
            DB::commit();
            flash('Product Tag created succesfully', 'success');
            return \redirect()->route('dashboard.productTag.index');
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
    public function update(StandardTagRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $standardTag = StandardTag::findOrFail($id);
            //linking orphan tags with standard tags
            $tags = collect($request->input('tags'))->pluck('id')->toArray();
            $standardTag->update($request->all());
            $standardTag->tags()->update(['mapped_to' => null]);
            Tag::whereIn('id', $tags)->update(['mapped_to' => $standardTag->id]);
            //syncing products and standard tags
            $productTags = DB::table('product_tag')->whereIn('tag_id', $tags)->pluck('product_id');
            $standardTag->productTags()->sync($productTags);
            DB::commit();
            flash('Product Tag updated succesfully', 'success');
            return \redirect()->route('dashboard.productTag.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this product tag.', 'danger');
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
            if (!$this->checkRelation($standardTag)) {
                $standardTag->delete();
                flash('Product tag deleted succesfully', 'success');
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
            $standardTag = StandardTag::findOrFail($id);
            $standardTag->statusChanger()->save();
            flash('Product Tag status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Product Tag', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
