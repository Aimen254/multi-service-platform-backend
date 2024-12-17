<?php

namespace Modules\Classifieds\Http\Controllers\Dashboard\Classifieds;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClassifiedAttributeTagsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {
        try {
            $attributes = Attribute::with(['standardTags' => function ($query) {
            }])->active()->whereHas('moduleTags', function ($query) use ($moduleId) {
                $query->where('id', $moduleId);
            })->get();

            $product = Product::whereUuid($uuid)->firstOrFail();

            $assignedStandards = $product->standardTags()->with(['tags_' => function ($query) use ($product) {
                $query->whereHas('products', function ($subQuery) use ($product) {
                    $subQuery->where('id', $product->id);
                });
            }])->withPivot(['attribute_id'])->with('attribute')->asTag()->active()->whereType('attribute')->get();

            return Inertia::render('Classifieds::Classifieds/Settings/AttributeTags', [
                'attributes' => $attributes,
                'assignedAttibuteTags' => $assignedStandards,
                'product' => $product
            ]);
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function assignTags($moduleId, $uuid)
    {
        try {
            DB::beginTransaction();
            $product = Product::whereUuid($uuid)->firstOrFail();
            $standardTags = collect(json_decode(request()->input('tags')))->toArray();
            foreach ($standardTags as $standardTag) {
                $product->standardTags()->syncWithoutDetaching($standardTag->id);
            }
            // removed tags
            $removedTags = collect(json_decode(request()->input('removedTags')))->toArray();
            foreach ($removedTags as $removedTag) {
                $product->standardTags()->detach($removedTag->id);
            }

            $removingAttributes = collect(json_decode(request()->input('removedTags')))->pluck('id')->toArray();
            if (count($removingAttributes) > 0) {
                ProductTagsLevelManager::priorityTwoTags($product, null, $removingAttributes, 'attribute');
            }
            ProductTagsLevelManager::priorityTwoTags($product);
            DB::commit();
            flash('Marketplace attribute tags updated successfully.', 'success');
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

    // search attribute tags
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
