<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ActivateDeactivateTagProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $tags, $business, $previousStandardTags;
    public function __construct($business, $tags, $previousStandardTags)
    {
        $this->business = $business;
        $this->tags = $tags;
        $this->previousStandardTags = $previousStandardTags;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $moduleTagIds = $this->tags;
        $previousStandardTags = $this->previousStandardTags;
        if($previousStandardTags->count() == 0) {
            foreach ($moduleTagIds as $id) {
                $products = $this->business->products()->whereHas('standardTags', function($query) use($id) {
                    $query->where('id', $id);
                })->get();
                foreach ($products as $product) {
                        $product->status =  $product->previous_status;
                        $product->saveQuietly();
                    }
                }
        } else {
            foreach ($previousStandardTags as $tag) {
                $products = $this->business->products()->whereHas('standardTags', function($query) use($tag) {
                        $query->where('id', $tag);
                    })->get();
                foreach ($products as $product) {
                    $product->previous_status = $product->status;
                    $product->status = 'inactive';
                    $product->saveQuietly();
                }
            }
        }
        
    }
}
