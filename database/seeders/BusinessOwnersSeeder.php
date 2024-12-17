<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Stripe\StripeClient;
use App\Models\CreditCard;
use App\Traits\StripePayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BusinessOwnersSeeder extends Seeder
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
        $businessOwners = array (
            [
                'first_name' => 'Business',
                'last_name' => 'Owner',
                'email' => 'businessOwner@interapptive.com',
                'password' => Hash::make('12345678'),
                'user_type' => 'business_owner',
            ],
            [
                'first_name' => 'Business',
                'last_name' => 'Owner',
                'email' => 'businessOwner1@interapptive.com',
                'password' => Hash::make('12345678'),
                'user_type' => 'business_owner',
            ],
            [
                'first_name' => 'Business',
                'last_name' => 'Owner',
                'email' => 'businessOwner2@interapptive.com',
                'password' => Hash::make('12345678'),
                'user_type' => 'business_owner',
            ],
        );

        foreach ($businessOwners as $owner) {
            $businessOwner = User::updateOrCreate(['email' => $owner['email']], [
                'first_name' => $owner['first_name'],
                'last_name' => $owner['last_name'],
                'password' => $owner['password'],
                'user_type' => $owner['user_type'],
                'email_verified_at' => Carbon::now(),
                'completed_stripe_onboarding' => 0,
            ]);

            // if the business owner is new
            if ($businessOwner->wasRecentlyCreated) {
                // creating customer stripe id
                $stripeCustomerId = $this->getStripeCustomerId($owner);
                $businessOwner->stripe_customer_id = $stripeCustomerId;
                $businessOwner->saveQuietly();

                // creating payment method of a business owner
                $paymentMethod = $this->makePaymentMethod();
                $this->stripeClient->paymentMethods->attach(
                    $paymentMethod->id,
                    ['customer' => $businessOwner->stripe_customer_id]
                );

                // creating dummy credit card information for business owner
                CreditCard::updateOrCreate(['user_id' => $businessOwner->id], [
                    'user_name' => 'Test Card',
                    'user_id' => $businessOwner->id,
                    'payment_method_id' => $paymentMethod->id,
                    'email' => $businessOwner->email,
                    'customer_id' => $businessOwner->stripe_customer_id,
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
}
