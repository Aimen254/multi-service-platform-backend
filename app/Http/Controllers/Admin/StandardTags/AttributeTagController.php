<?php

namespace App\Http\Controllers\Admin\StandardTags;

use Exception;
use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Attribute;
use App\Models\StandardTag;
use App\Traits\StandardTags;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\StandardTagRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttributeTagController extends Controller
{
    use StandardTags;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $slug)
    {
        try {
            $limit = \config()->get('settings.pagination_limit');
            $attributeType = Attribute::with('orphanTags')->whereSlug($slug)->firstOrFail();
            $attributeTag = $attributeType->standardTags()->where('type', 'attribute')->asTag()->with(['tags' => function ($query) {
                $query->asTag()->active();
            }, 'attributePosition'])->where(function ($query) use ($request) {
                $query->when($request->input('keyword'), function ($subQuery) use ($request) {
                    $subQuery->where('name', 'like', '%' . $request->input('keyword') . '%');
                });
            })->when($attributeType->manual_position, function ($query) use ($attributeType) {
                $query->orderBy(function ($standardTag) use ($attributeType) {
                    $standardTag->select('position')
                        ->from('attributes_standard_tags_positioning')
                        ->whereColumn('attributes_standard_tags_positioning.standard_tag_id', 'standard_tags.id')
                        ->where('attributes_standard_tags_positioning.attribute_id', $attributeType->id)
                        ->orderBy('position');
                });
            }, function ($query) {
                $query->orderBy('id', 'desc');
            })->paginate($limit);

            return Inertia::render('StandardTags/AttributeTags/Index', [
                'attributeTag' => $attributeTag,
                'searchedKeyword' => $request->input('keyword'),
                'attributeTypes' =>  $attributeType,
                'tags' => $attributeType->orphanTags()->asTag()->whereStatus('active')->where('is_show', 1)->whereNull('mapped_to')->get(),
                'attributeTagCount' => $attributeType->standardTags()->count(),
            ]);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
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
    public function store(StandardTagRequest $request, $slug)
    {
        try {
            DB::beginTransaction();
            $attributeTag = StandardTag::create($request->all());
            //linking orphan tags with standard tags
            $tags = collect($request->input('tags'))->pluck('id')->toArray();
            Tag::whereIn('id', $tags)->update([
                'mapped_to' => $attributeTag->id,
                'type' => $request->input('type'),
                'attribute_id' => $request->input('attribute_id')
            ]);
            //syncing products and standard tags
            $productTags = DB::table('product_tag')->whereIn('tag_id', $tags)->pluck('product_id');
            $attributeTag->productTags()->sync($productTags);
            DB::commit();
            flash('Attribute Tag created succesfully', 'success');
            return \redirect()->back();
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
    public function update(StandardTagRequest $request, $slug, StandardTag $attributeTag)
    {
        try {
            $attributeTag->update($request->all());
            //linking orphan tags with standard tags
            $tags = collect($request->input('tags'))->pluck('id')->toArray();
            $attributeTag->tags()->update(['mapped_to' => null]);
            Tag::whereIn('id', $tags)->update([
                'mapped_to' => $attributeTag->id,
                'type' => $request->input('type'),
                'attribute_id' => $request->input('attribute_id')
            ]);
            //syncing products and standard tags
            $productTags = DB::table('product_tag')->whereIn('tag_id', $tags)->pluck('product_id');
            $attributeTag->productTags()->sync($productTags);
            DB::commit();
            flash('Attribute Tag updated succesfully', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this Attribute tag.', 'danger');
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
    public function destroy($slug, StandardTag $attributeTag)
    {
        try {
            $attributeType = Attribute::where('slug', $slug)->firstOrFail();
            $attributeTagPosition = $attributeType->standardTagPosition()->wherePivot('standard_tag_id', $attributeTag->id)->first()?->pivot?->position;
            $attributeType->standardTagPosition()->detach($attributeTag->id);
            if ($attributeTagPosition) {
                $attributeType->standardTagPosition()->wherePivot('position', '>', $attributeTagPosition)->decrement('position');
            }
            $attributeTag->attribute()->detach($attributeType->id);
            $existInOtherAttribute = $attributeTag->attribute()->first();
            $attributeTag->type = $existInOtherAttribute ? 'attribute' : 'product';
            $attributeTag->priority = $existInOtherAttribute ?  2 : 4;
            $attributeTag->saveQuietly();
            DB::commit();
            flash('Attribute Tag removed succesfully', 'success');
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
            flash('Attribute Tag status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Attribute Tag', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function setPosition(Request $request)
    {
        try {
            $attributeType = Attribute::findOrFail($request->input('attribute_type'));
            $updatedStandardTag = $attributeType->standardTagPosition()->wherePivot('standard_tag_id', $request->input('attribute_tag_id'))->first()?->pivot;
            $swapedStandardTag = $attributeType->standardTagPosition()->wherePivot('position', $request->input('position'))->first()?->pivot;
            if ($swapedStandardTag) {
                $attributeType->standardTagPosition()->sync([$swapedStandardTag->standard_tag_id => ['position' => $updatedStandardTag->position]], false);
            }
            $attributeType->standardTagPosition()->sync([$request->input('attribute_tag_id') => ['position' => $request->input('position')]], false);
            flash('Position Changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this attribute tag.', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
