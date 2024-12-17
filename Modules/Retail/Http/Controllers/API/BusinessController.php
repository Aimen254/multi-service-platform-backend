<?php

namespace Modules\Retail\Http\Controllers\API;

use App\Models\Business;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;

class BusinessController extends Controller
{
    // use StripeSubscription;
    // protected StripeClient $stripeClient;

    // public function __construct(StripeClient $stripeClient)
    // {
    //     $this->stripeClient = $stripeClient;
    // }
    
    public function getBusinesses($module, $deliveryFlag, $tagId)
    {
        $business = Business::when(!request()->input('user_id'), function ($query) {
            $query->whereHas('products');
        }, function ($query) {
            $query->where('owner_id', request()->input('user_id'));
        })
        // ->when(request()->filled('subscriptionLevel'), function ($query) use ($module) {
        //     $businessIds = $this->getSubscriptionCustomers(request()->input('subscriptionLevel'), $module);
        //     $query->whereHas('businessOwner', function ($subQuery) use ($businessIds) {
        //         $subQuery->whereIn('stripe_customer_id', $businessIds);
        //     });
        // })
        
        ->whereHas('standardTags', function ($subQuery) use ($tagId) {
            $subQuery->where('id', $tagId)->orWhere('slug', $tagId);
        })->with(['deliveryZone'])->where(function ($query) use ($deliveryFlag) {
            // searching by store name
            if (request()->input('keyword')) {
                $query->where('name', 'like', '%' . request()->input('keyword') . '%')->orWhere('street_address', 'like', '%' . request()->input('keyword') . '%');
            }
            // featured stores
            if (request()->input('is_featured')) {
                $value = gettype(request()->is_featured) == 'boolean'
                    ? request()->is_featured
                    : (request()->is_featured == 'true' ? 1 : 0);
                $query->where('is_featured', $value);
            }
            // searching by product level one tag
            if (request()->input('level_three_tag')) {
                $query->whereHas('products.standardTags', function ($query) {
                    $query->where('id', request()->level_three_tag)->orWhere('slug', request()->level_three_tag);
                });
            }
            if ($deliveryFlag && request()->input('delivery_type') == 'pick_up') {
                $query->whereHas('deliveryZone', function ($query) {
                    $query->where('delivery_type', 0);
                });
            }
            if ($deliveryFlag && request()->input('delivery_type') == 'delivery') {
                $query->whereHas('deliveryZone', function ($query) {
                    $query->where('delivery_type', '!=', 0);
                });
            }
            if (request()->input('delivery_type') == 'mail') {
                $query->whereHas('mails');
            }
            if (request()->input('review_rating')) {
                $rating = request()->input('review_rating');
                $query->whereHas('reviews', function ($subQuery) use ($rating) {
                    $subQuery->havingRaw('round(avg(rating)) = ?', ["{$rating}"]);
                });
            }
            // active stores
            // active stores
            if (!request()->input('disableStatusFilter')) {
                $query->where('status', 'active');
            }
        })->withCount(['reviews as reviews_avg' => function ($query) {
            $query->select(DB::raw('avg(rating)'));
        }]);

        if(request()->input('wishlist')) {
            $business->whereDoesntHave('wishList', function ($query) {
                $query->where('user_id', auth()->user()->id);
            });
        }
        
        return $business;
    }       

}
