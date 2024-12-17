<?php

namespace Modules\Events\Http\Controllers\Dashboard\Events;


use Inertia\Inertia;

use App\Models\Product;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class EventAttributeController extends Controller
{
    /*
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
           return Inertia::render('Events::Settings/Attributes', [
               'attributes' => $attributes,
               'assignedAttibuteTags' => $assignedStandards,
               'product' => $product
           ]);
       } catch (\Exception $e) {
           flash($e->getMessage(), 'danger');
           return \back();
       }
   }

   public function assignTags($moduleId, $uuid)
   {
       try {
           DB::beginTransaction();
           $product = Product::whereUuid($uuid)->firstOrFail();
           $standardTags = collect(json_decode(request()->input('tags')))->toArray();
           foreach ($standardTags as $standardTag) {
               if (isset($standardTag->pivot) && $standardTag->pivot->attribute_id) {
                   $attribute = Attribute::where('id', $standardTag->pivot->attribute_id)->firstOrFail();
                   if ($attribute) {
                       $isExist = $product->standardTags()
                           ->withPivot(['attribute_id'])
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
                   } else {
                       $product->standardTags()->syncWithoutDetaching($standardTag->id);
                   }
               } else {
                   $isExist = $product->standardTags()
                       ->withPivot(['attribute_id'])
                       ->wherePivot('attribute_id', $standardTag->attribute[0]->id)
                       ->wherePivot('standard_tag_id', $standardTag->id)
                       ->where('product_id', $product->id)
                       ->first();
                   if (!$isExist) {
                       $product->standardTags()->attach($standardTag->id, [
                           'attribute_id' => $standardTag->attribute[0]->id,
                           'product_id' => $product->id
                       ]);
                   } else {
                       $product->standardTags()->syncWithoutDetaching($standardTag->id);
                   }
               }
           }

           // removed tags
           $removedTags = collect(json_decode(request()->input('removedTags')))->toArray();
           foreach ($removedTags as $removedTag) {
               if ($removedTag->pivot->attribute_id) {
                   $attribute = Attribute::where('id', $removedTag->pivot->attribute_id)->firstOrFail();
                   if ($attribute) {
                       $product->standardTags()
                           ->wherePivot('attribute_id', $attribute->id)
                           ->wherePivot('standard_tag_id', $removedTag->id)
                           ->where('product_id', $product->id)
                           ->detach();
                   } else {
                       $product->standardTags()->detach($removedTag->id);
                   }
               } else {
                   $product->standardTags()->detach($removedTag->id);
               }
           }
           $removingAttributes = collect(json_decode(request()->input('removedTags')))->pluck('id')->toArray();
           if (count($removingAttributes) > 0) {
               ProductTagsLevelManager::priorityTwoTags($product, null, $removingAttributes, 'attribute');
           }
           ProductTagsLevelManager::priorityTwoTags($product);
           DB::commit();
           flash('Events attribute tags updated successfully.', 'success');
           return \back();
       } catch (ModelNotFoundException $e) {
           DB::rollBack();
           return \back()->withErrors([
               'message' => $e->getMessage()
           ]);
       } catch (\Exception $e) {
           DB::rollBack();
           return \back()->withErrors([
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
