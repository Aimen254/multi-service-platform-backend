<?php

namespace Modules\RealEstate\Database\Seeders;

use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Modules\Retail\Entities\SubscriptionPermission;

class RealEstateSubscriptionTableSeeder extends Seeder
{
    protected StripeClient $stripeClient;
    private $product = null;
    private $stripeProducts = null;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        try {
            $subscriptionPlans = [
                [
                    "id" => null,
                    "priceId" => null,
                    "name" => "Broker + Newspaper",
                    "description" => "Lorum ipsum",
                    "price" => "129",
                    "interval" => "month",
                    "permissions" => [
                        "total_businesses" => [
                            "status" => true,
                            "key" => "total_businesses",
                            "value" => "1",
                        ],
                        "featured_businesses" => [
                            "status" => true,
                            "key" => "featured_businesses",
                            "value" => "1",
                        ],
                        "featured_products" => [
                            "status" => false,
                            "key" => "featured_products",
                            "value" => null,
                        ],
                        "total_products" => [
                            "status" => true,
                            "key" => "total_products",
                            "value" => "-1",
                        ],
                        "type" => [
                            "status" => false,
                            "key" => "type",
                            "value" => null,
                        ],
                    ],
                ],
                [
                    "id" => null,
                    "priceId" => null,
                    "name" => "L3 Feature + Newspaper",
                    "description" => "Lorum ipsum",
                    "price" => "499",
                    "interval" => "month",
                    "permissions" => [
                        "total_businesses" => [
                            "status" => true,
                            "key" => "total_businesses",
                            "value" => "3",
                        ],
                        "featured_businesses" => [
                            "status" => true,
                            "key" => "featured_businesses",
                            "value" => "3",
                        ],
                        "featured_products" => [
                            "status" => true,
                            "key" => "featured_products",
                            "value" => null,
                        ],
                        "total_products" => [
                            "status" => true,
                            "key" => "total_products",
                            "value" => "-1",
                        ],
                        "type" => [
                            "status" => true,
                            "key" => "type",
                            "value" => 'L3',
                        ],
                    ],
                ],
                [
                    "id" => null,
                    "priceId" => null,
                    "name" => "L2 Feature + Newspaper",
                    "description" => "Lorum ipsum",
                    "price" => "699",
                    "interval" => "month",
                    "permissions" => [
                        "total_businesses" => [
                            "status" => true,
                            "key" => "total_businesses",
                            "value" => "4",
                        ],
                        "featured_businesses" => [
                            "status" => true,
                            "key" => "featured_businesses",
                            "value" => "4",
                        ],
                        "featured_products" => [
                            "status" => true,
                            "key" => "featured_products",
                            "value" => null,
                        ],
                        "total_products" => [
                            "status" => true,
                            "key" => "total_products",
                            "value" => "-1",
                        ],
                        "type" => [
                            "status" => true,
                            "key" => "type",
                            "value" => 'L2',
                        ],
                    ],
                ],
                [
                    "id" => null,
                    "priceId" => null,
                    "name" => "L1 Feature + Newspaper",
                    "description" => "Lorum ipsum",
                    "price" => "999",
                    "interval" => "month",
                    "permissions" => [
                        "total_businesses" => [
                            "status" => true,
                            "key" => "total_businesses",
                            "value" => "5",
                        ],
                        "featured_businesses" => [
                            "status" => true,
                            "key" => "featured_businesses",
                            "value" => "5",
                        ],
                        "featured_products" => [
                            "status" => true,
                            "key" => "featured_products",
                            "value" => null,
                        ],
                        "total_products" => [
                            "status" => true,
                            "key" => "total_products",
                            "value" => "-1",
                        ],
                        "type" => [
                            "status" => true,
                            "key" => "type",
                            "value" => 'L1',
                        ],
                    ],
                ],
            ];
            $module = StandardTag::where('slug', 'real-estate')->firstOrFail();
            $products = $this->stripeClient->products->search([
                'limit' => 100,
                'query' => 'metadata[\'module\']:\'' . $module->slug . '\' AND active:"true"',
            ]);
            $this->stripeProducts = $products->data;
            foreach ($subscriptionPlans as $index => $plan) {
                if (count($this->stripeProducts) != count($subscriptionPlans)) {
                    if (count($this->stripeProducts) > 0) {
                        foreach ($this->stripeProducts as $key => $product) {
                            $planMatched = $this->matchPlan($plan, $this->stripeProducts);
                            if (!$planMatched) {
                                $product = $this->createPlanAndPermissions($plan, $module);
                                $this->product = $product;
                                $this->stripeProducts = Arr::prepend($this->stripeProducts, $this->product);
                                break;
                            }
                        }
                    } else {
                        $product = $this->createPlanAndPermissions($plan, $module);
                    }
                } else {
                    foreach ($this->stripeProducts as $key => $product) {
                        $exists = SubscriptionPermission::where('product_id', $product->id)->exists();
                        if (!$exists) {
                            $matchedPlan = $this->matchProduct($product, $plan);
                            if ($matchedPlan) {
                                foreach ($matchedPlan['permissions'] as $key => $value) {
                                    SubscriptionPermission::create([
                                        'product_id' => $product->id,
                                        'key' => $value['key'],
                                        'value' => $value['value'],
                                        'status' => $value['status'],
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Stripe\Exception\CardException $e) {
            Log::error([
                'message' => $e->getMessage(),
            ]);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error([
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function matchPlan($plan, $product)
    {
        try {
            $flag = false;
            foreach ($this->stripeProducts as $key => $product) {
                $price = $this->stripeClient->prices->retrieve(
                    $product->default_price,
                    []
                );
                $product->price = $price;
                if (($plan['name'] == $product->name) && ($plan['price'] * 100 == $product->price->unit_amount) && ($plan['interval'] == $product->price->recurring->interval)) {
                    $flag = true;
                    break;
                } else {
                    $flag = false;
                }
            }
            return $flag;
        } catch (\Throwable $th) {
            Log::error([
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function matchProduct($product, $plan)
    {
        try {
            $price = $this->stripeClient->prices->retrieve(
                $product->default_price,
                []
            );
            $product->price = $price;
            if (($plan['name'] == $product->name) && ($plan['price'] * 100 == $product->price->unit_amount) && ($plan['interval'] == $product->price->recurring->interval)) {
                return $plan;
            }
            return null;
        } catch (\Throwable $th) {
            Log::error([
                'message' => $th->getMessage(),
                'test' => 'hello',
            ]);
        }
    }

    public function createPlanAndPermissions($plan, $module)
    {
        $product = $this->stripeClient->products->create([
            'name' => $plan['name'],
            'description' => $plan['description'],
            'default_price_data' => [
                'unit_amount' => $plan['price'] * 100,
                'currency' => 'usd',
                'recurring' => ['interval' => $plan['interval']],
            ],
            'metadata' => [
                'module' => $module->slug,
            ],
        ]);
        foreach ($plan['permissions'] as $key => $value) {
            SubscriptionPermission::create([
                'product_id' => $product->id,
                'key' => $value['key'],
                'value' => $value['value'],
                'status' => $value['status'],
            ]);
        }
        return $product;
    }
}
