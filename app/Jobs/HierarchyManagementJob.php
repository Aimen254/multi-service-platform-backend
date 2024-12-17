<?php

namespace App\Jobs;

use App\Models\StandardTag;
use Illuminate\Support\Arr;
use App\Models\TagHierarchy;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class HierarchyManagementJob 
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;
    protected $module_id;
    protected $level;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $module_id, $level)
    {
        $this->request = $request;
        $this->module_id = $module_id;
        $this->level = $level;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $level = $this->level;
        $request = $this->request;
        $module_id = $this->module_id;

        $alreadyExists = TagHierarchy::select('L' . $level)->where('L1', $module_id)->where(function ($query) use ($level, $request) {
            if ($level > 2) {
                $query->where('L2', $request['L2']);
            }
            if ($level > 3) {
                $query->where('L3', $request['L3']);
            }
            // $query->where('level_type', $level);
        })->pluck('L' . $level)->toArray();
        $existedTags = TagHierarchy::with(['standardTags'])->where('L2', $request['L2'])->where('L3', $request['L3'])->first();
        $tagHierarchy = $this->processInput($request, $module_id, $level);
        $standardTags = array_diff(Arr::flatten(array_column($request['L' . $level], 'id')), $alreadyExists);

        if (count($standardTags) > 0) {
            $tagHierarchy->standardTags()->sync($standardTags);
            $standardTags = $tagHierarchy->standardTags()->get();
            foreach ($standardTags as $standardTag) {
                $products = $standardTag->productTags()->get();
                $standardTag->priority = 1;
                $standardTag->saveQuietly();
                //code commented to simplify job for crud purpose only
                // ProductTagsLevelManager::orphanTagsPriority($standardTag);
                // foreach ($products as $product) {
                //     ProductTagsLevelManager::checkProductTagsLevel($product);
                //     ProductTagsLevelManager::priorityOneTags($product, 'hierarchy');
                // }
            }
        }

        if (count($request['removeTags']) > 0) {
            $removeIds = Arr::flatten(array_column($request['removeTags'], 'id'));
            foreach ($removeIds as $id) {
                $standardTag = StandardTag::findOrFail($id);
                if ($level == 3) {
                    $level3 = $standardTag->levelThree()->first();
                    $level3->update([
                        'L3' => null,
                        'level_type' => 2
                    ]);
                } else {
                    $tagHierarchy->standardTags()->detach($id);
                }
                $standardTag->priority = 4;
                $standardTag->saveQuietly();
                //code commented to simplify job for crud purpose only
                // ProductTagsLevelManager::orphanTagsPriority($standardTag);
                // $products = $standardTag->productTags()->get();
                // $tags = $standardTag->tags->pluck('id')->toArray();
                // foreach ($products as $product) {
                //     ProductTagsLevelManager::checkProductTagsLevel($product);
                //     ProductTagsLevelManager::priorityOneTags($product, $removeIds, 'hierarchy');
                // }
            }
        }
    }


    private function processInput($request, $module_id, $level)
    {
        //check DB for matching
        $matchData = [
            'L1' => $module_id,
            'level_type' => $level
        ];
        $tagHierarchy = $this->removeTagsAndHierarchy($module_id, $level, $request);
        if ($level > 1) {

            //preparing the data for tag hierachy table according to level
            if ($level > 2) {
                $matchData['L2'] = $request['L2'];
            }

            if ($level > 3) {
                $matchData['L3'] = $request['L3'];
            }

            //if hierarchy exists according to level then remove the tag and make a pivot
            if ($tagHierarchy) {
                // $tagHierarchy->standardTags()->detach(request()->input('L' . $level - 1));
            }
        }
        $tagHierarchy = TagHierarchy::updateOrCreate($matchData, [
            'is_multiple' => true,
        ]);

        return $tagHierarchy;
    }

    //Removing hierarchy if there is no tag in its pivot
    private function removeTagsAndHierarchy($module_id, $level, $request)
    {
        $hierarchy = TagHierarchy::where(function ($query) use ($level, $module_id, $request) {
            // complex query
            // If level is four than "  'L' . $level - 2, request()->input('L' . $level - 2)  " will return L2 and $level-1 will return 3
            // If level is three than "  'L' . $level - 2, request()->input('L' . $level - 2)  " will return L3 and $level-1 will return 2
            $query->where('L1', $module_id);
            if ($level > 3) {
                $query->where('L2', $request['L2']);
            }
            $query->where('level_type', $level - 1);
        })->first();
        $totalCountingOfTags = 0;
        // counting tags against hierarchy expect the current tag
        if ($hierarchy) {
            $totalCountingOfTags = $hierarchy->standardTags()->where('id', '!=', $request['L' . $level - 1])->count();
            if ($totalCountingOfTags == 0) {
                $hierarchy->delete();
            }
        }
        return $hierarchy;
    }
}
