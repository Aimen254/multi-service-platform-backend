<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Traits\MyCategoriesManager;
use Modules\Automotive\Entities\DreamCar;

class MyCategoryRepository
{
    use MyCategoriesManager;

    protected $limit = 4;

    public function __construct()
    {
        $this->limit = request()->input('limit') ? request()->limit : $this->limit;
    }

    // fetch categories
    function activeCategoriesList()
    {
        $categories = DreamCar::where('user_id', request()->user()?->id)->whereHas('products', function($query) {
            $query->active();
        })->latest()->paginate($this->limit);
        return $categories;

    }

    // fetch latest products of categories
    function productsList()
    {
        $user = auth('sanctum')->user();
        $products = Product::active()->with(['business.logo', 'user'])->whereHas('dreamCars', function($query) use($user) {
            $query->where('user_id', $user?->id);
        })->latest()->paginate($this->limit);
        return $products;
    }
}
