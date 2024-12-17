<?php

namespace App\Traits;

use App\Models\Business;

trait BusinessProducts
{
    /**
     * To check count of business's products
     *
     * @param $business
     */
    public static function getProductCount(Business $business)
    {
        return $business->products()->count() > 0 ? \false : \true;
    }
}
