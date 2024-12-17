<?php

namespace Modules\Blogs\Database\Seeders;

use App\Models\User;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use App\Models\UserSubscription;
use App\Traits\StripeSubscription;
use Illuminate\Database\Eloquent\Model;
use Modules\Retail\Entities\SubscriptionPermission;

class BlogSubscribeTableSeeder extends Seeder
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
        $businessOwner = User::whereEmail('businessOwner@interapptive.com')->with('cards')->first();
        $businessOwner1 = User::whereEmail('businessOwner1@interapptive.com')->with('cards')->first();
        $businessOwner2 = User::whereEmail('businessOwner2@interapptive.com')->with('cards')->first();
        $business_owners = [
            $businessOwner->stripe_customer_id,
            $businessOwner1->stripe_customer_id,
            $businessOwner2->stripe_customer_id,
        ];
        $cards = [
            $businessOwner->cards[0]->payment_method_id,
            $businessOwner1->cards[0]->payment_method_id,
            $businessOwner2->cards[0]->payment_method_id,
        ];
        foreach ($business_owners as $key => $cusId) {
            $module = StandardTag::where('slug', 'blogs')->firstOrFail();
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
