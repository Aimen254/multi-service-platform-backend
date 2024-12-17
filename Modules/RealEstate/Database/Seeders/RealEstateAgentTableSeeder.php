<?php

namespace Modules\RealEstate\Database\Seeders;

use App\Models\Business;
use App\Models\User;
use Stripe\StripeClient;
use App\Models\CreditCard;
use App\Traits\StripePayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class RealEstateAgentTableSeeder extends Seeder
{
    use StripePayment;

    public function __construct(private StripeClient $stripeClient)
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

        // Agent 1
        $data = [
            'first_name' => 'Lisa',
            'last_name' => 'Busceme',
            'email' => 'agent01@interapptive.com',
            'password' => Hash::make('12345678'),
            'user_type' => 'agent',
        ];

        $businessDreamEstate = Business::where('slug', 'dream-estates')->first();
        $stripeCustomerId = $this->getStripeCustomerId($data);
        $data['stripe_customer_id'] = $stripeCustomerId;

        $agent01 =  User::updateOrCreate([
            'email' => $data['email']
        ], [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'password' => $data['password'],
            'user_type' => $data['user_type'],
            'email_verified_at' => now(),
            'stripe_customer_id' => $data['stripe_customer_id'],
            'stripe_connect_id' => null,
            'completed_stripe_onboarding' => 0,
            'business_id' => $businessDreamEstate->id
        ]);

        $paymentMethod = $this->makePaymentMethod();
        $this->stripeClient->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $agent01->stripe_customer_id]
        );
        $cardData = [
            'user_name' => 'Agent Lisa',
            'user_id' => $agent01->id,
            'payment_method_id' => $paymentMethod->id,
            'email' => 'agent01@interapptive.com',
            'customer_id' => $agent01->stripe_customer_id,
            'brand' => $paymentMethod->card->brand,
            'country' => $paymentMethod->card->country,
            'expiry_month' => $paymentMethod->card->exp_month,
            'expiry_year' => $paymentMethod->card->exp_year,
            'last_four' => $paymentMethod->card->last4,
            'default' => 1,
            'save_card' => 1
        ];
        CreditCard::updateOrCreate(['user_id' => $agent01->id], $cardData);


        // Agent 2
        $data = [
            'first_name' => 'Paul',
            'last_name' => 'Burns',
            'email' => 'agent02@interapptive.com',
            'password' => Hash::make('12345678'),
            'user_type' => 'agent',
        ];

        $stripeCustomerId = $this->getStripeCustomerId($data);
        $data['stripe_customer_id'] = $stripeCustomerId;

        $agent02 =  User::updateOrCreate([
            'email' => $data['email']
        ], [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'password' => $data['password'],
            'user_type' => $data['user_type'],
            'email_verified_at' => now(),
            'stripe_customer_id' => $data['stripe_customer_id'],
            'stripe_connect_id' => null,
            'completed_stripe_onboarding' => 0,
            'business_id' => $businessDreamEstate->id
        ]);

        $paymentMethod = $this->makePaymentMethod();
        $this->stripeClient->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $agent02->stripe_customer_id]
        );
        $cardData = [
            'user_name' => 'Agent Paul',
            'user_id' => $agent02->id,
            'payment_method_id' => $paymentMethod->id,
            'email' => 'agent02@interapptive.com',
            'customer_id' => $agent02->stripe_customer_id,
            'brand' => $paymentMethod->card->brand,
            'country' => $paymentMethod->card->country,
            'expiry_month' => $paymentMethod->card->exp_month,
            'expiry_year' => $paymentMethod->card->exp_year,
            'last_four' => $paymentMethod->card->last4,
            'default' => 1,
            'save_card' => 1
        ];
        CreditCard::updateOrCreate(['user_id' => $agent02->id], $cardData);
    }
}
