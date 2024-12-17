<?php

namespace Modules\Boats\Http\Controllers\Dashboard\Boat;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttributeTagsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {
        try {
            $product = Product::whereUuid($uuid)->with('tags', function ($query) {
                $query->asTag()->active()->where('is_show', 1)->with('standardTags');
            })->firstOrFail();
            $standardTags = StandardTag::whereType('attribute')->asTag()->with('attribute')->active()->get();
            $allAttributeTags = Arr::collapse([$standardTags]);
            $assignedStandards = $product->standardTags()->with(['tags_' => function ($query) use ($product) {
                $query->whereHas('products', function ($subQuery) use ($product) {
                    $subQuery->where('id', $product->id);
                });
            }])->withPivot(['attribute_id'])->with('attribute')->asTag()->active()->whereType('attribute')->get();
            $assignedTags = $assignedStandards;
            return Inertia::render('Boats::Boats/Settings/AttributeTags', [
                'product' => $product,
                'assignedTag' => $assignedTags,
                // 'allAttributeTags' => $allAttributeTags,
                'attributes' => Attribute::with(['standardTags' => function ($query) {
                }])->active()->whereHas('moduleTags', function ($query) use ($moduleId) {
                    $query->where('id', $moduleId);
                })->get(),
            ]);
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('boats::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('boats::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('boats::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
    public function assignTags($moduleId, $uuid)
    {
        try {
            DB::beginTransaction();
            $product = Product::whereUuid($uuid)->firstOrFail();
            $standardTags = Arr::collapse([$product->standardTags()->where('type', '!=', 'attribute')->pluck('id')->toArray(), collect(json_decode(request()->input('tags')))->pluck('id')->toArray()]);
            $existedAttributeTags = $product->standardTags()->whereType('attribute')->pluck('id')->toArray();
            $removingAttributes = array_diff($existedAttributeTags, collect(json_decode(request()->input('tags')))->pluck('id')->toArray());
            $product->standardTags()->sync($standardTags);
            if (count($removingAttributes) > 0) {
                ProductTagsLevelManager::priorityTwoTags($product, null, $removingAttributes, 'attribute');
            }
            ProductTagsLevelManager::priorityTwoTags($product);
            DB::commit();
            flash('Boat attribute tags updated successfully.', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return \redirect()->back()->withErrors([
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return \redirect()->back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function searchTags(Request $request)
    {
        $standardTags = StandardTag::whereType('attribute')->asTag()->with(['attribute' =>  function ($query) {
            $query->where('id', request()->attribute_id);
        }])->whereHas('attribute', function ($query) {
            $query->where('id', request()->attribute_id);
        })->active()->get();
        return $standardTags;
    }
}
