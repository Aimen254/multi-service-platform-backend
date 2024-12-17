<?php
namespace App\Traits;
use App\Models\StandardTag;

trait StandardTags
{
    /**
     * To check relation of standatrd tag with business, product and tag hierarachy
     *
     * @param $standardTag
     */
    public static function checkRelation(StandardTag $standardTag)
    {
        $tagExsists = StandardTag::where('id', $standardTag->id)->where(function ($query) {
            $query->whereHas('productTags')
                ->orWhereHas('businesses')
                ->orWhereHas('levelOne')
                ->orWhereHas('levelTwo')
                ->orWhereHas('levelThree')
                ->orWhereHas('levelFour')
                ->orWhereHas('tagHierarchies')
                ->orWhereHas('tags_');
        })->exists();
        return $tagExsists;
    }
}
