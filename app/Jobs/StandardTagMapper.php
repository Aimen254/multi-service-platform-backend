<?php

namespace App\Jobs;

use App\Models\Tag;
use App\Models\Product;;

use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class StandardTagMapper implements ShouldQueue
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $tagIds;
    protected $request;
    protected $removingTagIds;
    public function __construct($tagIds, $request, $removingTagIds)
    {
        $this->tagIds = $tagIds;
        $this->request = $request->all();
        $this->removingTagIds = $removingTagIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->request['type'] == 'attribute') {
            $attribute = Attribute::findOrFail($this->request['attribute']);
            $tags = $attribute->standardTags()->whereIn('id', $this->removingTagIds)->get();
            foreach ($tags as $tag) {
                $tag->attribute()->detach($this->request['attribute']);
                $existInOtherAttribute = $tag->attribute()->first();
                $tag->type = $existInOtherAttribute ? 'attribute' : 'product';
                $tag->priority = $existInOtherAttribute ? 2 : 4;
                $tag->saveQuietly();
                ProductTagsLevelManager::orphanTagsPriority($tag);
                $products = $tag->productTags()->get();
                if ($products->count() > 0) {
                    foreach ($products as $product) {
                        ProductTagsLevelManager::priorityTwoTags($product, null, $this->removingTagIds, 'attribute');
                    }
                }
            }
            foreach ($this->tagIds as $id) {
                $standardTag = StandardTag::findOrFail($id);
                $standardTag->type = 'attribute';
                $standardTag->priority = 2;
                $standardTag->saveQuietly();
                $standardTag->attribute()->syncWithoutDetaching($this->request['attribute']);
                ProductTagsLevelManager::orphanTagsPriority($standardTag);
                $products = $standardTag->productTags()->get();
                if ($products->count() > 0) {
                    foreach ($products as $product) {
                        ProductTagsLevelManager::priorityTwoTags($product);
                    }
                }
            }
        } else {
            $id = $this->request['standardTag'] ? $this->request['standardTag'] : $this->request['brand'];
            $standardTag = StandardTag::findOrFail($id);
            $existInHierarchy = StandardTag::where('id', $id)->where(function ($query) {
                $query->whereHas('levelOne')
                    ->orWhereHas('levelTwo')
                    ->orWhereHas('levelThree')
                    ->orWhereHas('tagHierarchies');
            })->first();
            $tags = $standardTag->tags_()->whereIn('id', $this->removingTagIds)->get();
            foreach ($tags as $tag) {
                $productIds = $tag->products()->pluck('id');
                $standardTag->tags_()->detach($tag->id);
                $standardTag->productTags()->detach($productIds);
                foreach ($productIds as $id) {
                    $product = Product::findOrFail($id);
                    ProductTagsLevelManager::checkProductTagsLevel($product);
                    if ($existInHierarchy) {
                        ProductTagsLevelManager::priorityOneTags($product, $this->removingTagIds, 'mapping');
                    } else if ($standardTag->type == 'attribute') {
                        ProductTagsLevelManager::priorityTwoTags($product, null, $this->removingTagIds, 'mapping', $standardTag);
                    } else if ($this->request['type'] == 'brand') {
                        ProductTagsLevelManager::priorityThree($product, $this->removingTagIds, true, null, $standardTag);
                    } else {
                        ProductTagsLevelManager::priorityFour($product, $tags->pluck('id')->toArray(), false, true);
                    }
                }
            }
            $this->attachStandardTags($standardTag, $existInHierarchy);
        }
    }

    public function getTags($tagIds)
    {
        $tags = [];
        foreach ($tagIds as $id) {
            $tag = Tag::findOrFail($id);
            array_push($tags, $tag);
        }
        return $tags;
    }

    public function attachStandardTags($standardTag, $existInHierarchy)
    {
        foreach ($this->tagIds as $id) {
            $tag = Tag::findOrFail($id);
            $standardTag->tags_()->syncWithOutDetaching($tag->id);
            $products = $tag->products()->withPivot('type')->get();
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
                    if ($product->pivot->type) {
                        $attribute = $standardTag->attribute()->where('slug', $product->pivot->type)->first();
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
                    } else {
                        $product->standardTags()->syncWithOutDetaching($standardTag->id);
                    }
                }
                ProductTagsLevelManager::checkProductTagsLevel($product);
                if ($existInHierarchy) {
                    ProductTagsLevelManager::priorityOneTags($product, $this->removingTagIds, 'mapping');
                } else if ($standardTag->type == 'attribute') {
                    ProductTagsLevelManager::priorityTwoTags($product, null, $this->removingTagIds, 'mapping');
                } else if ($this->request['type'] == 'brand') {
                    ProductTagsLevelManager::priorityThree($product, $this->tagIds, false, null);
                } else {
                    ProductTagsLevelManager::priorityFour($product, $this->tagIds);
                }
            }
        }
    }
}
