<?php

namespace App\Jobs;

use App\Models\Tag;
use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AssignTagsToStore implements ShouldQueue
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $tags, $business, $previousStandardTags;
    public function __construct($business, $tags, $previousStandardTags)
    {
        $this->tags = $tags;
        $this->business = $business;
        $this->previousStandardTags = $previousStandardTags;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //getting all producttags from user
        $tags = $this->tags->where('type', 'product')->pluck('id')->toArray();
        if (count($tags) > 0 || count($this->previousStandardTags) > 0) {
            //get all productIds against this business
            // $productIds = $this->business->products->pluck('id');
            //Getting all Ids of standard tags against this business
            $previousStandardTagsIds = $this->previousStandardTags->where('type', 'product')->pluck('id');
            $removingTags = array_diff($previousStandardTagsIds->toArray(), $this->tags->pluck('id')->toArray());
            //slug of all previous standard tags
            $previousStandardTagsSlug = $this->previousStandardTags->where('type', 'product')->pluck('slug');
            //Searching slug in orphan tag list to get all tags with this slug
            $previousIdsOrphan = Tag::where('type', 'product')->whereIn('slug', $previousStandardTagsSlug)->pluck('id');
            $products = $this->business->products()->get();
            $levelThreeTags = StandardTag::whereIn('id', $tags)->whereHas('levelThree')->get();
            foreach ($products as $product) {
                //getting all standard tags with user ids
                $standardTags = StandardTag::whereIn('id', $tags)->when(count($levelThreeTags) > 1, function ($query) use ($product) {
                    $query->where('name', $product->type);
                })->get();
                $standardTags = $standardTags->merge($this->business->standardTags()->whereHas('levelTwo')->get());
                if ($standardTags->count() > 0) {

                    // removing all previously assigned orphan tags from products
                    $deleted = DB::table('product_tag')->where('product_id', $product->id)->whereIn('tag_id', $previousIdsOrphan)->delete();
                    //removing all previously assigned standard tags from products
                    $deleted = DB::table('product_standard_tag')->where('product_id', $product->id)->whereIn('standard_tag_id', $previousStandardTagsIds)->delete();
                    // Looping through all standard tags
                    foreach ($standardTags as $standardTag) {
                        //checking if Orphan tags exists or not
                        $orphanTag = Tag::updateOrCreate([
                            'slug' => orphanTagSlug($standardTag->name),
                        ], [
                            'name' => $standardTag->name,
                            'type' => $standardTag->type,
                            'mapped_to' => $standardTag->id,
                        ]);
                        //syncing product against the standard tag
                        $standardTag->productTags()->syncWithoutDetaching($product->id);
                        //syncing product against the orphan tag
                        $orphanTag->products()->syncWithoutDetaching($product->id);
                        ProductTagsLevelManager::priorityOneTags($product, $removingTags, 'assign_tags');
                    }
                }
            }
        }
    }
}
