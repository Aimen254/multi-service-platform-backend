<?php

namespace App\Http\Controllers\Admin\Mapping;

use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Product;;

use App\Models\Attribute;
use App\Jobs\TagsPriority;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Jobs\StandardTagMapper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\MoveTagsToProductPriority;
use App\Traits\ProductTagsLevelManager;
use App\Http\Requests\StandardTagMapperRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function PHPSTORM_META\type;

class StandardTagMapperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('TagMapper/Index');
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
    public function store(StandardTagMapperRequest $request)
    {
        try {
            $newtagIds = null;
            $removingTagIds = null;
            $tagIds = Arr::flatten(array_column($request->tags, 'id'));
            if ($request->type == 'attribute') {
                $mappedIds = Attribute::findOrFail($request->attribute)->standardTags()->where('type', 'attribute')->pluck('id')->toArray();
                $newtagIds = array_diff($tagIds, $mappedIds);
                $removingTagIds = array_diff($mappedIds, $tagIds);
                $this->setTagPosition($newtagIds, $removingTagIds, $request->all());
            } else {
                $mappedIds = $request->filled('standardTag') ?
                    StandardTag::findOrFail($request->standardTag)->tags_()->pluck('id')->toArray() : StandardTag::find($request->brand)->tags_()->pluck('id')->toArray();
                $newtagIds = array_diff($tagIds, $mappedIds);
                $removingTagIds = array_diff($mappedIds, $tagIds);
            }
            StandardTagMapper::dispatch($tagIds, $request, $removingTagIds);
            DB::commit();
            flash('Tag mapped succesfully.', 'success');
            return \redirect()->back();
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this tag.', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return redirect()->back();
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
    public function update(Request $request, $id)
    {
        //
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

    public function getTags()
    {
        try {
            $tags = null;
            if (request()->input('type') == 'tag' || request()->input('type') == 'brand') {
                $tags = Tag::where('is_show', true)->where('name', 'like', '%' . request()->input('tag') . '%')
                    ->select(['id', 'name as text', 'attribute_id', 'mapped_to'])->get();
            } else {
                $tags = StandardTag::whereNotIn('type', ['brand', 'module'])->where('name', 'like', '%' . request()->input('tag') . '%')->with(['tags' => function ($query) {
                    $query->asTag();
                }])
                // commeting this code because of certain uses cases in which hierarchy tag and attribute tag can be same
                // ->where(function ($query) {
                //     $query->whereDoesntHave('levelOne')
                //         ->whereDoesntHave('levelTwo')
                //         ->whereDoesntHave('levelThree')
                //         ->whereDoesntHave('levelFour')
                //         ->whereDoesntHave('tagHierarchies');
                // })
                ->active()->asTag()->get();
            }
            return \response()->json([
                'tags' => $tags,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function setTagPosition($newIds, $removingIds, $request)
    {
        $attributeType = Attribute::findOrFail($request['attribute']);
        if (count($newIds)) {
            foreach ($newIds as $key => $id) {
                $standardTag = StandardTag::find($id);
                $max = $attributeType->standardTagPosition()->max('position');
                $maxPosition = $max + 1;
                $attributeType->standardTagPosition()->sync([$standardTag->id => ['position' => $maxPosition]], false);
            }
        }
        if (count($removingIds)) {
            foreach ($removingIds as $key => $id) {
                $standardTag = StandardTag::with('attributePosition')->find($id);
                $tagPosition = $attributeType->standardTagPosition()->wherePivot('standard_tag_id', $standardTag->id)->first()?->pivot?->position;
                if ($tagPosition) {
                    $attributeType->standardTagPosition()->wherePivot('position', '>', $tagPosition)->decrement('position');
                }
                $attributeType->standardTagPosition()->detach($standardTag->id);
            }
        }
        return;
    }

    public function searchStandardTags(Request $request)
    {
        try {
            $limit = \config()->get('settings.pagination_limit');
            if ($request->input('type') == 'tag') {
                $tags = StandardTag::where('type', '!=', 'brand')->where('type', '!=', 'module')->where('name', 'like', '%' . request()->input('keyword') . '%')->with(['tags_' => function ($query) use ($request) {
                    $query->select(['id', 'name as text', 'mapped_to']);
                }])->where('status', 'active')->select(['id', 'name as text', 'type'])->paginate($limit);
                return \response()->json([
                    'tags' => $tags,
                ], JsonResponse::HTTP_OK);
            } else if ($request->input('type') == 'attribute') {
                $attributes = Attribute::select(['id', 'name as text',])->where('name', 'like', '%' . request()->input('keyword') . '%')->where('status', 'active')->with(['standardTags' => function ($query) {
                    $query->select('id', 'name as text', 'type')->where('type', 'attribute');
                }])->paginate($limit);
                return \response()->json([
                    'attributes' => $attributes,
                ], JsonResponse::HTTP_OK);
            } else {
                $brandTags = StandardTag::where('type', 'brand')->where('name', 'like', '%' . request()->input('keyword') . '%')->with(['tags_' => function ($query) {
                    $query->select(['id', 'name as text', 'attribute_id', 'mapped_to']);
                }])->where('status', 'active')->select(['id', 'name as text', 'type', 'attribute_id'])->paginate($limit);
                return \response()->json([
                    'brands' => $brandTags,
                ], JsonResponse::HTTP_OK);
            }
        } catch (\Exception $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
