<?php

namespace Modules\Classifieds\Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use App\Models\UserSubscription;
use App\Traits\StripeSubscription;
use Illuminate\Database\Eloquent\Model;
use Modules\Retail\Entities\SubscriptionPermission;

class ClassifiedSubscribeTableSeeder extends Seeder
{
    use StripeSubscription;

    public function __construct(protected StripeClient $stripeClient)
    {
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $customer = User::whereEmail('c.customer01@interapptive.com')->with('cards')->first();
        $customer1 = User::whereEmail('c.customer02@interapptive.com')->with('cards')->first();
        $customer2 = User::whereEmail('c.customer03@interapptive.com')->with('cards')->first();
        $business_owners = [
            $customer->stripe_customer_id,
            $customer1->stripe_customer_id,
            $customer2->stripe_customer_id,
        ];
        $cards = [
            $customer->cards[0]->payment_method_id,
            $customer1->cards[0]->payment_method_id,
            $customer2->cards[0]->payment_method_id,
        ];
        foreach ($business_owners as $key => $cusId) {
            $module = StandardTag::where('slug', Product::MODULE_MARKETPLACE)->firstOrFail();
            $user = User::where('stripe_customer_id', $cusId)->firstOrFail();
            //getting user subscriptions
            $subscriptions = $this->stripeClient->subscriptions->all([
                'limit' => 100,
                'customer' => $cusId
            ]);
            //getting subscription matched with module
            $matchedSubscription = collect($subscriptions->data)->filter(function ($sub, $key) use ($module) {
                return $sub->metadata->module == $module;
            })->first();
            //cancelling user subscriptions
            if ($matchedSubscription) {
                $this->stripeClient->subscriptions->cancel(
                    $matchedSubscription->id,
                    []
                );
                $permission = UserSubscription::where('module_id', $module->id)->where('user_id', $user->id)->where('product_id', $matchedSubscription['plan']['product'])->get();
                if ($permission) {
                    $permission->delete();
                }
            }
            //getting plans
            $products = $this->stripeClient->products->search([
                'limit' => 100,
                'query' => 'metadata[\'module\']:\'' . $module->slug . '\' AND active:"true"',
            ]);
            if (count($products->data) > 0) {
                foreach ($products->autoPagingIterator() as $index => $product) {
                    if ($key == $index) {
                        $subscription = $this->stripeClient->subscriptions->create([
                            'customer' => $cusId,
                            'default_payment_method' => $cards[$key],
                            'collection_method' => 'charge_automatically',
                            'items' => [
                                ['price' => $product->default_price],
                            ],
                            'metadata' => [
                                'module' => $module->slug,
                            ],
                        ]);
                        if ($product->default_price && $module) {
                            $subscription_type = SubscriptionPermission::where('product_id', $product->id)->where('key', 'type')->firstOrFail();
                            if ($cusId) {
                                $customer = $this->stripeClient->customers->retrieve(
                                    $cusId,
                                    []
                                );
                                $customerMeta = $customer->metadata->toArray();
                                $flag = array_key_exists($module->slug, $customerMeta);
                                if ($flag) {
                                    $customerMeta[$module->slug] = $subscription_type->value;
                                } else {
                                    $customerMeta = Arr::add($customerMeta, $module->slug, $subscription_type->value);
                                }
                                $this->stripeClient->customers->update(
                                    $cusId,
                                    ['metadata' => $customerMeta]
                                );
                                //updating user_subscription table
                                $user_subscription = UserSubscription::updateOrCreate(
                                    ['user_id' => $user->id, 'module_id' => $module->id],
                                    ['product_id' => $product->id],
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}
