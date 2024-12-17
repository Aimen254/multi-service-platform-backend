<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProductTagsManager implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $tag;
    protected $type;
    public function __construct($tag, $type)
    {
        $this->tag = $tag;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $products = null;
        switch($this->type) {
            case 'standard_tag':
                $products = $this->tag->productTags()->get();
                break;
        }
        if($products->count() > 0 ) {
            foreach ($products as $product) {
                ProductTagsLevelManager::productTagsManager($product);
            }
        }
    }
}
