<?php

namespace Modules\Retail\Jobs;

use App\Models\Tag;
use App\Models\Size;
use App\Models\Color;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Business;
use App\Services\ChatGPTService;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\BusinessOrphanTagsManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Retail\Entities\ProductVariant;

class TagScraper implements ShouldQueue
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;
    protected $totalRecords = 1;
    protected $limit = 50;
    protected $page = 1;
    protected $totalPages = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::alert("Retail Scraper running");

        $businessesNames = Business::whereHas('standardTags', function ($query) {
            $query->where('slug', 'retail');
        })->pluck('name')->toArray();

        $namesList = implode(",", $businessesNames);
        $currentName = '';
        $nameArray = [];

        if (count($businessesNames) > 0) {
            while ($this->totalPages >= $this->page) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer 1b7a1022-c21a-436f-a83d-56a9f249bdaf'
                ])->get('http://178.128.168.240:3020/api/v1/products', [
                    'limit' => $this->limit,
                    'page' => $this->page,
                    'store' => 'messengers gifts'
                ]);

                $this->totalRecords = $response->object()->meta->total;
                $this->totalPages = ceil($this->totalRecords / $this->limit);

                foreach ($response->object()->data as $product) {
                    try {
                        DB::beginTransaction();

                        if (empty($currentName)) {
                            $currentName = $product->business->name;
                            if (!in_array($product->business->name, $nameArray)) {
                                array_push($nameArray, $product->business->name);
                            }
                        }

                        if ($currentName != $product->business->name) {
                            if (!in_array($product->business->name, $nameArray)) {
                                array_push($nameArray, $product->business->name);
                            }
                            $currentName = $product->business->name;
                        }

                        $slug = Str::slug($product->business->name);
                        $business = Business::where('slug', $slug)->first();

                        if ($business) {
                            $moduleTagId = $business->standardTags()->where('slug', 'retail')->first()->id;
                            $levelTwoTag = $business->standardTags()->whereHas('levelTwo')->count() > 0 ?  $business->standardTags()->whereHas('levelTwo')->first()->id : [];
                            $levelThreeCount = $business->standardTags()->whereHas('levelThree')->get()->count();
                            $levelThreeTag = [];
                            if ($levelThreeCount > 1) {
                                $levelThreeTag = $business->standardTags()->where('name', isset($product->product_for) ? $product->product_for : '')->pluck('id')->toArray();
                            } else {
                                $levelThreeTag = $business->standardTags()->pluck('id')->toArray();
                            }
                            $businessTags = $levelThreeTag;
                            array_push($businessTags, $levelTwoTag, $moduleTagId);
                            //Creating or upadting product
                            $newProduct = $this->createOrUpdateProduct($product, $business);
                            if ($newProduct) {
                                // assigning standard tags
                                $newProduct->standardTags()->syncWithoutDetaching($businessTags);

                                //Scrapping product tags
                                $newProductTagsIds = $this->saveAndReturnTags($product, $newProduct, $business); //Top

                                // check if any brand is available
                                $brandTags = $this->saveAndReturnBrandTags($product->brands_tags, $newProduct, $business);

                                //Scrapping variant tags and assigning it to product as orphan tag
                                $productVariants = $this->productVariants($product, $newProduct, $business);

                                //making orphan tags from product description
                                $description = isset($product->description) ? $product->description : null;

                                //making extra tags
                                $extraTags = $this->createExtraTags($product, $newProduct, $business);

                                $tags = Arr::collapse([$newProductTagsIds, $productVariants, $brandTags, $extraTags]);
                                $newProduct->tags()->syncWithoutDetaching($tags);

                                // potentially, all scrapped tags has been assigned at this point
                                // so, lets assign the missing ones with the help of AI
                                $this->assignMissingTags($newProduct);

                                // product images
                                $this->saveProductImages($product, $newProduct);

                                ProductTagsLevelManager::priorityOneTags($newProduct);
                                ProductTagsLevelManager::checkProductTagsLevel($newProduct);

                                // saving business tags
                                BusinessOrphanTagsManager::addOrUpdateBusinessTags($newProduct);
                            }
                        }

                        DB::commit();
                    } catch (\Exception $e) {
                        Log::error([
                            'message' => $e->getMessage(),
                            'line' => $e->getLine(),
                            'file' => $e->getFile(),
                            'store' => isset($business) ? $business->name : '',
                            'product' => isset($product) ? $product->title : ''
                        ]);
                        DB::rollBack();
                    }
                }
                $this->page++;
                Log::alert('Page #' . $this->page . '/' . $this->totalPages);
            }
        }

        Log::alert("Retail Scraper completed");
    }

    /**
     * create new product.
     *
     * @param  $product, $business
     * @return $newProduct
     */
    private function createOrUpdateProduct($product, $business)
    {
        $status = !$product->hasVariants
            ? \str_replace(' ', '_', \strtolower($product->variants[0]->status)) : 'in_stock';
        $newProduct = Product::updateOrCreate(
            ['external_id' => $product->product_id],
            [
                'business_id' => $business->id,
                'name' => $product->title ?? '',
                'description' => $product->description ?? '',
                'price' => $product->price ?? '',
                'type' => isset($product->product_for) ? strtolower($product->product_for) : NULL,
                'is_featured' => Product::where('business_id', $business->id)->count() < 10,
                'stock' => !$product->hasVariants
                    ? (isset($product->variants[0]->quantity) ? $product->variants[0]->quantity : -1) : -1,
                'weight' => !$product->hasVariants && isset($product->variants[0]->weight)
                    ? $product->variants[0]->weight : NULL,
                'weight_unit' => !$product->hasVariants && isset($product->variants[0]->weightUnit)
                    ? $product->variants[0]->weightUnit : NULL,
                'stock_status' => $status,
                'sku' => !$product->hasVariants && isset($product->variants[0]->sku)
                    ? $product->variants[0]->sku : NULL,
            ]
        );
        return $newProduct;
    }

    /**
     * create new variant.
     * @param  $$product, $newProduct
     */
    private function productVariants($product, $newProduct, $business)
    {
        $tagIds = [];
        $tags = [];
        if (isset($product->hasVariants) && $product->hasVariants) {
            if (isset($product->variants_tags_options) && count($product->variants_tags_options) > 0) {
                $tagIds = [];
                foreach ($product->variants_tags_options as $option) {
                    if (!is_null($option->values) && count($option->values) > 0) {
                        foreach ($option->values as $tag) {
                            $savedTag = Tag::where('slug', orphanTagSlug(trim($tag)))->first();
                            if ($savedTag) {
                                $savedTag->update([
                                    'priority' => $savedTag->priority && $savedTag->priority < 4 ? $savedTag->priority : 2,
                                ]);
                                $existInExtra = $savedTag->businesses()->where('id', $business->id)->first();
                                if (!$existInExtra) {
                                    $savedTag->update([
                                        'is_show' => true
                                    ]);
                                }
                                if (!$newProduct->ignoredTags()->whereJsonContains('tags', $savedTag->name)->exists()) {
                                    $tagIds[] = $savedTag->id;
                                    \array_push($tags, $savedTag->name);
                                    $this->mappedTagsAssignment($savedTag->id, $newProduct, $business);
                                }
                            } else {
                                $newTag = Tag::create([
                                    'slug' => trim($tag) == 0 ? '0' : orphanTagSlug(trim($tag)),
                                    'name' => trim($tag),
                                    'priority' => 2,
                                ]);
                                if (!$newProduct->ignoredTags()->whereJsonContains('tags', $newTag->name)->exists()) {
                                    $tagIds[] = $newTag->id;
                                    \array_push($tags, $newTag->name);
                                    $this->mappedTagsAssignment($newTag->id, $newProduct, $business);
                                }
                            }
                        }
                    }
                }
                foreach ($product->variants as $variant) {
                    $size = null;
                    $color = null;
                    //Previous system for storing variants colors and sizes 
                    if ((isset($variant->variant_obj->color) && $variant->variant_obj->color) || (isset($variant->variant_obj->size) && $variant->variant_obj->size)) {
                        if (isset($variant->variant_obj->color) && $variant->variant_obj->color) {
                            $color = Color::updateOrcreate([
                                'business_id' => $newProduct->business->id,
                                'title' => $variant->variant_obj->color
                            ], [])->id;
                        }
                        if (isset($variant->variant_obj->size) && $variant->variant_obj->size) {
                            $size = Size::updateOrcreate([
                                'business_id' => $newProduct->business->id,
                                'title' => $variant->variant_obj->size
                            ], [])->id;
                        }
                    } else {
                        // $newProduct->update(['status' => 'variants_error', 'previous_status' => 'active']);
                        $status = $newProduct->status;
                        $newProduct->status = 'variants_error';
                        $newProduct->previous_status = $status;
                        $newProduct->saveQuietly();
                    }

                    $status = \str_replace(' ', '_', \strtolower($variant->status));
                    ProductVariant::updateOrCreate([
                        'product_id' => $newProduct->id,
                        'external_id' => $variant->variant_id
                    ], [
                        'color_id' => $color,
                        'size_id' => $size,
                        'title' => $variant->title,
                        'sku' => isset($variant->sku) ? $variant->sku : NULL,
                        'price' => $variant->price,
                        'quantity' => isset($variant->quantity)
                            ? $variant->quantity : ($status == 'in_stock' ? -1 : NULL),
                        'weight' => isset($variant->weight)  ? $variant->weight : null,
                        'discount_price' => isset($variant->discount_price)  ? $variant->discount_price : null,
                        'stock_status' => $status == 'in_stock'
                            ? $status : 'out_of_stock'
                    ]);
                }
            }
        }
        return $tagIds;
    }
    /**
     * create new product tags.
     * @param  $scrapeProduct, $newProduct
     */
    private function saveAndReturnTags($scrapeProduct, $product, $business)
    {
        $newTagsId = [];
        $tags = [];
        foreach ($scrapeProduct->tags as $key => $tag) {
            $newTag = Tag::where('slug', orphanTagSlug($tag))->first();
            if (!$newTag) {
                $newTag = Tag::create([
                    'slug' => orphanTagSlug($tag),
                    'name' => $tag,
                    'priority' => 4,
                    'is_category' => true,
                ]);
            } else {
                $newTag->update([
                    'slug' => orphanTagSlug($tag),
                    'name' => $tag,
                    'priority' => 4,
                    'is_category' => true,
                ]);
                $existInExtra = $newTag->businesses()->where('id', $business->id)->first();
                if (!$existInExtra) {
                    $newTag->update([
                        'is_show' => true
                    ]);
                }
            }
            if (!$product->ignoredTags()->whereJsonContains('tags', $newTag->name)->exists()) {
                \array_push($newTagsId, $newTag->id);
                \array_push($tags, $newTag->name);
                //Checking if tag is mapped and already attached to product as standard tag
                $this->mappedTagsAssignment($newTag->id, $product, $business);
            }
        }
        ProductTagsLevelManager::priorityFour($product, $newTagsId);
        return $newTagsId;
    }

    /**
     * create new brand tags.
     * @param  $brandTags, $newProduct
     */
    private function saveAndReturnBrandTags($brandTags, $product, $business)
    {
        $newTagsId = [];
        if (\count($brandTags) > 0) {
            foreach ($brandTags as $tag) {
                $newTag = Tag::where('slug', orphanTagSlug($tag))->first();
                if ($newTag) {
                    $newTag->update([
                        'name' => $tag,
                        'priority' => 1,
                        'is_show' => \true
                    ]);
                    $existInExtra = $newTag->businesses()->where('id', $business->id)->first();
                    if (!$existInExtra) {
                        $newTag->update([
                            'is_show' => true
                        ]);
                    }
                } else {
                    $newTag = Tag::create([
                        'slug' => orphanTagSlug($tag),
                        'name' => $tag,
                        'priority' => 1
                    ]);
                }
                if (!$product->ignoredTags()->whereJsonContains('tags', $newTag->name)->exists()) {
                    \array_push($newTagsId, $newTag->id);
                    $this->mappedTagsAssignment($newTag->id, $product, $business);
                }
            }
            ProductTagsLevelManager::priorityThree($product, $newTagsId, false);
        }
        return $newTagsId;
    }

    private function checkTagIsMapped($tag, $product)
    {
        if ($tag && $tag->mapped_to) {
            $tags = [];
            $product = Product::where('external_id', $product->product_id)->first();
            if (!(DB::table('product_standard_tag')
                ->where('product_id', $product->id)
                ->where('standard_tag_id', $tag->mapped_to)->first())) {
                array_push($tags, $tag->mapped_to);
                $product->standardTags()->syncWithoutDetaching($tags);
            }
        }
    }

    /**
     * create product images.
     *
     * @param  $product, $newProduct
     * @return boolean
     */
    private function saveProductImages($product, $newProduct)
    {
        $newProduct->media()->delete();
        foreach ($product->images as $index => $image) {
            if ($index >= 7) {
                break;
            }
            if (\env('APP_ENV') == 'local') {
                $newProduct->media()->create([
                    'path' => $image->src,
                    'type' => 'image',
                    'is_external' => 1
                ]);
            } else {
                $imageExists = $this->checkRemoteFile($image->src);
                if ($imageExists) {
                    $newProduct->media()->create([
                        'path' => $image->src,
                        'type' => 'image',
                        'is_external' => 1
                    ]);
                }
            }
        }
        return true;
    }

    /**
     * check product image url.
     *
     * @param  $url
     * @return boolean
     */
    private function checkRemoteFile($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code == 200 ? \true : \false;
    }

    private function mappedTagsAssignment($tag_id, $product, $business)
    {
        $tag = Tag::find($tag_id);
        if ($tag) {
            $mappedStandardTags = $tag->standardTags_()->get();
            if ($mappedStandardTags->count() > 0) {
                foreach ($mappedStandardTags as $standardTag) {
                    $existInBothLevels = $standardTag->whereHas('levelThree')->whereHas('tagHierarchies')->where('id', $standardTag->id)->first();
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
                        $levelThreeTag = TagHierarchy::where('L3', $standardTag->id)->first();
                        if ($levelThreeTag) {
                            $productLevelThreeTag = $product->standardTags()->whereHas('levelThree')->first();
                            if (!$productLevelThreeTag) {
                                $checkBusinessLevelThreeTags = $business->standardTags()->where('id', $standardTag->id)->whereHas('levelThree')->first();
                                if ($checkBusinessLevelThreeTags) {
                                    $product->standardTags()->syncWithOutDetaching($standardTag->id);
                                }
                            }
                        } else {
                            $product->standardTags()->syncWithoutDetaching($standardTag->id);
                        }
                    }
                    if ($standardTag->type == 'attribute') {
                        ProductTagsLevelManager::priorityTwoTags($product);
                    } else if ($standardTag->priority == 4) {
                        $tags[] = $tag->id;
                        ProductTagsLevelManager::priorityFour($product, $tags);
                    }
                }
            } else {
                $standardTag = StandardTag::where('name', $tag->name)->first();
                if ($standardTag) {
                    $tag->update([
                        'priority' => $standardTag->priority,
                        'is_show' => \true
                    ]);
                    $tag->standardTags_()->syncWithoutDetaching($standardTag->id);
                    $existInBothLevels = $standardTag->whereHas('levelThree')->whereHas('tagHierarchies')->where('id', $standardTag->id)->first();
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
                        $levelThreeTag = TagHierarchy::where('L3', $standardTag->id)->first();
                        if ($levelThreeTag) {
                            $productLevelThreeTag = $product->standardTags()->whereHas('levelThree')->first();
                            if (!$productLevelThreeTag) {
                                $levelThreeTags = $business->standardTags()->whereHas('levelThree')->pluck('id')->toArray();
                                if (in_array($standardTag->id, $levelThreeTags)) {
                                    $product->standardTags()->syncWithoutDetaching($standardTag->id);
                                }
                            }
                        } else {
                            $product->standardTags()->syncWithoutDetaching($standardTag->id);
                        }
                    }
                    if ($standardTag->type == 'attribute') {
                        ProductTagsLevelManager::priorityTwoTags($product);
                    }
                } else {
                    $tags[] = $tag->id;
                    ProductTagsLevelManager::priorityFour($product, $tags);
                }
            }
        }
    }

    private function createExtraTags($product, $newProduct, $business)
    {
        $tagIds = [];
        $tags = [];
        if ($product && isset($product->extra_tags)) {
            foreach ($product->extra_tags as $key => $tag) {
                $savedTag = Tag::whereSlug(orphanTagSlug($tag))->first();
                if ($savedTag) {
                    $savedTag->update([
                        'priority' => $savedTag->priority && $savedTag->priority < 4 ? $savedTag->priority : 4,
                    ]);
                    if (!$newProduct->ignoredTags()->whereJsonContains('tags', $savedTag->name)->exists()) {
                        $tagIds[] = $savedTag->id;
                        $this->mappedTagsAssignment($savedTag->id, $newProduct, $business);
                    }
                } else {
                    $savedTag = Tag::create([
                        'slug' => orphanTagSlug($tag),
                        'name' => $tag,
                        'priority' => 4,
                        'is_show' => false
                    ]);
                    if (!$newProduct->ignoredTags()->whereJsonContains('tags', $savedTag->name)->exists()) {
                        $tagIds[] = $savedTag->id;
                        $this->mappedTagsAssignment($savedTag->id, $newProduct, $business);
                    }
                }
                if (!$newProduct->ignoredTags()->whereJsonContains('tags', $savedTag->name)->exists()) {
                    \array_push($tags, $savedTag);
                }
            }
            ProductTagsLevelManager::priorityFour($newProduct, $tagIds);
        }
        return $tagIds;
    }

    private function assignMissingTags($newProduct)
    {
        $productData = [
            'title' => $newProduct->name,
            'description' => $newProduct->description
        ];
        Log::alert(\json_encode($productData));
        $chatGPTService = new ChatGPTService();
        $productL1Tag = $newProduct->standardTags()->whereHas('levelOne')->first();
        $productL2Tag = $newProduct->standardTags()->whereHas('levelTwo', function ($query) use ($productL1Tag) {
            $query->where('L1', $productL1Tag->id);
        })->first();
        $productL3Tag = $newProduct->standardTags()->whereHas('levelThree', function ($query) use ($productL1Tag) {
            $query->where('L1', $productL1Tag);
        })->first();
        $productL4Tags = [];

        // if level two tag is missing
        if (empty($productL2Tag)) {
            // if the product have level three tag, then we use it to get level two tags
            if (!empty($productL3Tag)) {
                $l2Tags = StandardTag::whereHas('levelTwo', function ($query) use ($productL3Tag, $productL1Tag) {
                    $query->where('L3', $productL3Tag->id)->where('L1', $productL1Tag->id);
                })->get()->toArray();
            }

            // otherwise, we use level one tag to get level two tags
            else {
                // get all the level two tags which has been specified by the business
                $l2Tags = $newProduct->business->standardTags()->whereHas('levelTwo', function ($query) use ($productL1Tag) {
                    $query->where('L1', $productL1Tag);
                })->get()->toArray();
                // if there is no level two tag from the business
                // then, we get all the level two tags which are children of the level one tag
                if (count($l2Tags) <= 0) {
                    $l2Tags = StandardTag::whereHas('levelTwo', function ($query) use ($productL1Tag) {
                        $query->where('L1', $productL1Tag->id);
                    })->get()->toArray();
                }
            }

            // if we one have only 1 level two tag, then we assign that
            if (count($l2Tags) == 1) {
                $newProduct->standardTags()->syncWithoutDetaching($l2Tags[0]['id']);
            }

            // otherwise, we ask AI to suggest a tag from the given list
            else {
                $aiSuggestedTag = $chatGPTService->getCategory(array_column($l2Tags, 'name'), $productData);
                Log::alert('L2 Tags');
                Log::alert($aiSuggestedTag);
                Log::alert(array_column($l2Tags, 'name'));
                $aiSuggestedTagId = $this->getTagIdByName($l2Tags, $aiSuggestedTag);
                if ($aiSuggestedTagId) {
                    $newProduct->standardTags()->syncWithoutDetaching($aiSuggestedTagId);
                }
            }
            $productL2Tag = StandardTag::find(isset($aiSuggestedTagId) ? $aiSuggestedTagId : $l2Tags[0]['id']);
        }

        // // if level thee tag is missing
        if (empty($productL3Tag)) {

            $productL4Tags = $newProduct->standardTags()->whereHas('tagHierarchies', function ($query) use ($productL1Tag, $productL2Tag) {
                $query->where('level_type', 4)->where('L1', $productL1Tag->id)
                    ->where('L2', $productL2Tag->id);
            })->get();

            // if the product have level four tags, then we use these to get level three tags
            if ($productL4Tags->count() > 0) {
                $l3Tags = StandardTag::whereHas('levelThree', function ($query) use ($productL4Tags, $productL1Tag, $productL2Tag) {
                    $query->where('L1', $productL1Tag->id);
                    $query->where('L2', $productL2Tag->id);
                    $query->whereHas('standardTags', function ($query) use ($productL4Tags) {
                        $query->whereIn('id', $productL4Tags->pluck('id'));
                    });
                })->get()->toArray();

                if (count($l3Tags) <= 0) {
                    $l3Tags = StandardTag::whereHas('levelThree', function ($query) use ($productL1Tag, $productL2Tag) {
                        $query->where('L1', $productL1Tag->id);
                        $query->where('L2', $productL2Tag->id);
                    })->get()->toArray();

                    $productAssignedL4Tags = $newProduct->standardTags()
                        ->whereHas('tagHierarchies', function ($query) use ($productL1Tag, $productL2Tag) {
                            $query->where('L1', $productL1Tag->id)->where('L2', $productL2Tag->id);
                            $query->where('level_type', 4);
                        })->get()->toArray();
                    if (count($productAssignedL4Tags) > 0) {
                        $newProduct->standardTags()->detach(array_column($productAssignedL4Tags, 'id'));
                        $productL4Tags = [];
                    }
                }
            }

            // otherwise, we use level two tag to get level three tags
            else {
                // get all the level three tags which has been specified by the business
                $l3Tags = $newProduct->business->standardTags()->whereHas('levelThree', function ($query) use ($productL1Tag, $productL2Tag) {
                    $query->where('L1', $productL1Tag->id);
                    $query->where('L2', $productL2Tag->id);
                })->get()->toArray();

                // if there is no level three tag from the business
                // then, we get all the level three tags which are children of the level two tag
                if (count($l3Tags) <= 0) {
                    // get all the level two tags which has been specified by the business
                    $l2TagsIds = $newProduct->business->standardTags()->whereHas('levelTwo', function ($query) use ($productL1Tag) {
                        $query->where('L1', $productL1Tag->id);
                    })->pluck('id');

                    // if there is no level two tag from the business
                    // then, we get all the level two tags which are children of the level one tag
                    if (count($l2TagsIds) <= 0) {
                        $l2TagsIds = $newProduct->standardTags()->whereHas('levelTwo', function ($query) use ($productL1Tag) {
                            $query->where('L1', $productL1Tag->id);
                        })->pluck('id');

                        if (count($l2TagsIds) <= 0) {
                            $l2TagsIds = StandardTag::whereHas('levelTwo', function ($query) use ($productL1Tag) {
                                $query->where('L1', $productL1Tag->id);
                            })->pluck('id');
                        }
                    }

                    // now we get all the level three tags which are children of the level two tags
                    $l3Tags = StandardTag::whereHas('levelThree', function ($query) use ($l2TagsIds, $productL1Tag) {
                        $query->whereIn('L2', $l2TagsIds)->where('L1', $productL1Tag->id);
                    })->get()->toArray();
                }
            }
            if (count($l3Tags) == 1) {
                $newProduct->standardTags()->syncWithoutDetaching($l3Tags[0]['id']);
            } else {
                $aiSuggestedTag = $chatGPTService->getCategory(array_column($l3Tags, 'name'), $productData);
                Log::alert('L3 Tags');
                Log::alert($aiSuggestedTag);
                Log::alert(array_column($l3Tags, 'name'));
                $aiSuggestedTagId = $this->getTagIdByName($l3Tags, $aiSuggestedTag);
                if ($aiSuggestedTagId) {
                    $newProduct->standardTags()->syncWithoutDetaching($aiSuggestedTagId);
                }
            }

            $productL3Tag = StandardTag::find(isset($aiSuggestedTagId) ? $aiSuggestedTagId : $l3Tags[0]['id']);
        }

        // // if level four tags are missing
        if (count($productL4Tags) <= 0) {

            // at this point level three tag should be attached to the product, so we get that
            // $productL3Tag = $newProduct->standardTags()->whereHas('levelThree')->first();

            // get all the level four tags which are children of the level three tag
            if ($productL3Tag) {
                $l4Tags = StandardTag::whereHas('tagHierarchies', function ($query) use ($productL3Tag, $productL2Tag, $productL1Tag) {
                    $query->where('level_type', 4)->where('L3', $productL3Tag->id)
                        ->where('L2', $productL2Tag->id)->where('L1', $productL1Tag->id);
                })->get()->toArray();
                $aiSuggestedTag = $chatGPTService->getCategory(array_column($l4Tags, 'name'), $productData);
                Log::alert('L4 Tags');
                Log::alert($aiSuggestedTag);
                Log::alert(array_column($l4Tags, 'name'));
                $aiSuggestedTagId = $this->getTagIdByName($l4Tags, $aiSuggestedTag);
                if ($aiSuggestedTagId) {
                    $newProduct->standardTags()->syncWithoutDetaching($aiSuggestedTagId);
                }
            }
        }
    }

    private function getTagIdByName($categories, $name)
    {
        $key = array_search($name, array_column($categories, 'name'));
        return !empty($key)
            ? $categories[$key]['id']
            : (gettype($key) == "integer" ? $categories[$key]['id'] : false);
    }
}
