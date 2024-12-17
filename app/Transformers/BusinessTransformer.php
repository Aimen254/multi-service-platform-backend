<?php

namespace App\Transformers;

use stdClass;
use App\Models\Wishlist;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use App\Models\BusinessWishlist;
use App\Transformers\Transformer;
use Illuminate\Support\Facades\Log;
use App\Transformers\UserTransformer;
use PhpParser\PrettyPrinter\Standard;
use App\Transformers\MediaTransformer;
use App\Transformers\ReviewTransformer;
use App\Transformers\MailingTransformer;
use App\Transformers\ProductTransformer;
use App\Transformers\StandardTagTransformer;
use Illuminate\Database\Eloquent\Collection;
use App\Transformers\DeliveryZoneTransformer;
use App\Transformers\Business\HolidayTransformer;
use App\Transformers\Business\SettingTransformer;
use App\Transformers\Business\ScheduleTransformer;

class BusinessTransformer extends Transformer
{
    public function transform($business, $options = null)
    {


        $levelTwoTagOptions = [
            'withProducts' => request()->boolean('withLevelTwoTagProducts') ? true : false,
        ];
        $moduleId = isset($options['module']) ? $options['module'] : null;
        $module = StandardTag::where('slug', $moduleId)->orWhere('id', $moduleId)->first();

        $levelTwoTags = $business->standardTags()->whereHas('productTags', function ($query) use ($business, $options) {
            $query->when(isset($options['header_filter']) && $options['header_filter'], function ($subQuery) use ($options) {
                switch ($options['header_filter']) {
                    case 'delivery':
                        $subQuery->where('is_deliverable', '!=', 0)
                            ->whereRelation('business.deliveryZone', 'delivery_type', '!=', 0);
                        break;
                    case 'new':
                    case 'used':
                        $subQuery->whereRelation('vehicle', 'type', $options['header_filter']);
                        break;
                }
            })->active();
            $query->where('status', 'active')->where('business_id', $business->id);
        })
        ->where(function ($query) use ($module) {
            $query->whereHas('levelTwo', function ($query) use ($module) {
                $query->where('L1', $module->id);
            })->orWhereHas('tagHierarchies', function ($query) use ($module) {
                $query->where('L1', $module->id)
                    ->where('level_type', 2);
            });
        })
        ->where('status', 'active')->get();
        $levelThreeTags = $business->standardTags()->whereHas('productTags', function ($query) use ($business, $options) {
            $query->when(isset($options['header_filter']) && $options['header_filter'], function ($subQuery) use ($options) {
                switch ($options['header_filter']) {
                    case 'delivery':
                        $subQuery->where('is_deliverable', '!=', 0)
                            ->whereRelation('business.deliveryZone', 'delivery_type', '!=', 0);
                        break;
                    case 'new':
                    case 'used':
                        $subQuery->whereRelation('vehicle', 'type', $options['header_filter']);
                        break;
                }
            })->active();
            $query->where('status', 'active')->where('business_id', $business->id);
        })->where(function ($query) use ($module) {
            $query->whereHas('levelThree', function ($query) use ($module) {
                $query->where('L1', $module->id);
            })->orWhereHas('tagHierarchies', function ($query) use ($module) {
                $query->where('L1', $module->id);
                $query->where('level_type', 3);
            });
        })->where('status', 'active');
        $data = [
            'id' => (int) $business->id,
            'uuid' =>  $business->uuid,
            'name' => (string) $business->name,
            'user_name' => (string) $business->user_name,
            'slug' => $business->slug,
            'email' => (string) $business->email,
            'phone' => (string) $business->phone,

            'mobile' => (string) $business->mobile,
            'logo' => $business->logo
                ? getImage($business->logo->path, 'image', $business->logo->is_external)
                : getImage(NULL, 'image'),
            'latitude' => (float) $business->latitude,
            'longitude' => (float) $business->longitude,
            'thumbnail' =>  $business->thumbnail
                ? getImage($business->thumbnail->path, 'image')
                : getImage(NULL, 'image'),
            'banner' => $business->banner
                ? getImage($business->banner->path, 'image', $business->banner->is_external)
                : getImage(NULL, 'banners'),
            'rejection_reason' => $business->rejection_reason,
            'secondary_banner' => $business->secondaryBanner
                ? getImage($business->secondaryBanner->path, 'image', $business->secondaryBanner->is_external)
                : getImage(NULL, 'secondaryBanner'),
            'address' => (string) $business->address,
            'is_featured' => (bool) $business->is_featured,
            'review_count' => (int) $business->reviews()->count(),
            'reviews_avg' => (float) $business->reviews()->avg('rating'),
            'street_address' => (string) $business->street_address,
            'in_wishlist' => $this->businessExistsInWishlist($business->id),
            'short_description' => (string) $business->short_description,
            'city' => $business->city,
            'home_delivery' => $business->home_delivery,
            'virtual_appointments' => $business->virtual_appointments,
            'status' => $business->status,
            'message'=> $business->message,
            'long_description'=> $business->long_description,
            'shipping_and_return_policy'=> $business->shipping_and_return_policy,
            'shipping_and_return_policy_short'=> $business->shipping_and_return_policy_short,


        ];

        if (isset($options['withOwner']) && $options['withOwner']) {
            $data['business_owner'] = $business->businessOwner
                ? (new UserTransformer)->transform($business->businessOwner) : new stdClass();
        }
        if (isset($options['withProducts']) && $options['withProducts']) {
            $data['products'] = (new ProductTransformer)->transformCollection($this->getProductsWithImage($business->products, 5, 3));
        }

        if (isset($options['withMailing']) && $options['withMailing']) {
            $cartTotal = (int)round($options['mailingPrice']);
            $mails = $business->mails()->where('status', 'active')->where('minimum_amount', '<=', $cartTotal)->get();
            $data['mailing'] = (new MailingTransformer)->transformCollection($mails);
        }

        if (isset($options['withDetails']) && $options['withDetails']) {
            $data['can_add_review'] = $this->canUserAddReview($business, $module);
            $data['message'] = (string) $business->message;
            $data['long_description'] = (string) $business->long_description;
            $data['shipping_and_return_policy_short'] = (string) $business->shipping_and_return_policy_short;
            $data['shipping_and_return_policy'] = (string) $business->shipping_and_return_policy;
            $data['secondary_images'] = (new MediaTransformer)->transformCollection($business->secondaryImages);
            $data['schedules'] = (new ScheduleTransformer)->transformCollection($business->businessschedules);
            $data['business_holidays'] = (new HolidayTransformer)->transformCollection($business->businessHolidays);
            $data['reviews'] = (new ReviewTransformer)->transformCollection($business->reviews);
            $data['deliveryZone'] = (new DeliveryZoneTransformer)->transform($business->deliveryZone);
            $data['settings'] = (new SettingTransformer)->transformCollection($business->settings);
        }
        if (isset($options['withLevelTwoTags']) && $options['withLevelTwoTags']) {
            $data['level_two_tags'] = $levelTwoTags->count() > 0
                ? (new StandardTagTransformer)->transformCollection($levelTwoTags, $levelTwoTagOptions) : [];
        }

        if (isset($options['withLevelThreeTags']) && $options['withLevelThreeTags']) {
            $data['level_three_tags'] = $levelThreeTags->count() > 0
                ? (new StandardTagTransformer)->transformCollection($levelThreeTags->limit(3)->get()) : [];
        }

        if (isset($options['withUsers'])) {
            $data['users'] = $business->users ? (new UserTransformer)->transformCollection($business->users()->whereHas('products')->get()) : new stdClass();
        }
        return $data;
    }

    private function getProductsWithImage($products, $checkLimit, $limit)
    {
        $products = $products->whereNotNull('mainImage')->take($checkLimit);
        $productArray = [];
        foreach ($products as $product) {
            if (checkImageIsBroken($product->mainImage)) {
                $productArray[] = $product;
            }
            if (count($productArray) >= $limit) {
                break;
            }
        }
        return new Collection($productArray);
    }

    private function businessExistsInWishlist($id)
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $wishlist = Wishlist::where('user_id', $user->id)->where('model_id', $id)->where('model_type', 'App\Models\Business')->first();
            return $wishlist ? true : false;
        } else {
            return false;
        }
    }

    private function isBodyStyleTag($tag)
    {
        return Arr::has(config()->get('automotive.body_styles'), $tag->name);
    }

    private function canUserAddReview($business, $module)
    {
        $user = auth('sanctum')->user();
        if (in_array($module->slug, ['services', 'employment', 'government'])) {
            return true;
        }
        if ($user) {
            $orders = $business->orders()->where('model_id', $user->id)->first();
            return $orders ? true : false;
        } else {
            return false;
        }
    }
}
