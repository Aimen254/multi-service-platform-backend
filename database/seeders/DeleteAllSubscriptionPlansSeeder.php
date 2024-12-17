<?php

namespace Database\Seeders;

use Stripe\StripeClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Retail\Entities\SubscriptionPermission;

class DeleteAllSubscriptionPlansSeeder extends Seeder
{
    protected StripeClient $stripeClient;

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
        //Delete all permissions from table
        SubscriptionPermission::truncate();
        //code to delete all products/plans from stripe
        $products = $this->stripeClient->products->all([
            'limit' => 100,
            'active' => true,
        ]);
        foreach ($products->autoPagingIterator() as $product) {
            $this->stripeClient->products->update(
                $product->id,
                ['active' => false]
            );
            $subscriptions = $this->stripeClient->subscriptions->all(['price' => $product->default_price, 'limit' => 100]);
            if ($subscriptions->count() > 0) {
                foreach ($subscriptions->autoPagingIterator() as $subscription) {
                    // Do something with each subscription
                    $this->stripeClient->subscriptions->cancel(
                        $subscription->id,
                        []
                    );
                }
            }
        }
    }
}
