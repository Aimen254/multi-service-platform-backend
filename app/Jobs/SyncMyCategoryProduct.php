<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\{Product, Attribute};

class SyncMyCategoryProduct implements ShouldQueue
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $module;
    protected $dreamCar;
    public function __construct($module, $dreamCar)
    {
        $this->module = $module;
        $this->dreamCar = $dreamCar;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $dreamCar = $this->dreamCar;
        $module = $this->module;

        $search = (object) [
            'level_one_id' => $this->dreamCar?->module_id,
            'level_two_id' => $this->dreamCar?->make_id,
            'level_three_id' => $this->dreamCar?->model_id,
            'level_four_id' => $this->dreamCar?->level_four_tag_id,
        ];

        $products = Product::  // Apply the 'active' scope
            hierarchyBasedProducts($search) // Apply the 'scopeHierarchyBasedProducts' scope with $search
            ->when($this->module == 'events', function($query) {
                $query->whereEventDateNotPassed();
            })
            ->when($this->dreamCar?->from && $this->dreamCar?->to, function($query) use ($dreamCar) {
                $query->whereHas('vehicle', function ($subQuery) use ($dreamCar) {
                    $subQuery->whereBetween('year', [$dreamCar?->from, $dreamCar?->to]);
                });
            })->when($dreamCar?->min_price && $dreamCar?->max_price, function($query) use ($dreamCar) {
                $query->whereBetween('price', [$dreamCar->min_price, $dreamCar->max_price]);
            })->when($dreamCar?->bed, function ($query) use ($dreamCar) {
                $attribute = Attribute::whereIn('slug', ['bed', 'beds'])->first();
                $query->whereHas('standardTags', function ($subQuery) use ($dreamCar, $attribute) {
                    $subQuery->where('id', $dreamCar->bed)->where('product_standard_tag.attribute_id', $attribute->id);
                });
            })->when($dreamCar->bath, function ($query) use ($dreamCar) {
                $attribute = Attribute::whereIn('slug', ['bath', 'baths'])->first();
                $query->whereHas('standardTags', function ($subQuery) use ($dreamCar, $attribute) {
                    $subQuery->where('id', $dreamCar->bath)->where('product_standard_tag.attribute_id', $attribute->id);
                });
            })->when($dreamCar->square_feet, function ($query) use ($dreamCar) {
                $attribute = Attribute::whereIn('slug', ['square-feet', 'square-foot'])->first();
                $query->whereHas('standardTags', function ($subQuery) use ($dreamCar, $attribute) {
                    $subQuery->where('id', $dreamCar->square_feet)->where('product_standard_tag.attribute_id', $attribute->id);
                });
            })
            ->pluck('id');
        $dreamCar->products()->detach();
        $products->chunk(1000)->each(function ($chunk) use($dreamCar) {
            $dreamCar->products()->attach($chunk);
        });
    }
}
