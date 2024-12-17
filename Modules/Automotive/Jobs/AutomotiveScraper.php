<?php

namespace Modules\Automotive\Jobs;

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
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\BusinessOrphanTagsManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Automotive\Entities\MakerModelHierarachy;

class AutomotiveScraper implements ShouldQueue
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
        Log::alert("Automotive Scraper running");
        $businessesNames = Business::whereHas('standardTags', function ($query) {
            $query->where('slug', 'automotive');
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
                            //Creating or upadting product
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
        Log::alert("Automotive Scraper completed");
    }


    /**
     * create new product.
     *
     * @param  $product, $business
     * @return $newProduct
     */
    private function createOrUpdateProduct($product, $business)
    {
        ModuleSessionManager::setModule('automotive', \false);
        $newProduct = Product::updateOrCreate(
            ['external_id' => $product->product_id],
            [
                'business_id' => $business->id,
                'name' => $product->title ?? '',
                'description' => $product->description ?? '',
                'price' => $product->price ?? '',
                'is_featured' => Product::where('business_id', $business->id)->count() < 10,
            ]
        );

        // generation of hierarchy dynamically
        $this->makeHierarchy($product, $business, $newProduct);
        $this->makeAttributes($product, $business, $newProduct);

        $maker = $newProduct->standardTags()->whereHas('levelTwo')->first();
        $model = $newProduct->standardTags()->whereHas('levelThree')->first();

        $newProduct->vehicle()->updateOrCreate(['product_id' => $newProduct->id], [
            'type' => $product->type,
            'maker_id' => $maker ? $maker->id : null,
            'model_id' => $model ? $model->id : null,
            'trim' => $product->trim,
            'year' => $product->year,
            'mpg' => '',
            'stock_no' => $product->stock,
            'vin' => $product->vin,
            'sellers_notes' => $product->sellerNotes,
            'mileage' => $product->mileage,
            'transmission' => $product->trans,
            'engine' => $product->engine_size,
            'drivetrain' => $product->drive_train,
            'fuel_type' => $product->fuel_type
        ]);

        return $newProduct;
    }

    /**
     * create new hierarchy.
     *
     * @param  $product, $business
     * 
     */

    private function makeHierarchy($product, $business, $newProduct)
    {
        $module = StandardTag::where('slug', 'automotive')->first();
        $makerModels = MakerModelHierarachy::where('make', $product->category)
            ->where('model', $product->sub_category)->where('year', $product->year);

        if ($makerModels->exists()) {
            // creating maker as a standard tag
            $maker = StandardTag::updateOrCreate(
                ['slug' => Str::slug($product->category)],
                [
                    'name' => $product->category,
                    'priority' => 1,
                    'status' => 'active',
                ]
            );

            // creating model as a standard tag
            $model = StandardTag::updateOrCreate(
                ['slug' => Str::slug($product->sub_category)],
                [
                    'name' => $product->sub_category,
                    'priority' => 1,
                    'status' => 'active',
                ]
            );

            // creating bodyStyle as a standard tag
            $bodyStyle = StandardTag::updateOrCreate(
                ['slug' => Str::slug($product->body)],
                [
                    'type' => 'product',
                    'name' => $product->body,
                    'priority' => 1,
                    'status' => 'active',
                ]
            );

            // creating or updatting bodystyle => model hierarchy
            $bodyStyleHierarchy = TagHierarchy::updateOrCreate(
                [
                    'L1' => $module->id,
                    'L2' => $bodyStyle->id,
                    'L3' => $model->id
                ],
                [
                    'status' => 'active',
                    'level_type' => 4,
                    'is_multiple' => 1
                ]
            );

            // creating or updatting make => model hierarchy
            $hierarchy = TagHierarchy::updateOrCreate(
                [
                    'L1' => $module->id,
                    'L2' => $maker->id,
                    'L3' => $model->id
                ],
                [
                    'status' => 'active',
                    'level_type' => 4,
                    'is_multiple' => 1
                ]
            );

            // finding exact trim
            $makerModelsTrim = $makerModels->where('trim', $product->trim);
            if ($makerModelsTrim->exists()) {
                // creating trim as a standard tag
                $trim = StandardTag::updateOrCreate(
                    ['slug' => Str::slug($product->trim)],
                    [
                        'name' => $product->trim,
                        'priority' => 1,
                        'status' => 'active',
                    ]
                );

                // assigning trim to maker model hierarchy
                $hierarchy->standardTags()->syncWithoutDetaching($trim->id);

                // assigning trim to bodyStyle model hierarchy
                $bodyStyleHierarchy->standardTags()->syncWithoutDetaching($trim->id);

                // tags to be assigned to a product
                $tagIds = [$module->id, $maker->id, $model->id, $trim->id, $bodyStyle->id];
            } else {
                $makerModels = MakerModelHierarachy::where('make', $product->category)
                    ->where('model', $product->sub_category)->where('year', $product->year)
                    ->groupBy('trim')->get();

                // tags to be assigned to a product
                $tagIds = [$module->id, $maker->id, $model->id, $bodyStyle->id];
                foreach ($makerModels as $makerModel) {
                    // creating or updating trim
                    $trim = StandardTag::updateOrCreate(
                        ['slug' => Str::slug($makerModel->trim)],
                        [
                            'name' => $makerModel->trim,
                            'priority' => 1,
                            'status' => 'active',
                        ]
                    );

                    // assigning trim to maker model hierarchy
                    $hierarchy->standardTags()->syncWithoutDetaching($trim->id);

                    // assigning trim to bodyStyle model hierarchy
                    $bodyStyleHierarchy->standardTags()->syncWithoutDetaching($trim->id);
                }

                $this->orphanTags($product, $business, $newProduct);
            }

            // attaching all the ID's to that product
            $newProduct->standardTags()->syncWithoutDetaching($tagIds);

            // attaching Level 2 and Level 3 tags to business
            $business->standardTags()->syncWithoutDetaching([
                $maker->id,
                $bodyStyle->id,
                $model->id
            ]);
        }

        return \true;
    }

    /**
     * create new orphan tag of trim.
     * 
     */

    private function orphanTags($product, $business, $newProduct)
    {
        $tag = Tag::updateOrCreate([
            'slug' => orphanTagSlug(trim($product->trim))
        ], [
            'name' => $product->trim,
            'type' => 'product',
        ]);

        $existInExtra = $tag->businesses()->where('id', $business->id)->first();
        if (!$existInExtra) {
            $tag->update([
                'is_show' => true
            ]);
        }

        $newProduct->tags()->syncWithoutDetaching($tag->id);

        ProductTagsLevelManager::priorityFour($newProduct);
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


    /* check product image url.
     *
     * @param  $url
     * @return boolean
     */
    private function bodyStyleTags($product, $business, $newProduct)
    {
        $tag = Tag::updateOrCreate([
            'slug' => orphanTagSlug(trim($product->body))
        ], [
            'name' => $product->body,
            'type' => 'product',
        ]);

        $existInExtra = $tag->businesses()->where('id', $business->id)->first();
        if (!$existInExtra) {
            $tag->update([
                'is_show' => true
            ]);
        }
        $newProduct->tags()->syncWithoutDetaching($tag->id);

        // check if exist in standard tags

        $bodyStyle = StandardTag::where('name', $tag->name)->whereIn('name', \config()->get('automotive.body_styles'))->first();
        if ($bodyStyle) {
            $tag->update([
                'priority' => $bodyStyle->priority,
                'is_show' => \true
            ]);
            $tag->standardTags_()->syncWithoutDetaching($bodyStyle->id);
            $newProduct->standardTags()->syncWithoutDetaching($bodyStyle->id);
            if ($bodyStyle->type == 'attribute') {
                ProductTagsLevelManager::priorityTwoTags($newProduct);
            }
        }
    }

    // make attribute tags of vehicle
    public function makeAttributes($product, $business, $newProduct)
    {

        // interiror color
        if ($product->int_color) {
            $this->makerAttributeOrphanTags($product->int_color, $business, $newProduct, 'interior-color');
        }

        // exterior color
        if ($product->ext_color) {
            $this->makerAttributeOrphanTags($product->ext_color, $business, $newProduct, 'exterior-color');
        }

        // transmission
        if ($product->trans) {
            $this->makerAttributeOrphanTags($product->trans, $business, $newProduct, 'transmission');
        }

        // engine
        if ($product->engine_size) {
            $this->makerAttributeOrphanTags($product->engine_size, $business, $newProduct, 'engine');
        }

        // drivetrain
        if ($product->drive_train) {
            $this->makerAttributeOrphanTags($product->drive_train, $business, $newProduct, 'drivetrain');
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
        // map if interiror color exsit in standard tags with interior color attribut
        $this->mapAttributeTags($tag, $newProduct, $type);
    }

    // map tags with standard tags
    private function mapAttributeTags($tag, $product, $type)
    {
        $attribute = Attribute::where('slug', $type)->firstOrFail();
        if ($attribute) {
            if ($type == 'interior-color' || $type == 'exterior-color') {
                $isExist = $product->tags()->withPivot(['type'])
                    ->wherePivot('type', $type)
                    ->wherePivot('product_id', $product->id)
                    ->where('tag_id', $tag->id)
                    ->first();
                if (!$isExist) {
                    $product->tags()->attach($tag->id, [
                        'type' => $type,
                        'product_id' => $product->id,
                    ]);
                }
                // $product->tags()->syncWithoutDetaching($tag->id);
                $standardTag = $attribute->standardTags()->where('name', $tag->name)->first();
                if ($standardTag) {
                    $tag->update([
                        'priority' => $standardTag->priority,
                        'is_show' => \true
                    ]);
                    $tag->standardTags_()->syncWithoutDetaching($standardTag->id);
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
                $product->tags()->syncWithoutDetaching($tag->id);
                $attribute->standardTags()->syncWithoutDetaching($standardTag->id);
                $tag->standardTags_()->syncWithoutDetaching($standardTag->id);
                $product->standardTags()->syncWithoutDetaching($standardTag->id);
                ProductTagsLevelManager::priorityTwoTags($product);
            }
        } else {
            $product->tags()->syncWithoutDetaching($tag->id);
        }
    }
}
