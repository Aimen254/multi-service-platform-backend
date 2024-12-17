<?php

namespace App\Transformers\Search;

use App\Models\Wishlist;
use App\Transformers\Transformer;
use App\Transformers\EventTransformer;
use Modules\Automotive\Entities\VehicleReview;

class SearchProductTransformer extends Transformer
{
    public function transform($product, $options = null)
    {
        $options = \json_decode(json_encode($options));
        $data = [
            'id' => $product->id,
            'uuid' => (string) $product->uuid,
            'name' => (string) in_array($options->L1?->slug, ['notices', 'government']) 
                ? $product?->description : $product?->name,
            'price' => $this->getPriceFormat($product->price, $options->L1),
            'price_type' => $product?->price_type,
            'max_price' => $product->max_price ? $this->getPriceFormat($product->max_price, $options->L1) : null,
            'main_image' => $product->mainImage
                ? getImage($product->mainImage->path, 'image', $product->mainImage->is_external)
                : (in_array($options->L1?->slug, ['government']) ? '' : getImage(NULL, 'image')),
            'date_of_birth' => (string) $product?->date_of_birth,
            'date_of_death' => (string) $product?->date_of_death,
            'reviews_avg' => $this->calculateReviewsAverage($product, $options),
            'in_wishlist' => $this->productsExistsInWishlist($product->id),
            'created_at' => convertDate($product?->created_at, 'M d, Y'),
        ];
        if ($product->business) {
            $data['business'] = [
                'id' => $product->business->id,
                'uuid' => $product->business->uuid,
                'slug' => $product->business->slug,
                'name' => $product->business->name,
                'logo' =>  $product->business->logo
                    ? getImage($product->business?->logo->path, 'image', $product?->business?->logo?->is_external)
                    : getImage(NULL, 'image'),
            ];
        } else if ($product->user) {
            $data['user'] = [
                'id' => $product->user?->id,
                'name' => $product->user?->getFullName(),
                'avatar' => (string) getImage($product->user->avatar ?? $product->user?->avatar, 'avatar', false),
            ];
        }

        return $data;
    }

    private function calculateReviewsAverage($product, $options)
    {
        if ($options->L1?->slug === 'automotive') {
            $ratings = VehicleReview::where('make_id', $options?->L2?->id)
                ->where('model_id', $options->L3?->id)
                ->where('year', $product->vehicle->year)
                ->get();
        }
        return (float)(in_array($options->L1?->slug, ['automotive']) ? $ratings->avg('overall_rating') : $product->reviews->avg('rating'));
    }

    private function getPriceFormat($price, $levelOneTag)
    {
        if (in_array($levelOneTag->slug, ['automotive', 'boats', 'real-estate'])) {
            return number_format($price, 0, "", ",");
        } else {
            return numberFormat($price);
        }
    }

    private function productsExistsInWishlist($id)
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $wishlist = Wishlist::where('user_id', $user->id)->where('model_id', $id)->where('model_type', 'App\Models\Product')->first();
            return $wishlist ? true : false;
        } else {
            return false;
        }
    }
}
