<?php

namespace App\Jobs\DataImporter;

use App\Models\User;
use JsonMachine\Items;
use App\Models\Product;
use App\Models\Business;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AutomotiveDataImporter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileBasePath, $count;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->count = 0;
        $this->fileBasePath = base_path("modules_json/AutomotiveDataset.json");
        if (!file_exists($this->fileBasePath)) {
            Log::error("File not found: " . $this->fileBasePath);
            exit;
        }

        try {
            Log::info('Automotive Importer start');
            $module = StandardTag::whereSlug('automotive')->whereType('module')
                ->firstOrFail();
            $businesses = Items::fromFile($this->fileBasePath);
            foreach ($businesses as $index => $business) {
                $owner = $this->getBusinessOwner($business);
                $savedBusiness = $this->saveBusinessInformation($business, $owner);

                $dealpershipProductCount = 0;
                foreach ($business->products as $key => $item) {
                    // get vehicle complete hierarchy
                    $hierarchyTags = $this->getHierarchyTags($item, $module);
                    if (count($hierarchyTags) >= 4) {
                        DB::beginTransaction();
                        $savedProduct = $this->saveProduct($savedBusiness, $item);

                        // getting ready hierarchy syncing data
                        $hierarchyTags = $this->generateHierarchyTagsData($hierarchyTags, $item, $module);
                        // attach tags to product
                        $savedProduct->standardTags()->syncWithoutDetaching($hierarchyTags);

                        // attach tags to business
                        $savedBusiness->standardTags()->syncWithoutDetaching(
                            $this->getHierarchyTags($item, $module, 'L3')
                        );

                        // now we will sart working on saving attributes for automotive
                        $this->generateAttributes($item, $module, $savedProduct);

                        // managing product priorities
                        ProductTagsLevelManager::checkProductTagsLevel($savedProduct);
                        ProductTagsLevelManager::priorityOneTags($savedProduct);
                        ProductTagsLevelManager::priorityTwoTags($savedProduct);
                        ProductTagsLevelManager::priorityThree($savedProduct);
                        ProductTagsLevelManager::priorityFour($savedProduct);

                        DB::commit();
                    }

                    $dealpershipProductCount++;
                    $this->count++;
                }

                Log::alert($dealpershipProductCount . ' vehicles are added to database for business -> ' . $savedBusiness->name);
            }

            Log::info($this->count . ' vehicles for automotive module are added to database');
            Log::info('Events Importer end');
        } catch (\Exception $e) {
            Log::warning('Message: ' . $e->getMessage());
            Log::warning('Line: ' . $e->getLine());
            Log::warning('File: ' . $e->getFile());
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

    // generate full hierarchy including standard tags and hierachy information
    function generateHierarchyTagsData($hierarchyTags, $item, $module) : array {
        // get hierarchy id
        $hierarchyId = $this->getHierarchyId($item, $module);

        $dataToBeSynced = [];
        foreach ($hierarchyTags as $key => $hierarchyTag) {
            $dataToBeSynced[$hierarchyTag] =  ['hierarchy_id' => $hierarchyId];
        }
        return $dataToBeSynced;
    }

    // get business owner informations
    private function getBusinessOwner($business) : Model {
        return User::where('email', $business->user_email)->firstOrFail();
    }

    // store business information
    function saveBusinessInformation($business, $owner) : Model {
        $savedBusiness = Business::updateOrCreate(['name' => $business->business_name], [
            'email' => $business->business_email,
            'slug' => Str::slug($business->business_name),
            'owner_id' => $owner->id,
            'phone' => '+' . $business->phone,
            'address' => $business->address,
            'is_featured' => $business->is_featured,
        ]);

        // saving or updating business media information
        $this->saveBusinessMedia($savedBusiness, $business);

        return $savedBusiness;
    }

    // store business media like logo, banners
    private function saveBusinessMedia($savedBusiness, $business) : bool {
        $media = [
            'logo' => $business->business_logo,
            'banner' => $business->business_banner,
            'secondaryBanner' => $business->business_secondaryBanner
        ];

        foreach ($media as $key => $image) {
            $savedBusiness->media()->updateOrCreate(['type' => $key], [
                'path' => $image,
                'is_external' => 1
            ]);
        }

        return true;
    }

    // store basic product information
    private function saveProduct($savedBusiness, $item) : Model {
        $savedProduct = NULL;
        Model::withoutEvents(function () use ($savedBusiness, $item, &$savedProduct) {
            $savedProduct = $savedBusiness->products()->updateOrCreate(
                ['name' => $item->product_name],
                [
                    'type' => $item?->type ? $item?->type : null,
                    'price' => $item?->price ? $item?->price : null,
                    'sku' => $item?->sku ? $item?->sku : null,
                    'trim' => $item?->trim ? $item?->trim : null,
                    'description' => $item?->description ? $item->description : null,
                    'is_featured' => $item?->is_featured ? $item?->is_featured : false,
                    'previous_status' => 'inactive'
                ]
            );

            if ($savedProduct->wasRecentlyCreated) {
                $savedProduct->update(['uuid' => Str::uuid()]);
            }

            // saving product extra information
            $savedProduct->vehicle()->updateOrCreate(
                ['product_id' => $savedProduct->id],
                [
                    'type' => $item?->type,
                    'year' => $item?->year,
                    'trim' => $item?->trim,
                    'mileage' => $item?->mileage,
                    'stock_no' => $item?->stock_no,
                    'engine' => $item?->engine,
                    'transmission' => $item?->transmission,
                    'drivetrain' => $item?->drivetrain,
                    'fuel_type' => $item?->fuel_type,
                    'mpg' => $item?->mpg,
                    'vin' => $item?->vin
                ]
            );

            // saving media information
            if (count($item->media) > 0) {
                $savedProduct->media()->delete();
                foreach ($item->media as $key => $image) {
                    $savedProduct->media()->create([
                        'path' => $image->path,
                        'is_external' => 1
                    ]);
                }
            }
        });

        return Product::findOrFail($savedProduct?->id);
    }

    // get all hierarhcy tags from L1 to L4
    private function getHierarchyTags($item, $module, $type = 'L4') : array {
        $tagNames = $type == 'L4'
            ? [$item->l2, $item->l3, $item->l4] : [$item->l2, $item->l3];
        $tags = StandardTag::whereIn('name', $tagNames)
            ->pluck('id')->toArray();

        return array_merge($tags, [$module->id]);
    }

    private function getHierarchyId($item, $module) : int {
        return TagHierarchy::where('L1', $module->id)
            ->whereHas('levelTwo', function ($query) use ($item) {
                $query->whereName($item->l2);
            })->whereHas('levelThree', function ($query) use ($item) {
                $query->whereName($item->l3);
            })->whereHas('standardTags', function ($query) use ($item) {
                $query->whereName($item->l4);
            })->firstOrFail()->id;
    }

    // generate attributes for vehicles
    private function generateAttributes($item, $module, $savedProduct) : bool {
        $attributesList = [
            'interior_color' => $item->interior_color_id,
            'exterior_color' => $item->exterior_color_id,
            'engine' => $item->engine,
            'transmission' => $item->transmission,
            'drivetrain' => $item->drivetrain,
            'fuel_type' => $item->fuel_type,
            'year' => $item->year,
        ];

        foreach ($attributesList as $attributeKey => $attributeValue) {
            $attributeName = Str::replace('_', ' ', $attributeKey);
            $this->attributeAttachment($attributeValue, $attributeName, $module, $savedProduct);
        }
        return true;
    }

    // assign attributes
    private function attributeAttachment($attributeValue, $attributeName, $module, $savedProduct) {
        $attributeDetails = $this->getAttributeDetails($attributeName, $attributeValue, $module);
        if ($attributeDetails) {
            if (count($attributeDetails->standardTags) == 1) {
                $attributeOption = $attributeDetails->standardTags[0];
                $attributeData = [
                    'attribute_value_id' => $attributeOption->id,
                    'attribute_id' => $attributeDetails->id
                ];
            } else {
                $newStandardTag = StandardTag::updateOrCreate(
                    ['name' => $attributeValue],
                    [
                        'type' => 'attribute'
                    ]
                );

                // sync tag value with attribute
                $attributeDetails->standardTags()->syncWithoutDetaching(
                    [$newStandardTag->id]
                );
                $attributeData = [
                    'attribute_value_id' => $newStandardTag->id,
                    'attribute_id' => $attributeDetails->id
                ];
            }

            $this->attachAttributes($attributeData, $savedProduct);
        } else {
            // log infomration of the product and attribute
            Log::info("{$attributeName} Attribute's value {$attributeValue} is missing for product {$savedProduct->name}");
        }
        return true;
    }

    // attach attributes
    private function attachAttributes($attributeData, $savedProduct) : bool {
        $savedProduct->standardTags()->syncWithoutDetaching([
            $attributeData['attribute_value_id'] => [
                'attribute_id' => $attributeData['attribute_id']
            ]
        ]);

        return true;
    }

    // get Attribute details
    function getAttributeDetails($attributeName, $attributeValue, $module) {
        return Attribute::whereName($attributeName)->active()
            ->with(['standardTags' => function ($query) use ($attributeValue) {
                $query->whereName($attributeValue);
            }])->whereHas('moduleTags', function ($query) use ($module) {
                $query->whereName($module->name)->whereType('module');
            })->first();
    }
}
