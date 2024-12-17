<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Automotive\Entities\DreamCar;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\StandardTag;

class SyncProductsWithOldCategories implements ShouldQueue
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $categories = DreamCar::all();
        $categories->chunk(500)->each(function ($chunk) {
            foreach ($chunk as $category) {
                $category->products()->detach();
                $search = (object) [
                    'level_one_id' => $category?->module_id,
                    'level_two_id' => $category?->make_id,
                    'level_three_id' => $category?->model_id,
                    'level_four_id' => $category?->level_four_tag_id,
                ];

                $module = StandardTag::where('id', $category?->module_id)->firstOrFail();

                $products = Product::  // Apply the 'active' scope
                    hierarchyBasedProducts($search) // Apply the 'scopeHierarchyBasedProducts' scope with $search
                    ->when($module?->slug == 'events', function($query) {
                        $query->whereEventDateNotPassed();
                    })
                    ->when($category?->from && $category?->to, function($query) use ($category) {
                        $query->whereHas('vehicle', function ($subQuery) use ($category) {
                            $subQuery->whereBetween('year', [$category?->from, $category?->to]);
                        });
                    })->when($category?->min_price && $category?->max_price, function($query) use ($category) {
                        $query->whereBetween('price', [$category->min_price, $category->max_price]);
                    })->when($category?->bed, function ($query) use ($category) {
                        $attribute = Attribute::whereIn('slug', ['bed', 'beds'])->first();
                        $query->whereHas('standardTags', function ($subQuery) use ($category, $attribute) {
                            $subQuery->where('id', $category->bed)->where('product_standard_tag.attribute_id', $attribute->id);
                        });
                    })->when($category?->bath, function ($query) use ($category) {
                        $attribute = Attribute::whereIn('slug', ['bath', 'baths'])->first();
                        $query->whereHas('standardTags', function ($subQuery) use ($category, $attribute) {
                            $subQuery->where('id', $category->bath)->where('product_standard_tag.attribute_id', $attribute->id);
                        });
                    })->when($category?->square_feet, function ($query) use ($category) {
                        $attribute = Attribute::whereIn('slug', ['square-feet', 'square-foot'])->first();
                        $query->whereHas('standardTags', function ($subQuery) use ($category, $attribute) {
                            $subQuery->where('id', $category->square_feet)->where('product_standard_tag.attribute_id', $attribute->id);
                        });
                    })
                    ->pluck('id');
                    $products->chunk(1000)->each(function ($chunk) use($category) {
                        $category->products()->attach($chunk);
                    });  
            }
        });
    }
}
