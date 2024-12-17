<?php

namespace App\Traits;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Modules\Automotive\Entities\DreamCar;

trait MyCategoriesManager
{
    protected $limit = 4;
    protected $modulesBasedOnYearFilter = ['automotive', 'boats'];

    function categoryWithActiveRecords(DreamCar $category): bool
    {
        $count = Product::active()->whereHas('standardTags', function ($query) use ($category) {
            $query->whereIn('id', [
                $category->module_id,
                $category->make_id,
                $category->model_id,
                $category->level_four_tag_id
            ])->select('*', DB::raw('count(*) as total'))->when(!$category->level_four_tag, function ($query) {
                $query->having('total', '>=', 3);
            }, function ($query) {
                $query->having('total', '>=', 4);
            });
        })->when(\in_array($category->module->slug, $this->modulesBasedOnYearFilter), function ($query) use ($category) {
            $query->whereHas('vehicle', function ($subQuery) use ($category) {
                $subQuery->whereBetween(
                    'year',
                    [$category->from, $category->to]
                );
            });
        })
            ->count();
        return $count > 0 ? false : true;
    }

    function categoryLatestProducts(DreamCar $category)
    {
        return Product::active()->with(['business', 'user'])->whereHas('standardTags', function ($query) use ($category) {
            $query->whereIn('id', [
                $category->module_id,
                $category->make_id,
                $category->model_id,
                $category->level_four_tag_id
            ])->select('*', DB::raw('count(*) as total'))->when(!$category->level_four_tag, function ($query) {
                $query->having('total', '>=', 3);
            }, function ($query) {
                $query->having('total', '>=', 4);
            });
        })->when(\in_array($category->module->slug, $this->modulesBasedOnYearFilter), function ($query) use ($category) {
            $query->whereHas('vehicle', function ($subQuery) use ($category) {
                $subQuery->whereBetween(
                    'year',
                    [$category->from, $category->to]
                );
            });
        })->latest()->take($this->limit)->get();
    }
}
