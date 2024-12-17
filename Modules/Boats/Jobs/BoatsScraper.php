<?php

namespace Modules\Boats\Jobs;

use App\Models\Tag;
use App\Models\Product;
use App\Models\Business;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ModuleSessionManager;
use Illuminate\Support\Facades\Http;
use PhpParser\PrettyPrinter\Standard;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\BusinessOrphanTagsManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BoatsScraper implements ShouldQueue
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        Log::alert("Boats Scraper running");
        $businessesNames = Business::whereHas('standardTags', function ($query) {
            $query->where('slug', 'boats');
        })->pluck('name')->toArray();
        $namesList = implode(",", $businessesNames);
        if (count($businessesNames) > 0) {
            while ($this->totalPages >= $this->page) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer 1b7a1022-c21a-436f-a83d-56a9f249bdaf'
                ])->get('http://178.128.168.240:3020/api/v1/products', [
                    'limit' => $this->limit,
                    'page' => $this->page,
                    'store' => $namesList
                ]);

                $this->totalRecords = $response->object()->meta->total;
                $this->totalPages = ceil($this->totalRecords / $this->limit);

                foreach ($response->object()->data as $product) {
                    try {
                        DB::beginTransaction();

                        $slug = Str::slug($product->business->name);
                        $business = Business::where('slug', $slug)->first();

                        if ($business) {
                            $newProduct = $this->createOrUpdateProduct($product, $business);

                            if ($newProduct) {
                                //  product images
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
                Log::alert('Page #' . $this->page);
            }
        }
        Log::alert("Boats Scraper completed");
    }

    /**
     * create new product.
     *
     * @param  $product, $business
     * @return $newProduct
     */
    private function createOrUpdateProduct($product, $business)
    {
        ModuleSessionManager::setModule('boats', \false);
        $newProduct = Product::updateOrCreate(
            ['external_id' => $product->product_id],
            [
                'business_id' => $business->id,
                'name' => $product->title ?? '',
                'description' => $product->description ?? '',
                'price' => $product->price ?? '',
                'status' => $product->status ? $product->status : 'active',
            ]
        );
        // generation of hierarchy dynamically
        $createdHierarchy = $this->makeHierarchy($product, $business, $newProduct);
        $maker = $createdHierarchy[0];
        $model = $createdHierarchy[1];

        // make attributes of boats
        $this->makeAttributes($product, $business, $newProduct);

        $newProduct->boat()->updateOrCreate(['product_id' => $newProduct->id], [
            'type' => $product->type,
            'maker_id' => isset($maker) ? $maker->id : null,
            'model_id' => isset($model) ? $model->id : null,
            'year' => $product->year,
            'stock_no' => $product->stock,
            'vin' => $product->vin,
            'sellers_notes' => $product->sellerNotes,
            'engine' => $product->engine_size,
            // 'fuel_type' => $product->fuel_type
        ]);

        return $newProduct;
    }



    private function saveProductImages($product, $newProduct)
    {
        $newProduct->media()->delete();
        foreach ($product->images as $index => $image) {
            if ($index >= 7) {
                break;
            }
            if (
                \env('APP_ENV') == 'local'
            ) {
                $newProduct->media()->create([
                    'path' => $image->image,
                    'type' => 'image',
                    'is_external' => 1
                ]);
            } else {
                $imageExists = $this->checkRemoteFile($image->image);
                if ($imageExists) {
                    $newProduct->media()->create([
                        'path' => $image->image,
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

    public function makeHierarchy($product, $business, $newProduct)
    {
        $module = StandardTag::where('slug', 'boats')->first();
        $manuefacturer = StandardTag::where('slug', 'manufacturers')->first();
        $maker = null;
        $model = null;
        // create maker as standard tags
        if ($product->make) {
            $maker = StandardTag::updateOrCreate(
                ['slug' => Str::slug($product->make)],
                [
                    'name' => $product->make,
                    'priority' => 1,
                    'status' => 'active',
                ]
            );
        }


        if ($product->model) {
            $model = StandardTag::updateOrCreate(
                ['slug' => Str::slug($product->model)],
                [
                    'name' => $product->model,
                    'priority' => 1,
                    'status' => 'active',
                ]
            );
        }

        // creating or updatting make => model hierarchy
        $hierarchy = TagHierarchy::updateOrCreate(
            [
                'L1' => $module->id,
                'L2' => $manuefacturer->id,
                'L3' => $maker ? $maker->id : null
            ],
            [
                'status' => 'active',
                'level_type' => 4,
                'is_multiple' => 1
            ]
        );

        if ($maker && $model) {
            $hierarchy->standardTags()->syncWithoutDetaching($model->id);
        }

        $tagIds = [$module->id, $manuefacturer->id];
        if ($maker) {
            $tagIds[] = $maker->id;
        }
        if ($model) {
            $tagIds[] = $model->id;
        }
        // check if body type exist
        if (property_exists($product, 'body_type') && filled($product->body_type)) {
            $body_type = StandardTag::where('slug', 'body-types')->first();

            $body = StandardTag::where('name', $product->body_type)->first();

            // body type hierarchy
            if ($body) {
                $bodyTypeHierarchy = TagHierarchy::updateOrCreate(
                    [
                        'L1' => $module->id,
                        'L2' => $body_type->id,
                        'L3' => $body->id
                    ],
                    [
                        'status' => 'active',
                        'level_type' => 4,
                        'is_multiple' => 1
                    ]
                );

                if ($maker) {
                    $bodyTypeHierarchy->standardTags()->syncWithoutDetaching($maker->id);
                }
                // push values in tag ids
                array_push($tagIds, $body->id, $body_type->id);

                // attaching Level 2 and Level 3 tags to business
                $business->standardTags()->syncWithoutDetaching([
                    $body_type->id,
                    $body->id
                ]);
            }
        }

        // attaching all the ID's to that product
        $newProduct->standardTags()->syncWithoutDetaching($tagIds);
        // attaching Level 2 and Level 3 tags to business
        $business->standardTags()->syncWithoutDetaching([
            $manuefacturer->id,
        ]);
        if ($maker) {
            $business->standardTags()->syncWithoutDetaching([
                $maker->id
            ]);
        }

        return [$maker, $model];
    }


    // make attribute tags of vehicle
    public function makeAttributes($product, $business, $newProduct)
    {

        // color
        if ($product->color) {
            $this->makerAttributeOrphanTags($product->color, $business, $newProduct, 'color');
        }

        // engine
        if ($product->engine_size) {
            $this->makerAttributeOrphanTags($product->engine_size, $business, $newProduct, 'engine');
        }

        // fuel type
        if ($product->fuel_type) {
            $this->makerAttributeOrphanTags($product->fuel_type, $business, $newProduct, 'fuel-type');
        }

        // year
        if ($product->year) {
            $this->makerAttributeOrphanTags($product->year, $business, $newProduct, 'year');
        }
    }

    private function makerAttributeOrphanTags($tagName, $business, $newProduct, $type)
    {
        $tag =
            Tag::updateOrCreate([
                'slug' => orphanTagSlug(trim($tagName))
            ], [
                'name' => $tagName,
                'type' => 'product',
            ]);
        $existInExtra = $tag->businesses()->where('id', $business->id)->first();
        if (!$existInExtra) {
            $tag->update([
                'is_show' => true
            ]);
        }
        // map orphan tags with standard tags
        $this->mapAttributeTags($tag, $newProduct, $type);
    }

    // map tags with standard tags
    private function mapAttributeTags($tag, $product, $type)
    {
        $attribute = Attribute::where('slug', $type)->firstOrFail();
        $product->tags()->syncWithoutDetaching($tag->id);
        if ($attribute) {
            if ($type == 'color') {
                $standardTag = $attribute->standardTags()->where('name', $tag->name)->first();
                if ($standardTag) {
                    $tag->update([
                        'priority' => $standardTag->priority,
                        'is_show' => \true
                    ]);
                    $tag->standardTags_()->syncWithoutDetaching($standardTag->id);
                    if ($standardTag->type == 'attribute') {
                        ProductTagsLevelManager::priorityTwoTags($product);
                    }
                }
            } else {
                $standardTag = StandardTag::updateOrCreate([
                    'slug' => Str::slug($tag->name)
                ], [
                    'name' => $tag->name,
                    'type' => 'attribute',
                    'priority' => 2
                ]);
                $tag->update([
                    'priority' => $standardTag->priority,
                    'is_show' => \true
                ]);
                $attribute->standardTags()->syncWithoutDetaching($standardTag->id);
                $tag->standardTags_()->syncWithoutDetaching($standardTag->id);
                $product->standardTags()->syncWithoutDetaching($standardTag->id);
                ProductTagsLevelManager::priorityTwoTags($product);
            }
        }
    }
}
