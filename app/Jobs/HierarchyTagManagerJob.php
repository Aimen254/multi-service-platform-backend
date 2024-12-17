<?php

namespace App\Jobs;

use App\Models\StandardTag;
use Illuminate\Support\Arr;
use App\Models\TagHierarchy;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class HierarchyTagManagerJob 
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $request;
    protected $module_id;
    protected $level;
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
        $alreadyExists = TagHierarchy::where('L1', $module_id)->select('L' . $level)->where(function ($query) use ($level, $request) {
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
        $existedTagsIds = $existedTags ? $existedTags->standardTags()->pluck('id')->toArray() : [];
        $removeIds = array_diff($existedTagsIds, Arr::flatten(array_column($request['L' . $level], 'id')));
        if ($level == 3) {
            $level3 = TagHierarchy::where('L2', $request['L2'])->where('level_type', 3)->first();
            $levelThreeexistedTagsIds = $level3 ? $level3->standardTags()->pluck('id')->toArray() : [];
            $removeIds = array_diff($levelThreeexistedTagsIds, Arr::flatten(array_column($request['L' . $level], 'id')));
        }
        if ($level == 2) {
            $level2 = TagHierarchy::where('L1', $module_id)->where('level_type', 2)->first();
            $levelTwoexistedTagsIds = $level2 ? $level2->standardTags()->pluck('id')->toArray() : [];
            $removeIds = array_diff($levelTwoexistedTagsIds, Arr::flatten(array_column($request['L' . $level], 'id')));
        }
        if (count($standardTags) == 0) {
            $this->syncTagRemovedFromPivot($tagHierarchy, $level, $request, $module_id);
            if ($level == 2) {
                $removeTags = array_diff($alreadyExists, Arr::flatten(array_column($request['L' . $level], 'id')));
                foreach ($removeTags as $tag) {
                    if ($tag) {
                        $deletingHirarchy = TagHierarchy::where('L2', $tag)->first();
                        if ($deletingHirarchy) {
                            $deletingHirarchy->delete();
                        }
                    }
                }
            }
        } else {
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
                //     ProductTagsLevelManager::priorityOneTags($product, $removeIds, 'hierarchy');
                // }
            }
        }

        if ($alreadyExists && $existedTags && $existedTags->standardTags->count() > 0) {
            foreach ($existedTags->standardTags as $standardTag) {
                $standardTag->priority = 1;
                $standardTag->saveQuietly();
                //code commented to simplify job for crud purpose only
                // ProductTagsLevelManager::orphanTagsPriority($standardTag);
                // $products = $standardTag->productTags()->get();
                // foreach ($products as $product) {
                //     ProductTagsLevelManager::checkProductTagsLevel($product);
                //     ProductTagsLevelManager::priorityOneTags($product, $removeIds, 'hierarchy');
                // }
            }
        }
        if ($removeIds && count($removeIds) > 0) {
            foreach ($removeIds as $id) {
                $standardTag = StandardTag::findOrFail($id);
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

    private function syncTagRemovedFromPivot($tagHierarchy, $level, $request, $module_id)
    {
        $previousLevel = $level - 1;
        $columnName = 'L' . $previousLevel;
        $id = $tagHierarchy->$columnName;
        $hierachy = TagHierarchy::where('L1', $tagHierarchy->L1)
            ->where(function ($query) use ($level, $tagHierarchy) {
                if ($level == 4) {
                    $query->where('L2', $tagHierarchy->L2);
                }
            })->where('level_type', $previousLevel)->first();
        if ($tagHierarchy) {
            $tagHierarchy->delete();
        }
        if (!$hierachy) {
            $data['L1'] = $module_id;
            if ($level == 4) {
                $data['L2'] = $request['L2'];
                $data['level_type'] = $level - 1;
            }
            $hierachy = TagHierarchy::create($data);
        }
        $hierachy->standardTags()->syncWithoutDetaching($id);
    }
}
