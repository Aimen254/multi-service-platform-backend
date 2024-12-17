<?php

namespace Modules\Automotive\Jobs;

use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class VehicleSpecifications implements ShouldQueue
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
        Log::alert("Automotive Maker and Model Scraper running");
        $response = Http::get('https://vpic.nhtsa.dot.gov/api/vehicles/GetModelsForMakeId/' . 0, [
            'format' => 'json'
        ]);

        $body = $response->getBody();
        $modelsAndMakes = json_decode($body);
        $moduleTag = StandardTag::whereSlug('automotive')->firstOrFail();
        $levelTwoTag = StandardTag::whereSlug('make')->firstOrFail();
        foreach ($modelsAndMakes->Results as $model) {
            $levelThreeTag = StandardTag::updateOrCreate(['slug' => Str::slug($model->Make_Name)], [
                'name' => $model->Make_Name,
                'type' => 'product',
                'priority' => 1
            ]);
            $levelFourTag = StandardTag::updateOrCreate(['slug' => Str::slug($model->Model_Name)], [
                'name' => $model->Model_Name,
                'type' => 'product',
                'priority' => 1
            ]);

            $checkExistedHierarchy = TagHierarchy::where('L1', $moduleTag->id)->where('L2', $levelTwoTag->id)->where('L3', $levelThreeTag->id)->whereHas('standardTags', function ($quuery) use ($levelFourTag) {
                $quuery->where('id', $levelFourTag->id);
            })->first();

            if (!$checkExistedHierarchy) {
                $tagHierarchy = TagHierarchy::create([
                    'L1' => $moduleTag->id,
                    'L2' => $levelTwoTag->id,
                    'L3' => $levelThreeTag->id,
                    'level_type' => 4,
                    'is_multiple' => 1
                ])->standardTags()->syncWithoutDetaching($levelFourTag->id);
            }
        }
        
        Log::alert("Automotive Maker and Model Scraper completed");
    }
}
