<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AutomotiveMultipleHierarchy implements ShouldQueue
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
        Log::info("start");
        $module = StandardTag::where('slug', 'automotive')->first();
        $products = Product::whereRelation('standardTags', 'slug', 'automotive')->get();
        foreach ($products as $product) {
            $levelThree = null; 
            $levelFour = null;
            $levelTwo = $product->standardTags()->whereHas('levelTwo', function($subquery) use($module) {
                $subquery->where('L1', $module->id);
            })->wherePivotNull('hierarchy_id')->first();

            if($levelTwo) {
                $levelThree = $product->standardTags()->whereHas('levelThree', function($subquery) use($module, $levelTwo) {
                    $subquery->where('L1', $module->id)->where('L2', $levelTwo?->id);
                })->wherePivotNull('hierarchy_id')->first();
                if($levelThree) {
                    $levelFour = $product->standardTags()->whereHas('tagHierarchies', function($subquery) use($module, $levelTwo, $levelThree) {
                        $subquery->where('L1', $module?->id)->where('L2', $levelTwo?->id)->where('L3', $levelThree?->id);
                    })->wherePivotNull('hierarchy_id')->first();
                }
            }

            $hierarchy = TagHierarchy::where('L1', $module->id)->when($levelTwo, function($query) use ($levelTwo) {
                $query->where('L2', $levelTwo?->id);
            })->when($levelThree, function($query) use ($levelThree) {
                $query->where('L3', $levelThree?->id);
            })->when($levelFour, function($query) use ($levelFour) {
                $query->whereRelation('standardTags', 'id', $levelFour?->id);
            })
            ->first();

            // Update the pivot table
            if ($levelTwo) {
                $product->standardTags()->updateExistingPivot($levelTwo->id, ['hierarchy_id' => $hierarchy->id]);
            }
            if ($levelThree) {
                $product->standardTags()->updateExistingPivot($levelThree->id, ['hierarchy_id' => $hierarchy->id]);
            }
            if ($levelFour) {
                $product->standardTags()->updateExistingPivot($levelFour->id, ['hierarchy_id' => $hierarchy->id]);
            }
            Log::info('end');
        }
    }
}
