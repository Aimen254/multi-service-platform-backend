<?php

namespace Modules\Automotive\Http\Controllers\API;

use App\Models\Business;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;

class GarageController extends Controller
{
    // use StripeSubscription;
    // protected StripeClient $stripeClient;

    // public function __construct(StripeClient $stripeClient)
    // {
    //     $this->stripeClient = $stripeClient;
    // }
    /**
     * Show all garages.
     * @return Renderable
     */
    public function getGarages($module, $tagId, $typeFlag)
    {
        $garages = Business::when(!request()->input('user_id'), function ($query) {
            // $query->whereHas('products');
        }, function ($query) {
            $query->where('owner_id', request()->input('user_id'));
        })->whereHas('standardTags', function ($query) use ($tagId, $module) {
            $query->where('id', $module)->orWhere('slug', $module);
        })->where(function ($query) use ($typeFlag, $module) {
            // searching by store name
            if (request()->input('keyword')) {
                $query->where('name', 'like', '%' . request()->input('keyword') . '%');
            }
            // featured stores
            if (request()->input('is_featured')) {
                $value = gettype(request()->is_featured) == 'boolean' ? request()->is_featured : (request()->is_featured == 'true' ? 1 : 0);
                $query->where('is_featured', $value);
            }

            if (request()->input('level_two_tag')) {
                $query->whereHas('standardTags', function ($query) {
                    $query->where('id', request()->level_two_tag)->orWhere('slug', request()->level_two_tag);
                });
            }

            // searching by product level one tag
            if (request()->input('level_three_tag')) {
                $query->whereHas('standardTags', function ($query) {
                    $query->where('id', request()->level_three_tag)->orWhere('slug', request()->level_three_tag);
                    // $query->whereHas('productTags', function ($query) {
                    //     $query->where('status', 'active');
                    // });
                });
            }
            // searching by garage review rating
            if (request()->input('review_rating')) {
                $rating = request()->input('review_rating');
                $query->whereHas('reviews', function ($subQuery) use ($rating) {
                    $subQuery->havingRaw('round(avg(rating)) = ?', ["{$rating}"]);
                });
            }
            //filtering dealership according to new and old vehicles
            if ($typeFlag && request()->filled('header_filter')) {
                $query->active()->whereHas('products', function ($query) {
                    $query->whereRelation('vehicle', 'type', request()->input('header_filter'));
                });
            }
            //get customer business
            if (request()->input('getCustomerBusiness') && request()->input('ownerId')) {
                $query->whereRelation('businessOwner', 'id', request()->input('ownerId'));
            }
            // active stores
            if (!request()->input('disableStatusFilter')) {
                $query->where('status', 'active');
            }
            $query->when(request()->filled('subscriptionLevel'), function ($query) use ($module) {
                $businessIds = $this->getSubscriptionCustomers(request()->input('subscriptionLevel'), $module);
                $query->whereHas('businessOwner', function ($subQuery) use ($businessIds) {
                    $subQuery->whereIn('stripe_customer_id', $businessIds);
                });
            });
        })->withCount(['reviews as reviews_avg' => function ($query) {
            $query->select(DB::raw('avg(rating)'));
        }]);
        return $garages;
    }
}
