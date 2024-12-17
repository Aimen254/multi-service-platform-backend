<?php

namespace Modules\Retail\Http\Controllers\Dashboard\Business;

use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExtraTagsController extends Controller
{
    protected $business;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $business = getBusinessDetails(Route::current()->parameters['business_uuid']);
        $this->business = $business;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId)
    {
        $business = $this->business;
        $limit = \config()->get('settings.pagination_limit');
        $extraTags = Tag::whereHas('businesses', function ($query) use ($business) {
            $query->where('id', $business->id)->where('is_extra', true);
        })->whereDoesntHave('standardTags_')->where(function ($query) {
            if (request()->keyword) {
                $keyword = request()->keyword;
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('slug', 'like', '%' . $keyword . '%');
            }
        })->active()->orderBy('id', 'desc')->paginate($limit);
        $standardTags = StandardTag::asTag()->whereIn('type', ['product', 'attribute', 'brand'])->with('attribute')->get();
        $attributesList  = Attribute::where('status', 'active')->get();
        return Inertia::render('Retail::Business/ExtraTags/Index', [
            'extraTagsList' => $extraTags,
            'attributesList' => $attributesList,
            'standardTagsList' => $standardTags,
            'searchedKeyword' => request()->keyword,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('retail::create');
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
        return view('retail::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('retail::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $moduleId, $businessd, $id)
    {
        try {
            DB::beginTransaction();
            $mapped_id = null;
            $priority = null;
            $tag = Tag::findOrFail($id);
            $tagId = [$tag->id];
            $selectedTags = $request->has('tags') ? $request->input('tags') : null;
            foreach ($selectedTags as $key => $sTag) {
                $standardTag = null;
                switch ($sTag['tag_type']) {
                    case 'product':
                    case 'attribute':
                        switch (true) {
                            case isset($sTag['standard_tag']):
                                $standardTag = StandardTag::findOrFail($sTag['standard_tag']);
                                $priority = $standardTag->priority;
                                $mapped_id = $sTag['standard_tag'];
                                break;
                        }
                        break;
                    case 'brand':
                        $standardTag = StandardTag::where(function ($query) use ($sTag, $tag) {
                            if (isset($sTag['standard_tag'])) {
                                $query->where('id', $sTag['standard_tag']);
                            } else {
                                $query->where('slug', $tag->slug)->where('type', $sTag['tag_type']);
                            }
                        })->first();
                        $standardTag = $standardTag ? $standardTag : $tag->standardTags_()->create([
                            'name' => $tag->name,
                            'type' => $sTag['tag_type'],
                            'priority' => 2
                        ]);
                        $priority = $standardTag->priority;
                        $mapped_id = $standardTag->id;
                        break;
                }
                $tag->update([
                    'priority' => $priority ?: $tag->priority,
                ]);
                $tag->standardTags_()->syncWithoutDetaching($mapped_id);

                // handling standrd tag mapping with products
                if ($standardTag) {
                    $products = $tag->products()->withPivot('type')->get();
                    $existInHierarchy = StandardTag::where('id', $standardTag->id)->where(function ($query) {
                        $query->whereHas('levelOne')
                            ->orWhereHas('levelTwo')
                            ->orWhereHas('levelThree')
                            ->orWhereHas('tagHierarchies');
                    })->first();

                    $existInBothLevels = $standardTag->whereHas('levelThree')->whereHas('tagHierarchies')->where('id', $standardTag->id)->first();

                    foreach ($products as $product) {
                        $business = $product->business()->first();
                        if ($existInHierarchy) {
                            if ($existInBothLevels) {
                                $productLevelThreeTag = $product->standardTags()->where('id', '<>', $standardTag->id)->whereHas('levelTHree')->first();
                                if ($productLevelThreeTag) {
                                    $exsistInlevelFour = $standardTag->whereHas('tagHierarchies', function ($query) use ($productLevelThreeTag) {
                                        $query->where('L3', $productLevelThreeTag->id);
                                    })->where('id', $standardTag->id)->first();
                                    if ($exsistInlevelFour) {
                                        $product->standardTags()->syncWithOutDetaching($standardTag->id);
                                    }
                                } else {
                                    $checkBusinessLevelThreeTags = $business->standardTags()->where('id', $standardTag->id)->whereHas('levelThree')->first();
                                    if ($checkBusinessLevelThreeTags) {
                                        $product->standardTags()->syncWithOutDetaching($standardTag->id);
                                    }
                                }
                            } else {
                                $existInLevelThree = $standardTag->whereHas('levelThree')->where('id', $standardTag->id)->first();
                                if ($existInLevelThree) {
                                    $productLevelThreeTag = $product->standardTags()->whereHas('levelThree')->first();
                                    if (!$productLevelThreeTag) {
                                        $checkBusinessLevelThreeTags = $business->standardTags()->where('id', $standardTag->id)->whereHas('levelThree')->first();
                                        if ($checkBusinessLevelThreeTags) {
                                            $product->standardTags()->syncWithOutDetaching($standardTag->id);
                                        }
                                    }
                                } else {
                                    $product->standardTags()->syncWithOutDetaching($standardTag->id);
                                }
                            }
                        } else {
                            if ($sTag['attribute_id']) {
                                $attribute = Attribute::where('id', $sTag['attribute_id'])->first();
                                if ($attribute && ($attribute->slug == 'interior-color' || $attribute->slug == 'exterior-color')) {
                                    if ($attribute->slug == $product->pivot->type) {
                                        $isExist = $product->standardTags()->withPivot(['attribute_id'])
                                            ->wherePivot('attribute_id', $attribute->id)
                                            ->wherePivot('standard_tag_id', $standardTag->id)
                                            ->where('product_id', $product->id)
                                            ->first();
                                        if (!$isExist) {
                                            $product->standardTags()->attach($standardTag->id, [
                                                'attribute_id' => $attribute->id,
                                                'product_id' => $product->id
                                            ]);
                                        }
                                    }
                                } else {
                                    $product->standardTags()->syncWithOutDetaching($standardTag->id);
                                }
                            } else {
                                $product->standardTags()->syncWithOutDetaching($standardTag->id);
                            }
                        }
                        ProductTagsLevelManager::checkProductTagsLevel($product);
                        if ($existInHierarchy) {
                            ProductTagsLevelManager::priorityOneTags($product);
                        } else if ($standardTag->type == 'attribute') {
                            ProductTagsLevelManager::priorityTwoTags($product);
                        } else if ($standardTag->type == 'brand') {
                            ProductTagsLevelManager::priorityThree($product, $tagId, false);
                        }
                    }
                }
            }

            DB::commit();
            flash($mapped_id ? 'Tag mapped succesfully' : 'Tag updated successfully', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this tag', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
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

    public function cloneTag($moduleId, $businessid, $id)
    {
        try {
            $tag = Tag::find($id);
            $standardTag = StandardTag::updateOrCreate(['slug' => $tag->slug], [
                'name' => $tag->name,
                'type' => request()->has('standardTagType') ? request()->input('standardTagType') : null,
                'priority' => request()->input('standardTagType') == 'attribute' ? 2 : (request()->input('standardTagType') == 'brand' ? 3 : 4)
            ]);

            if (request()->has('standardTagType') && request()->has('standardTagType') == 'attribute') {
                $attribute = Attribute::findOrFail(request()->attributeId);
                $standardTag->attribute()->syncWithoutDetaching(request()->attributeId);
                $max = $attribute->standardTagPosition()->max('position');
                $maxPosition = $max + 1;
                $attribute->standardTagPosition()->sync([$standardTag->id => ['position' => $maxPosition]], false);
                $tag->standardTags_()->syncWithoutDetaching($standardTag->id);
            } else {
                $tag->standardTags_()->syncWithoutDetaching($standardTag->id);
            }
            // $tagIds = $tag->products()->pluck('id');
            // $standardTag->productTags()->syncWithoutDetaching($tagIds);
            //priority update
            if ($tag->products()->count() > 0) {
                $products = $tag->products()->withPivot('type')->get();
                foreach ($products as $key => $product) {
                    if (request()->has('standardTagType') && request()->input('standardTagType') == 'brand') {
                        $product->syncWithoutDetaching($standardTag->id);
                        ProductTagsLevelManager::priorityThree($product, [$id]);
                    } else if (request()->has('standardTagType') && request()->input('standardTagType') == 'attribute') {
                        if ($attribute && ($attribute->slug == 'interior-color' || $attribute->slug == 'exterior-color')) {
                            if ($attribute->slug == $product->pivot->type) {
                                $isExist = $product->standardTags()->withPivot(['attribute_id'])
                                    ->wherePivot('attribute_id', $attribute->id)
                                    ->wherePivot('standard_tag_id', $standardTag->id)
                                    ->where('product_id', $product->id)
                                    ->first();
                                if (!$isExist) {
                                    $product->standardTags()->attach($standardTag->id, [
                                        'attribute_id' => $attribute->id,
                                        'product_id' => $product->id
                                    ]);
                                }
                            }
                        } else {
                            $product->standardTags()->syncWithOutDetaching($standardTag->id);
                        }
                        ProductTagsLevelManager::priorityTwoTags($product);
                    } else {
                        $product->syncWithoutDetaching($standardTag->id);
                    }
                }
            }
            flash('Tag is marked as standard tag', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this tag', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }
}
