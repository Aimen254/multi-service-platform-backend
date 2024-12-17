<?php

namespace App\Jobs\DataImporter;


use App\Models\User;
use JsonMachine\Items;
use App\Models\Product;
use App\Models\Business;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Traits\ModuleSessionManager;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Modules\Automotive\Entities\ProductAutomotive;

class AutomotiveDataImporter implements ShouldQueue
{
    protected $count;

    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        try {
            $this->count = 0;
            Log::info('Automotive Importer start');
            $jsonFilePath = base_path("modules_json/AutomotiveDataset.json");

            if (!file_exists($jsonFilePath)) {
                Log::error("File not found: $jsonFilePath");
                return;
            }
            $moduleItems = Items::fromFile($jsonFilePath);
            foreach ($moduleItems as $key => $item) { 
                $ownerId = User::where('email', $item?->user_email)->first()?->id;
                $business = Business::updateOrCreate([
                    'name' => $item?->business_name,
                    'slug' => Str::slug($item?->business_name)
                ], [
                    'email' => $item?->business_email,
                    'owner_id' => $ownerId,
                    'phone' => '+' . $item?->phone,
                    'address' => $item?->address,
                    'is_featured' => $item?->is_featured,
                ]);
                
                // business logo
                $this->businessMedia($business, $item?->business_logo, 'logo');
                // business banner
                $this->businessMedia($business, $item?->business_banner, 'banner');
                // business secondary banner
                $this->businessMedia($business, $item?->business_secondaryBanner, 'secondaryBanner');

                // iterate products to create product
                foreach ($item->products as $product) {
                    Log::alert('Automotive product id is :' . $product->id);
                    // call function to create product
                    $this->createProduct($business, $product);

                    $this->count++;
                }
            }
            Log::info($this->count . ' items for automotive module are added to database');
            Log::info('Automotive Importer end');
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
            Log::alert($e->getLine());
            Log::alert($e->getFile());
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 
    }

    // business media
    private function businessMedia($business, $media, $type) {
        $business->media()->updateOrCreate([
            'type' => $type,
        ], [
            'path' => $media,
            'is_external' => 1
        ]);
    }

    private function createProduct($business, $product) {
        // find module tag
        $L1 = StandardTag::where('slug', Str::slug($product?->l1))->first();
        ModuleSessionManager::setModule($L1?->name);
        $newProduct = null;
        Model::withoutEvents(function () use ($product, &$newProduct, &$business) { 
            $newProduct = Product::updateOrCreate([
                'name' => $product?->product_name,
            ], [
                'uuid' => Str::uuid(),
                'type' => $product?->type ? $product?->type : null,
                'business_id' => $business?->id,
                'price' => $product?->price ? $product?->price : null,
                'sku' => $product?->sku ? $product?->sku : null,
                'trim' => $product?->trim ? $product?->trim : null,
                'description' => $product?->description ? $product->description : null,
                'is_featured' => $product?->is_featured ? $product?->is_featured : false,
                'previous_status' => 'inactive'
            ]);
            
        });

        $L2 = StandardTag::where('slug', Str::slug($product->l2))->first();
        // L3 tag
        $L3 = StandardTag::where('slug', Str::slug($product->l3))->first();
        // L4 tag
        $L4 = StandardTag::where('slug', Str::slug($product->l4))->first();
        
        // attach tags to product
        $newProduct?->standardTags()->syncWithoutDetaching([$L1?->id, $L2?->id, $L3?->id, $L4->id]);

        // attach tags to business
        $business?->standardTags()->syncWithoutDetaching([$L1?->id, $L2?->id, $L3?->id]);

        // product automotive
        if($L1?->slug == 'automotive' || $L1?->slug == 'boats') {
            $this->productAutomotive($newProduct, $product, $L3, $L4);
        }

        // product attributes
        $this->productAttribute($newProduct, $product, $L1);
        
        ProductTagsLevelManager::checkProductTagsLevel($newProduct);
        ProductTagsLevelManager::priorityOneTags($newProduct);
        ProductTagsLevelManager::priorityTwoTags($newProduct);
        ProductTagsLevelManager::priorityThree($newProduct);
        ProductTagsLevelManager::priorityFour($newProduct);
    }

