<?php

namespace Database\Seeders;

use App\Models\User;
use Stripe\StripeClient;
use App\Models\CreditCard;
use App\Traits\StripePayment;
use Illuminate\Database\Seeder;

class RemoveStripeAccountSeeder extends Seeder
{
    use StripePayment;
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
        $businessOwners = User::where('user_type', 'business_owner')->whereNotNull('stripe_connect_id')->get();
        foreach ($businessOwners as $owner) {
            $owner->stripe_customer_id = null;
            $owner->stripe_connect_id = null;
            $owner->stripe_bank_id = null;
            $owner->completed_stripe_onboarding = 0;
            $owner->saveQuietly();

            // creating customer stripe id
            $stripeCustomerId = $this->getStripeCustomerId($owner);
            $owner->stripe_customer_id = $stripeCustomerId;
            $owner->saveQuietly();

            // creating payment method of a business owner
            $paymentMethod = $this->makePaymentMethod();
            $this->stripeClient->paymentMethods->attach(
                $paymentMethod->id,
                ['customer' => $owner->stripe_customer_id]
            );

            // creating dummy credit card information for business owner
            CreditCard::updateOrCreate(['user_id' => $owner->id], [
                'user_name' => 'Test Card',
                'user_id' => $owner->id,
                'payment_method_id' => $paymentMethod->id,
                'email' => $owner->email,
                'customer_id' => $owner->stripe_customer_id,
                'brand' => $paymentMethod->card->brand,
                'country' => $paymentMethod->card->country,
                'expiry_month' => $paymentMethod->card->exp_month,
                'expiry_year' => $paymentMethod->card->exp_year,
                'last_four' => $paymentMethod->card->last4,
                'default' => 1,
                'save_card' => 1
            ]);
        }
    }
}
