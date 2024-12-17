<?php

namespace App\Transformers;

use stdClass;
use App\Models\Product;
use App\Models\Attribute;
use App\Transformers\Transformer;

class DreamCarTransformer extends Transformer
{
    public function transform($dreamCar, $options = null)
    {
        $data = [
            'id' => (int) $dreamCar->id,
            'from' => (string) $dreamCar->from,
            'to' => (string) $dreamCar->to,
            'min_price' => $this->getPriceFormat($dreamCar->min_price, $options),
            'max_price' => $this->getPriceFormat($dreamCar->max_price, $options),
            'maker' => $dreamCar->maker ? (new StandardTagTransformer)->transform($dreamCar->maker) : new stdClass,
            'model' => $dreamCar->model ? (new StandardTagTransformer)->transform($dreamCar->model) : new stdClass,
            'level_four_tag' => $dreamCar->level_four_tag ? (new StandardTagTransformer)->transform($dreamCar->level_four_tag) : new stdClass,
            'bed' => $dreamCar->beds ? (new StandardTagTransformer)->transform($dreamCar->beds) : new stdClass,
            'bath' => $dreamCar->baths ? (new StandardTagTransformer)->transform($dreamCar->baths) : new stdClass,
            'square_feet' => $dreamCar->squareFeets ? (new StandardTagTransformer)->transform($dreamCar->squareFeets) : new stdClass,
        ];

        // Retrieve products from the relationship
        $products = $this->getProducts($dreamCar, $options);
        // $dreamCar->products()
        // ->active()
        // ->withCount('comments')
        // ->when(isset($options['module']) && $options['module'] && in_array($options['module'], ['news', 'posts', 'obituaries', 'blogs', 'recipes', 'marketplace', 'taskers', 'events']), function ($query) use ($dreamCar, $options) {
        //     $query->with(['user', 'events'])
        //         ->whereRelation('standardTags', 'id', $dreamCar->module_id);

        //     if ($options['module'] === 'events') {
        //         $query->whereEventDateNotPassed();
        //     }

        //     return $query;
        // })
        // ->take(request()->input('productLimit'))->get();

        $data['products'] = (new ProductTransformer)->transformCollection($products, ['withMinimumData' => true]);
        return $data;
    }

    private function getProducts($filter, $options)
    {
        $products = Product::active()->withCount('comments')->when(isset($options['module']) && $options['module'] && in_array($options['module'], ['news', 'posts', 'obituaries', 'blogs', 'recipes', 'marketplace', 'taskers', 'events']), function ($query) use ($filter, $options) {
            $query->when($options['module'] === 'events', function ($subQuery) {
                $subQuery->whereEventDateNotPassed();
            });
            $query->with(['user', 'events'])
            ->whereRelation('standardTags', 'id', $filter->module_id);
        }, function ($query) use ($filter) {
            // $query->whereHas('business', function ($subQuery) {
            //     $subQuery->where('status', 'active');
            // });
            $query->whereDoesntHave('inappropriateProducts', function ($subQuery) {
                $subQuery->where('user_id', auth('sanctum')->id());
            });
            $query->with('vehicle')->whereStandardTag($filter->module_id);
        })->where(function ($query) use ($filter, $options) {
            $query->where('status', 'active');
            $query->when($filter->make_id, function ($subQuery) use ($filter) {
                $subQuery->whereHas('standardTags', function ($innerQuery) use ($filter) {
                    $innerQuery->where(function ($query) use ($filter) {
                        $query->where('id', $filter->make_id);
                    })->where(function ($subQuery) {
                        $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                            $subQuery->where('level_type', 2);
                        })->orWhereHas('levelTwo');
                    });
                });
            })->when($filter->model_id, function ($subQuery) use ($filter) {
                $subQuery->whereHas('standardTags', function ($innerQuery) use ($filter) {
                    $innerQuery->where(function ($query) use ($filter) {
                        $query->where('id', $filter->model_id);
                    })->where(function ($subQuery) {
                        $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                            $subQuery->where('level_type', 3);
                        })->orWhereHas('levelThree');
                    });
                });
            })->when($filter->level_four_tag_id, function ($query) use ($filter) {
                $query->whereHas('standardTags', function ($innerQuery) use ($filter) {
                    $innerQuery->where(function ($query) use ($filter) {
                        $query->where('id', $filter->level_four_tag_id);
                    })->where(function ($subQuery) {
                        $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                            $subQuery->where('level_type', 4);
                        });
                    });
                });
            })->when($filter->bed, function ($query) use ($filter) {
                $attribute = Attribute::whereIn('slug', ['bed', 'beds'])->first();
                $query->whereHas('standardTags', function ($subQuery) use ($filter, $attribute) {
                    $subQuery->where('id', $filter->bed)->where('product_standard_tag.attribute_id', $attribute->id);
                });
            })->when($filter->bath, function ($query) use ($filter) {
                $attribute = Attribute::whereIn('slug', ['bath', 'baths'])->first();
                $query->whereHas('standardTags', function ($subQuery) use ($filter, $attribute) {
                    $subQuery->where('id', $filter->bath)->where('product_standard_tag.attribute_id', $attribute->id);
                });
            })->when($filter->square_feet, function ($query) use ($filter) {
                $attribute = Attribute::whereIn('slug', ['square-feet', 'square-foot'])->first();
                $query->whereHas('standardTags', function ($subQuery) use ($filter, $attribute) {
                    $subQuery->where('id', $filter->square_feet)->where('product_standard_tag.attribute_id', $attribute->id);
                });
            })->when(!isset($options['module']) && !$options['module'], function ($query) use ($filter) {
                $query->whereHas('vehicle', function ($query) use ($filter) {
                    $query->whereBetween('year', [$filter->from, $filter->to]);
                });
            })
                ->when(isset($options['module']) && $options['module'] && in_array($options['module'], ['real-estate']), function ($query) use ($filter) {
                    $query->whereBetween('price', [$filter->min_price, $filter->max_price]);
                });
        })->latest()->when(request()->input('productLimit'), function ($query) {
            $query->take(request()->input('productLimit'));
        })->get();

        return $products;
    }

    private function getPriceFormat($price, $options)
    {
        if (in_array($options && $options['module'], ['real-estate'])) {
            return number_format($price, 0, "", ",");
        } else {
            return numberFormat($price);
        }
    }
}