    // create vehicle in automotive and boats
    private function productAutomotive($newProduct, $product, $L3, $L4) {
        ProductAutomotive::updateOrCreate([
            'product_id' => $newProduct?->id,
            'maker_id' => $L3?->id,
            'model_id' => $L4?->id,
            'type' => $product?->type,
            'year' => $product?->year,
            'trim' => $product?->trim,
            'mileage' => $product?->mileage,
            'stock_no' => $product?->stock_no,
            'engine' => $product?->engine,
            'transmission' => $product?->transmission,
            'drivetrain' => $product?->drivetrain,
            'fuel_type' => $product?->fuel_type,
            'mpg' => $product?->mpg,
            'vin' => $product?->vin
        ]);
    }

    // product attributes
    private function productAttribute($newProduct, $product, $L1) {
        switch ($L1?->slug) {
            case 'automotive':
                // year attribute
                if($product?->year) {
                    $yearAttribute = Attribute::updateOrCreate([
                        'name' => 'Year',
                        'slug' => 'year'
                    ], [
                        'status' => 'active',
                    ]);
                    $year = StandardTag::updateOrCreate([
                        'name' => $product?->year,
                        'slug' => $product?->year
                    ], [
                        'status' =>'active',
                        'priority' => 2,
                        'type' => 'attribute',
                    ]);
                    $yearAttribute->standardTags()->syncWithoutDetaching([$L1?->id, $year?->id]);
                    // check if product has years attribute tag
                    $productsYearTags = $newProduct->standardTags()->whereRelation('attribute', 'id', $yearAttribute->id)->where('type', '<>', 'module')->pluck('id');
                    // remove previous years tags
                    $newProduct->standardTags()->detach($productsYearTags);

                    // attach new year tags
                    $newProduct->standardTags()->syncWithoutDetaching($year->id);
                } 
                if($product?->exterior_color_id || $product?->interior_color_id) {
                    $attributeName = ['Interior Color', 'Exterior Color'];
                    foreach ($attributeName as $name) {
                        $attribute = Attribute::updateOrCreate([
                            'name' => $name,
                            'slug' => Str::slug($name)
                        ], [
                            'status' => 'active',
                        ]);
                        $attributeTag = null;
                        if($attribute->slug == 'interior-color');
                        {
                            $attributeTag = StandardTag::updateOrCreate([
                                'name' => $product?->interior_color_id,
                                'slug' => Str::slug($product?->interior_color_id)
                            ], [
                                'status' =>'active',
                                'priority' => 2,
                                'type' => 'attribute',
                            ]);
                            $this->checkAttributTag($attributeTag, $attribute, $newProduct);
                        } 

                        if($attribute->slug == 'exterior-color');
                        {
                            $attributeTag = StandardTag::updateOrCreate([
                                'name' => $product?->exterior_color_id,
                                'slug' => Str::slug($product?->exterior_color_id)
                            ], [
                                'status' =>'active',
                                'priority' => 2,
                                'type' => 'attribute',
                            ]);
                            $this->checkAttributTag($attributeTag, $attribute, $newProduct);
                        }
                    }
                }
                break;
        }
    }

    // check if attribute tag already exist
    private function checkAttributTag($tag, $attribute, $newProduct) {
        $isExist = $newProduct->standardTags()
                ->withPivot(['attribute_id'])
                ->wherePivot('attribute_id', $attribute->id)
                ->wherePivot('standard_tag_id', $tag->id)
                ->where('product_id', $newProduct->id)
                    ->first();
        if (!$isExist) {
            $newProduct->standardTags()->attach($tag->id, [
                'attribute_id' => $attribute->id,
                'product_id' => $newProduct->id
            ]);
        }
    }
}
