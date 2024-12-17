<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use App\Models\StandardTag;

class UpdateProductPriorityOneTag implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $tag;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(StandardTag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $products = Product::whereHas('standardTags', function ($query) {
            $query->where('standard_tag_id', $this->tag->id);
        })
        ->active() 
        ->get();  
        foreach ($products as $product) {
            ProductTagsLevelManager::priorityOneTags($product, null, 'update');
        }

    }
}
