<?php

namespace App\Http\Controllers\Admin\Subscription;

use stdClass;
use App\Models\User;
use Inertia\Inertia;
use Stripe\StripeClient;
use App\Models\CreditCard;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Enums\Business\Settings\DeliveryType;
use Modules\Retail\Entities\SubscriptionPermission;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubscribeController extends Controller
{
    use StripeSubscription;
    private $product = [];

    public function __construct(protected StripeClient $stripeClient)
    {
        $this->middleware('can_access_news_plans');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $moduleSlug = $request->has('moduleSlug')
            ? $request->input('moduleSlug')
            : StandardTag::where('slug', 'news')->where('type', 'module')->firstOrFail()->slug;

        $products = $this->stripeClient->products->search([
            'limit' => 100,
            'query' => 'active:\'true\' AND metadata[\'module\']:\'' . $moduleSlug . '\'',
        ]);

        $subscription = $this->stripeClient->subscriptions->all([
            'limit' => 20,
            'customer' => request()->user()->stripe_customer_id
        ]);
        $customerSubscriptions = collect(Arr::pluck($subscription, 'items.data'))->flatten(1);
        $subscriptionsPlainData = $customerSubscriptions->values()->all();
        $customerSubscriptions = Arr::pluck($subscriptionsPlainData, 'plan.product');
        collect($products->data)->each(function ($item, $key) use ($customerSubscriptions) {
            if ($item->active) {
                $singleProduct = new stdClass();
                $price = $this->stripeClient->prices->retrieve(
                    $item->default_price,
                    []
                );
                $permissions = SubscriptionPermission::where('product_id', $item->id)->get();
                $singleProduct->product = $item;
                $singleProduct->price = $price;
                $singleProduct->currentPlan = in_array($item->id, $customerSubscriptions);
                $singleProduct->permissions = $permissions;
                array_push($this->product, $singleProduct);
            }
        });
        $cards = CreditCard::where('user_id', request()->user()->id)->orderBy('id', 'desc')->get();
        $modules = StandardTag::when(
            auth()?->user()?->hasRole('admin') || auth()?->user()?->hasRole('reporter'),
            function ($query) {
                $query->whereType('module');
            },
            function ($query) {
                $query->whereType('module')->where('slug', '!=', 'news');
            }
        )->get();
        return Inertia::render('Subscription/BusinessOwnerPlans/Index', [
            'plans' => $this->product,
            'subs' => $subscription,
            'cards' => $cards,
            'modules' => $modules,
            'selected_module' => $moduleSlug
        ]);
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
        $subscriptions = $this->stripeClient->subscriptions->all(
            [
                'limit' => 10,
                'price' => $id,
                'customer' => request()->user()->stripe_customer_id
            ]
        );
        $subscription = $this->stripeClient->subscriptions->retrieve(
            $subscriptions->data[0]->id,
            []
        );
        $product = $this->stripeClient->products->retrieve(
            $subscription->plan->product,
            []
        );
        $invoice = $this->stripeClient->invoices->all([
            'subscription' => $subscription->id,
            'limit' => 100
        ]);
        $upcomingInvoice = $this->stripeClient->invoices->upcoming([
            'subscription' => $subscription->id
        ]);
        $user = User::with('addresses')->where('id', request()->user()->id)->where('stripe_customer_id', $subscription->customer)->first();
        $card = CreditCard::where('user_id', request()->user()->id)->where('payment_method_id', $subscription->default_payment_method)->first();
        return Inertia::render('Subscription/BusinessOwnerPlans/Plan', [
            'subs' => $subscription,
            'product' => $product,
            'invoice' => $invoice,
            'upcomingInvoice' => $upcomingInvoice,
            'user' => $user,
            'card' => $card,
        ]);
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
        try {
            $newPlan = $request->input('newPlan');
            $selectedBusinessesIds = $request->input('selectedBusinesses');
            $subscription = $this->stripeClient->subscriptions->retrieve($id);
            $module = $subscription->metadata->module;
            if ($request->has('selectedBusinesses')) {
                //deactivate all active businesses
                $activeBusinesses = $request->user()->businesses()->active()->with('standardTags', function ($query) {
                    $query->whereType('module');
                })->whereHas('standardTags', function ($query) use ($module) {
                    $query->whereSlug($module);
                })->update(['status' => 'inactive']);
                //activate selected businesses
                $businessesToInactive = $request->user()->businesses()->whereIn('id', $selectedBusinessesIds)->update(['status' => 'active']);
            }
            $this->checkSubscriptionProductLimit($newPlan, $subscription, $request);
            $subscriptionUpdate = $this->stripeClient->subscriptions->update(
                $id,
                [
                    'items' => [
                        [
                            'id' => $subscription->items->data[0]->id,
                            'price' =>  $newPlan['default_price'],
                        ],
                    ],
                ],
            );
            if ($newPlan) {
                $this->updateUserMeta($newPlan['id'], $module);
            }
            if ($subscriptionUpdate) {
                flash('Subscription changed succesfully', 'success');
                return redirect()->back();
            } else {
                flash('Upgradation of subscription Failed Due To Some Error', 'danger');
                return redirect()->back();
            }
        } catch (\Stripe\Exception\CardException $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $subscription = $this->stripeClient->subscriptions->cancel(
                $id,
                []
            );
            $moduleTags = StandardTag::whereSlug($subscription['metadata']['module'])->firstOrFail();
            if ($subscription) {
                $permission = UserSubscription::where('module_id', $moduleTags->id)->where('user_id', request()->user()->id)->where('product_id', $subscription['plan']['product'])->delete();
                flash('Subscription cancelled succesfully', 'success');
                return redirect()->back();
            } else {
                flash('Cancellation of subscription Failed Due To Some Error', 'danger');
                return redirect()->back();
            }
        } catch (\Stripe\Exception\CardException $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * getplan
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPlan(Request $request)
    {
        try {
            $subscription = $this->stripeClient->subscriptions->create([
                'customer' => $request->user()->stripe_customer_id,
                'default_payment_method' => $request->input('card'),
                'collection_method' => 'charge_automatically',
                'items' => [
                    ['price' => $request->input('priceId')],
                ],
                'metadata' => [
                    'module' => $request->input('module'),
                ],
            ]);
            if ($request->has('productId') && $request->has('module')) {
                $this->updateUserMeta($request->input('productId'), $request->input('module'));
            }
            if ($subscription) {
                flash('Subscribed succesfully', 'success');
                return redirect()->back();
            } else {
                flash('Subscription Failed Due To Some Error', 'danger');
                return redirect()->back();
            }
        } catch (\Stripe\Exception\CardException $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function checkActiveBusinesses(Request $request)
    {
        try {
            $flag = true;
            $data = $request->input('Data');
            $activeSubscription = $data['subscriptionId'][0];
            $currentProduct = $activeSubscription['plan']['product'];
            $module = $activeSubscription['metadata']['module'];
            $newProduct = $data['newPlan']['id'];
            $currentProductPermissions = SubscriptionPermission::where('product_id', $currentProduct)->where('key', 'total_businesses')->first();
            $newProductPermissions = SubscriptionPermission::where('product_id', $newProduct)->where('key', 'total_businesses')->first();
            //active businesses
            $activeBusinesses = $request->user()->businesses()->active()->with('standardTags', function ($query) {
                $query->whereType('module');
            })->whereHas('standardTags', function ($query) use ($module) {
                $query->whereSlug($module);
            })->count();
            //all businesses
            $allBusinesses = $request->user()->businesses()->with('standardTags', function ($query) {
                $query->whereType('module');
            })->whereHas('standardTags', function ($query) use ($module) {
                $query->whereSlug($module);
            })->get();
            //verifying
            if ((int)$currentProductPermissions->value >  (int)$newProductPermissions->value) {
                if ((int)$newProductPermissions->value == -1) {
                    $flag = true;
                } else if ($activeBusinesses > (int)$newProductPermissions->value) {
                    $flag = false;
                }
            }
            return \response()->json([
                'businesses' => $allBusinesses,
                'flag' => $flag,
                'allowedBusinesses' => $newProductPermissions->value,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return \response()->json([
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
                flash('Invoice Paid succesfully', 'success');
                return redirect()->back();
            }
        } catch (\Stripe\Exception\CardException $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function checkSubscriptionProductLimit($newPlan, $currentPlan, $request)
    {
        $currentProductPermissions = SubscriptionPermission::where('product_id', $currentPlan->plan->product)->where('key', 'total_products')->orWhere('key', 'total_news')->first();
        $newProductPermissions = SubscriptionPermission::where('product_id', $newPlan['id'])->where('key', 'total_products')->orWhere('key', 'total_news')->first();
        $newDeliveryPermissions = SubscriptionPermission::where('product_id', $newPlan['id'])->where('key', 'delivery')->first();
        $activebusinesses = $request->user()->businesses()->active()->get();
        //Deactivating products on subscription downgrade
        if ((int)$currentProductPermissions->value >= (int)$newProductPermissions->value) {
            return;
        } else {
            $activebusinesses->each(function ($val, $key) use ($newProductPermissions) {
                if ($val->products()->count() > 0) {
                    $activeProductIds = $val->products()->orderBy('created_at', 'desc')->take((int)$newProductPermissions->value)->pluck('id')->toArray();
                    $val->products()->whereNotIn('id', $activeProductIds)->whereStatus('active')->update(['status' => 'inactive']);
                }
            });
        }
        //Changing delivery type to no delivery on downgrading subscription
        if (!$newDeliveryPermissions->status) {
            $activebusinesses->each(function ($val, $key) use ($newProductPermissions) {
                $deliveryType = DeliveryType::coerce(str_replace(' ', '', ucwords($val->deliveryZone->delivery_type)))->value;
                if ($deliveryType) {
                    $val->deliveryZone()->update(['delivery_type' => 0]);
                }
            });
        }
    }
}
