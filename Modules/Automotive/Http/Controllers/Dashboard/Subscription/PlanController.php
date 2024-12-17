<?php

namespace Modules\Automotive\Http\Controllers\Dashboard\Subscription;

use stdClass;
use Inertia\Inertia;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Redirect;
use Modules\Retail\Entities\SubscriptionPermission;
use Modules\Retail\Http\Requests\SubscriptionPlanRequest;

class PlanController extends Controller
{
    protected StripeClient $stripeClient;
    private $product = [];

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId)
    {
        $module = StandardTag::findOrFail($moduleId);
        $products = $this->stripeClient->products->search([
            'limit' => 100,
            'query' => 'metadata[\'module\']:\'' . $module->slug . '\'',
        ]);
        collect($products->data)->each(function ($item, $key) {
            if ($item->active) {
                $singleProduct = new stdClass();
                $price = $this->stripeClient->prices->retrieve(
                    $item->default_price,
                    []
                );
                $singleProduct->product = $item;
                $singleProduct->price = $price;
                array_push($this->product, $singleProduct);
            }
        });
        return Inertia::render('Automotive::Subscription/Plans/Index', [
            'subscriptions' => $this->product,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return Inertia::render('Automotive::Subscription/Plans/Edit', []);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(SubscriptionPlanRequest $request, $moduleId)
    {
        try {
            $module = StandardTag::findOrFail($moduleId);
            $productData = [
                'name' => $request->input('name'),
                'default_price_data' => [
                    'unit_amount' => $request->input('price') * 100,
                    'currency' => 'usd',
                    'recurring' => ['interval' => $request->input('interval')],
                ],
                'metadata' => [
                    'module' => $module->slug,
                ]
            ];

            if ($request->input('description')) {
                $productData['description'] = $request->input('description');
            }

            $product = $this->stripeClient->products->create($productData);
            foreach ($request->input('permissions') as $key => $value) {
                SubscriptionPermission::create([
                    'product_id' => $product->id,
                    'key' => $value['key'],
                    'value' => $value['value'],
                    'status' => $value['status'],
                ]);
            }
            if ($product) {
                $products = $this->stripeClient->products->search([
                    'limit' => 100,
                    'query' => 'metadata[\'module\']:\'' . $module->slug . '\'',
                ]);
                $products->data = Arr::prepend($products->data, $product);
                collect($products->data)->each(function ($item, $key) {
                    if ($item->active) {
                        $singleProduct = new stdClass();
                        $price = $this->stripeClient->prices->retrieve(
                            $item->default_price,
                            []
                        );
                        $singleProduct->product = $item;
                        $singleProduct->price = $price;
                        array_push($this->product, $singleProduct);
                    }
                });
                flash('Subscription added successfully', 'success');
                return Inertia::render('Automotive::Subscription/Plans/Index', [
                    'subscriptions' => $this->product,
                ]);
            } else {
                flash('Subscription can not be added due to some error.', 'danger');
                return \redirect()->back();
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
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('automotive::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($moduleId, $id)
    {
        $product = $this->stripeClient->products->retrieve(
            $id,
            []
        );
        $price = $this->stripeClient->prices->retrieve(
            $product->default_price,
            []
        );
        $permissions = SubscriptionPermission::where('product_id', $product->id)->get();
        $singleProduct = new stdClass();
        $singleProduct->product = $product;
        $singleProduct->price = $price;
        $singleProduct->permissions = $permissions;
        return Inertia::render('Automotive::Subscription/Plans/Edit', [
            'plan' => $singleProduct,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(subscriptionPlanRequest $request, $moduleId, $id)
    {
        try {
            $permissions = $request->input('permissions');
            $subPermissions = SubscriptionPermission::where('product_id', $id)->get();
            foreach ($permissions as $key => $permission) {
                foreach ($subPermissions as $key => $value) {
                    if ($value->key == $permission['key']) {
                        $value->value =  $permission['value'];
                        $value->status =  $permission['status'];
                        $value->save();
                    }
                }
            }
            $product = $this->stripeClient->products->retrieve($id);
            $subscription = $this->stripeClient->subscriptions->all([
                'limit' => 100,
                'price' => $product->default_price,
            ]);
            $price = $this->stripeClient->prices->create(
                [
                    'unit_amount' => $request->input('price') * 100,
                    'currency' => 'usd',
                    'recurring' => ['interval' => $request->input('interval')],
                    'product' => $id,
                ]
            );
            $updatedProduct = $this->stripeClient->products->update(
                $id,
                [
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'default_price' => $price->id,
                ]
            );
            if (count($subscription->data) > 0) {
                collect($subscription->data)->each(function ($item, $key) use ($price) {

                    $subscriptionUpdate = $this->stripeClient->subscriptions->update(
                        $item->id,
                        [
                            'items' => [
                                [
                                    'id' => $item->items->data[0]->id,
                                    'price' =>  $price->id,
                                ],
                            ],
                        ],
                    );
                });
            }
            $oldPrice = $this->stripeClient->prices->update(
                $request->input('priceId'),
                [
                    'active' => false,
                ]
            );
            flash('Subscription Updated successfully', 'success');
            return \redirect()->route('automotive.dashboard.subscription.plan.index', $moduleId);
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
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $id,Request $request)
    {
        try {
            $this->stripeClient->products->update(
                $id,
                ['active' => false]
            );
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            SubscriptionPermission::where('product_id', $id)->delete();
            flash('Subscription deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('automotive.dashboard.subscription.plan.index', [$moduleId, 'page' => $previousPage]);
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
}
