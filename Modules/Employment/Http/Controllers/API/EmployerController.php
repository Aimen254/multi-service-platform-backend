<?php

namespace Modules\Employment\Http\Controllers\API;

use App\Models\Business;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;

class EmployerController extends Controller
{
    use StripeSubscription;
    protected StripeClient $stripeClient;

    // public function __construct(StripeClient $stripeClient)
    // {
    //     $this->stripeClient = $stripeClient;
    // }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($module, $tagId)
    {
        $order_by = request()->input('order') ??  'desc';
        $keyword = request()->input('keyword') ?? '';

        $business = Business::when(!request()->input('user_id'), function ($query) {
            $query->whereHas('products');
        }, function ($query) {
            $query->where('owner_id', request()->input('user_id'));
        })->when(request()->filled('subscriptionLevel'), function ($query) use ($module) {
            $businessIds = $this->getSubscriptionCustomers(request()->input('subscriptionLevel'), $module);
            $query->whereHas('businessOwner', function ($subQuery) use ($businessIds) {
                $subQuery->whereIn('stripe_customer_id', $businessIds);
            });
        })->whereHas('standardTags', function ($subQuery) use ($tagId) {
            $subQuery->where('id', $tagId)->orWhere('slug', $tagId);
        })->where(function ($query) use ($keyword, $order_by) {
            // searching by store name
            if (request()->input('keyword')) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . request()->input('keyword') . '%')
                        ->orWhere('street_address', 'like', '%' . request()->input('keyword') . '%');
                });
            }
            if (request()->input('order')) {
                $query->orderBy('created_at',  $order_by);
            }
            // searching by product level one tag
            if (request()->input('level_three_tag')) {
                $query->whereHas('products.standardTags', function ($query) {
                    $query->where('id', request()->level_three_tag)->orWhere('slug', request()->level_three_tag);
                });
            }
            if (request()->input('review_rating')) {
                $rating = request()->input('review_rating');
                $query->whereHas('reviews', function ($subQuery) use ($rating) {
                    $subQuery->havingRaw('round(avg(rating)) = ?', ["{$rating}"]);
                });
            }
            // featured stores
            if (request()->input('is_featured')) {
                $value = gettype(request()->is_featured) == 'boolean'
                    ? request()->is_featured
                    : (request()->is_featured == 'true' ? 1 : 0);
                $query->where('is_featured', $value);
            }
            // active stores
            $query->when(!request()->input('disableStatusFilter'), function ($subQuery) {
                $subQuery->where('status', 'active');
            });
        })->withCount(['reviews as reviews_avg' => function ($query) {
            $query->select(DB::raw('avg(rating)'));
        }]);

        return $business;
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('employment::create');
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
        return view('employment::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('employment::edit');
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