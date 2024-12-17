<?php

namespace Modules\Boats\Http\Controllers\API;

use App\Models\Business;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;

class DealershipController extends Controller
{
    use StripeSubscription;
    protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function getGarages($module, $tagId, $typeFlag)
    {
        $garages = Business::when(!request()->input('user_id'), function ($query) {
            $query->whereHas('products');
        }, function ($query) {
            $query->where('owner_id', request()->input('user_id'));
        })->whereHas('standardTags', function ($query) use ($tagId) {
            $query->where('id', $tagId)->orWhere('slug', $tagId);
        })->where(function ($query) use ($typeFlag, $module) {
            // searching by store name
            if (request()->input('keyword')) {
                $query->where('name', 'like', '%' . request()->input('keyword') . '%')->orWhere('street_address', 'like', '%' . request()->input('keyword') . '%');
            }
            // featured stores
            if (request()->input('is_featured')) {
                $value = gettype(request()->is_featured) == 'boolean' ? request()->is_featured : (request()->is_featured == 'true' ? 1 : 0);
                $query->where('is_featured', $value)->where('status', 'active');
            }
            // searching by product level one tag
            if (request()->input('level_three_tag')) {
                $query->whereHas('products.standardTags', function ($query) {
                    $query->where('id', request()->level_three_tag)->orWhere('slug', request()->level_three_tag);
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
            if ($typeFlag && request()->filled('delivery_type')) {
                $query->active()->whereHas('products', function ($query) {
                    $query->whereRelation('vehicle', 'type', request()->input('delivery_type'));
                });
            }

            //filtering boat dealership according to new and old boats
            if ($typeFlag && request()->filled('header_filter')) {
                $query->active()->whereHas('products', function ($query) {
                    $query->whereRelation('boat', 'type', request()->header_filter);
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

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('boats::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('boats::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('boats::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
