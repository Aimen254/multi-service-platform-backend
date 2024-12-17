<?php

namespace App\Http\Controllers\API;

use stdClass;
use Stripe\StripeClient;
use App\Models\CreditCard;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use App\Http\Controllers\Controller;
use Modules\Retail\Entities\SubscriptionPermission;

class SubuscriptionController extends Controller
{
    use StripeSubscription;
    protected StripeClient $stripeClient;
    private $product = [];

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($moduleId)
    {
        $user = request()->user();
        $moduleSlug = StandardTag::where('id', $moduleId)
            ->orWhere(function ($query) use ($moduleId) {
                $query->where('slug', $moduleId)
                    ->whereNotExists(function ($subquery) use ($moduleId) {
                        $subquery->from('standard_tags')
                            ->where('id', $moduleId);
                    });
            })
            ->firstOrFail()->slug;
        if ($user) {
            if ($moduleSlug) {
                $products = $this->stripeClient->products->search([
                    'limit' => 100,
                    'query' => 'metadata[\'module\']:\'' . $moduleSlug . '\'',
                ]);
            }
            $subscription = $this->stripeClient->subscriptions->all([
                'limit' => 100,
                'customer' => $user->stripe_customer_id,
                'status' => 'active',
            ]);
            $filteredSubscriptions = [];

            // Check each subscription's metadata for the desired module
            $filteredSubscriptions = array_filter($subscription->data, function ($subscription) use ($moduleSlug) {
                return isset($subscription->metadata->module) && $subscription->metadata->module === $moduleSlug;
            });
            $meta = Arr::pluck($subscription, 'metadata.module');
            $customerSubscriptions = collect(Arr::pluck($subscription, 'items.data'))->flatten(1);
            $subscriptionsPlainData = $customerSubscriptions->values()->all();
            $customerSubscriptions = Arr::pluck($subscriptionsPlainData, 'plan.product');
            collect($products->data)->each(function ($item, $key) use ($customerSubscriptions, $meta, $moduleSlug) {
                if ($item->active) {
                    $singleProduct = new stdClass();
                    $price = $this->stripeClient->prices->retrieve(
                        $item->default_price,
                        []
                    );
                    $permissions = SubscriptionPermission::where('product_id', $item->id)->get();
                    $singleProduct->product = $item;
                    $singleProduct->price = $price;
                    $singleProduct->currentPlan = count($meta) && $meta[0] == $moduleSlug ? in_array($item->id, $customerSubscriptions) : false;
                    $singleProduct->permissions = $permissions;
                    array_push($this->product, $singleProduct);
                }
            });
            $cards = CreditCard::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        }
        $result = [
            'data' => array_values($filteredSubscriptions),

        ];
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => [
                'user' => $user,
                'plans' => $this->product,
                'subscriptions' => $result,
                'cards' => $cards,
                'module' => $moduleSlug
            ],
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($moduleId, $id)
    {
        try {
            $subscription = $this->stripeClient->subscriptions->cancel(
                $id,
                []
            );
            $moduleTags = StandardTag::whereSlug($subscription['metadata']['module'])->firstOrFail();
            if ($subscription) {
                $permission = UserSubscription::where('module_id', $moduleTags->id)->where('user_id', request()->user()->id)->where('product_id', $subscription['plan']['product'])->delete();
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'message' => 'Subscription cancelled succesfully.',
                ], JsonResponse::HTTP_OK);
            } else {
                return response()->json([
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                    'message' => 'Cancellation of subscription Failed Due To Some Error.'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
        } catch (\Stripe\Exception\CardException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getPlan(Request $request, $moduleId)
    {
        try {
            $moduleSlug = StandardTag::where('id', $moduleId)
                ->orWhere(function ($query) use ($moduleId) {
                    $query->where('slug', $moduleId)
                        ->whereNotExists(function ($subquery) use ($moduleId) {
                            $subquery->from('standard_tags')
                                ->where('id', $moduleId);
                        });
                })
                ->firstOrFail()->slug;
            $retrivedSubscription = request()->input('id') ? $this->stripeClient->subscriptions->retrieve(request()->id) : null;
            if ($request->user() && $request->user()->stripe_customer_id && !$retrivedSubscription) {
                $subscription = $this->stripeClient->subscriptions->create([
                    'customer' => $request->user()->stripe_customer_id,
                    'default_payment_method' => $request->input('card'),
                    'collection_method' => 'charge_automatically',
                    'items' => [
                        ['price' => $request->input('priceId')],
                    ],
                    'metadata' => [
                        'module' => $moduleSlug,
                    ],
                ]);
                if ($request->has('productId') && $request->has('module')) {
                    $this->updateUserMeta($request->input('productId'), $moduleSlug);
                }
                if ($subscription) {
                    return response()->json([
                        'status' => JsonResponse::HTTP_OK,
                        'message' => 'Subscribed succesfully.',
                        'subscription' => $subscription,
                    ], JsonResponse::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => JsonResponse::HTTP_NOT_FOUND,
                        'message' => 'Subscription Failed Due To Some Error.'
                    ], JsonResponse::HTTP_NOT_FOUND);
                }
            } else {
                $subscriptionUpdate = $this->stripeClient->subscriptions->update(
                    request()->id,
                    [
                        'items' => [
                            [
                                'id' => $retrivedSubscription->items->data[0]->id,
                                'price' => $request->input('priceId'),
                            ],
                        ],
                    ],
                );
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'message' => 'Subscription updated succesfully.',
                    'subscription' => $subscriptionUpdate,
                ], JsonResponse::HTTP_OK);
            }
        } catch (\Stripe\Exception\CardException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function payLastInvoice(Request $request)
    {
        try {
            if ($request->has('subscription')) {
                $latestInvoice = $request->input('subscription')['latest_invoice'];
                $paymendMethodId = $request->input('subscription')['card'];
                $invoice = $this->stripeClient->invoices->pay(
                    $latestInvoice,
                    [
                        'payment_method' => $paymendMethodId,
                    ]
                );
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'message' => 'Invoice Paid succesfully.',
                ], JsonResponse::HTTP_OK);
            }
        } catch (\Stripe\Exception\CardException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
