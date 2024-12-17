<?php

namespace App\Traits;

use App\Models\BusinessCategory;
use App\Models\BusinessBusinessCategory;
use App\Models\Business;;
trait ActiveInactiveChildBusinessCategories
{
    /**
     * To activate all child categories of a parent.
     *
     * @param $businessCategory
     */
    public static function activeInActiveChilds(BusinessCategory $businesCategory)
    {
        foreach ($businesCategory->childrens as $child) {
            $child->update(['status' => $businesCategory->status]);
            if (isset($child->childrens)) {
                self::activeInActiveChilds($child);
            }
        }
    }

    /**
     * To de attach categories to business.
     *
     * @param $businesCategory
     */
    public static function attachOrDeattachCategories(BusinessBusinessCategory $businesCategory)
    {
        $category = BusinessCategory::findOrFail($businesCategory->business_category_id);
        $business = Business::findOrFail($businesCategory->business_id);
        self::detachCatgories($category, $business);
    }
    /**
     * To attach all child categories to business.
     *
     * @param $businesCategory
     */
    public static function attachChildCategories($childrens, $business)
    {
        foreach ($childrens as $child) {
            $business->businessCategories()->attach($child->id);
            if (isset($child->childrens)) {
                self::attachChildCategories($child->childrens, $business);
            }
        }
    }

    /**
     * To de attach categories to business.
     *
     * @param $businesCategory
     */
    public static function detachCatgories($category, $business)
    {
        foreach ($category->childrens as $child) {
            $business_category = $business->businessCategories()
                ->where('business_category_id', $child->id)->first();
            if ($business_category) {
                $business->businessCategories()->detach($child->id);
            }
        }
    }
    /**
     * To attach all parent categories to business.
     *
     * @param $businesCategory
     */
    public static function attachParentCategories($parent, $business)
    {
        $business_department = $business->businessCategories()
            ->where('business_category_id', $parent->id)->first();
        if (!$business_department) {
            $business->businessCategories()->attach($parent->id);
        }
        if ($parent->parent != null) {
            self::attachParentCategories($parent->parent, $business);
        }
    }
}
