<?php

namespace App\Traits;

use Exception;
use App\Models\StandardTag;
use Illuminate\Support\Facades\Log;

trait TopSearches
{

    public static function saveSearch($user, $module = null)
    {
        try {
            if ((request()->filled('keyword') && request()->filled('L1')) || (request()->filled('keyword') && $module)) {
                $moduleTag = $module ? $module : StandardTag::where('id', request()->L1)->first();
                if ($moduleTag && ($moduleTag->slug == 'automotive' || $moduleTag->slug == 'boats') && !$user->searchHistory()->where('module_id', $moduleTag->id)->where('keyword', request()->keyword)->exists()) {
                    $searchHistoryCount = $user->searchHistory()->where('module_id', $moduleTag->id)->count();
                    if ($searchHistoryCount > 5) {
                        $user->searchHistory()->where('module_id', $moduleTag->id)->first()->delete();
                    }
                    $user->searchHistory()->create([
                        'keyword' => request()->keyword,
                        'module_id' => $moduleTag->id
                    ]);
                }
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
