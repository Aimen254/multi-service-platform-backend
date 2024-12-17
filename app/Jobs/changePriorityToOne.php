<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class changePriorityToOne implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $tag;
    protected $removeFlag;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tag, $removeFlag = false)
    {
        $this->tag = $tag;
        $this->removeFlag = $removeFlag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orphanTags = $this->tag->tags_()->get();
        if($orphanTags->count() > 0) {
            foreach ($orphanTags as $tag) {
                $products = $tag->products()->get();
                if($products->count() > 0) {
                    foreach ($products as $product) {
                        ProductTagsLevelManager::priorityThree($product, $tag->where('id', $tag->id)->pluck('id'), $this->removeFlag, null); 
                    }
                }
            }
        }
    }
}
