<?php

namespace App\Transformers;

use App\Models\StandardTag;
use stdClass;
use Carbon\Carbon;
use App\Transformers\Transformer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Transformers\UserTransformer;
use App\Transformers\ProductTransformer;

class WishlistTransformer extends Transformer
{
    public function transform($wishlist, $options = null)
    {
        $data = [
            'id' => (int) $wishlist->id,
        ];
        if (isset($options['type']) && $options['type'] == 'business') {
            if (isset($options['withDelivery']) && $options['withDelivery'] == 'delivery') {
                $business = $wishlist->model()->whereHas('deliveryZone', function ($query) {
                    $query->where('delivery_type', '!=', 0);
                })->first();
                $data['business'] = $business && $business->count() > 0 ? (new BusinessTransformer)->transform($business, $options) : new stdClass();
                return $data;
            } else if (isset($options['withDelivery']) && $options['withDelivery'] == 'new') {
                $business = $wishlist->model()->whereHas('products', function ($query) {
                    $query->whereRelation('vehicle', 'type', 'new');
                })->first();
                $data['business'] = $business && $business->count() > 0 ? (new BusinessTransformer)->transform($business, $options) : new stdClass();
            } else if (isset($options['withDelivery']) && $options['withDelivery'] == 'used') {
                $business = $wishlist->model()->whereHas('products', function ($query) {
                    $query->whereRelation('vehicle', 'type', 'used');
                })->first();
                $data['business'] = $business && $business->count() > 0 ? (new BusinessTransformer)->transform($business, $options) : new stdClass();
            } else {
                $data['business'] = $wishlist->model->count() > 0 ? (new BusinessTransformer)->transform($wishlist->model, $options) : new stdClass();
            }
        } else if (isset($options['type']) && $options['type'] == 'product') {
            $productOptions = [
                "withVariants" => true,
                'withMinimumData' => true
            ];
            if (isset($options['withDelivery']) && $options['withDelivery'] == 'delivery') {
                $product = $wishlist->product()->withCount('comments')->where('is_deliverable', '<>', 0)->whereHas('business.deliveryZone', function ($query) {
                    $query->where('delivery_type', '!=', 0);
                })->first();
                $data['product'] = $product && $product->count() > 0 ? (new ProductTransformer)->transform($product, $productOptions) : new stdClass();
            } else if (isset($options['withDelivery']) && $options['withDelivery'] == 'new') {
                $product = $wishlist->product()->withCount('comments')->whereRelation('vehicle', 'type', 'new')->first();
                $data['product'] = $product && $product->count() > 0 ? (new ProductTransformer)->transform($product, $productOptions) : new stdClass();
            } else if (isset($options['withDelivery']) && $options['withDelivery'] == 'used') {
                $product = $wishlist->product()->withCount('comments')->whereRelation('vehicle', 'type', 'used')->first();
                $data['product'] = $product && $product->count() > 0 ? (new ProductTransformer)->transform($product, $productOptions) : new stdClass();
            } else {
                $module = StandardTag::where('id', request()->module)->orWhere('slug', request()->module)->first();
                $product = $wishlist->product()->withCount(['comments', 'wishList', 'views'])->when($module && in_array($module->slug, ['boats', 'automotive']), function ($query) {
                    $query->with('vehicle', 'vehicle.model', 'vehicle.maker');
                })->when($module && in_array($module->slug, ['posts', 'news', 'obituaries', 'recipes', 'blogs', 'marketplace', 'taskers', 'events']), function ($query) {
                    $query->with(['user', 'events']);
                })->first();
                $data['product'] = $wishlist->product->count() > 0 ? (new ProductTransformer)->transform($product ? $product : $wishlist->product, $productOptions) : new stdClass();
            }
        } elseif (isset($options['type']) && $options['type'] == 'user') {
            $data['user'] = $wishlist->model->count() > 0 ? (new UserTransformer)->transform($wishlist->model, $options) : new stdClass();
        } else {
            $data['standardTags'] = $wishlist->model->count() > 0 ? (new StandardTagTransformer)->transform($wishlist->model) : new stdClass();
        }
        return $data;
    }
}
