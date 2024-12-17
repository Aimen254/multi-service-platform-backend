<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Console\Command;

class DetachProductTagsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'detach:product-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detach product tags that do not belong to specific levels';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $moduleId = StandardTag::where('slug', 'blogs')->where('type', 'module')->pluck('id')->toArray();
        $products = Product::whereRelation('standardTags', 'id', $moduleId)
            ->where('status', Product::ACTIVE)
            ->get();

        foreach ($products as $product) {
            $levelOneTag = $product->standardTags()->whereHas('levelOne')->first();
            $levelTwoTag = $product->standardTags()->whereHas('levelTwo', function ($query) use ($levelOneTag) {
                $query->where('L1', $levelOneTag->id);
            })->first();
            $levelThreeTag = $product->standardTags()->whereHas('levelThree', function ($query) use ($levelOneTag, $levelTwoTag) {
                $query->where('L1', $levelOneTag->id)->where('L2', $levelTwoTag->id);
            })->first();
            $productLevelFourTag = StandardTag::whereHas('productTags', function ($query) use ($levelOneTag, $levelTwoTag, $levelThreeTag, $product) {
                $query->where('product_id', $product->id)->whereHas('standardTags', function ($subQuery) use ($levelOneTag, $levelTwoTag, $levelThreeTag) {
                    if ($levelOneTag && $levelTwoTag && $levelThreeTag) {
                        $subQuery->whereIn('id', [$levelOneTag->id, $levelTwoTag->id, $levelThreeTag->id]);
                    }
                });
            })->whereHas('tagHierarchies', function ($query) use ($levelOneTag, $levelTwoTag, $levelThreeTag) {
                $query->where('L1', $levelOneTag->id)->where('L2', $levelTwoTag->id)->where('L3', $levelThreeTag->id);
                $query->where('level_type', 4);
            })->first();
            $tagsToDetachIds = $product->standardTags()
            ->whereNotIn('id', [$levelOneTag->id, $levelTwoTag->id, $levelThreeTag->id, $productLevelFourTag->id])
            ->pluck('id')
            ->toArray();
            $product->standardTags()->detach($tagsToDetachIds);
        
        }
    }
}
