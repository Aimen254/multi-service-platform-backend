<?php

namespace Modules\Classifieds\Database\Seeders;

use App\Models\User;
use Stripe\StripeClient;
use App\Models\CreditCard;
use App\Traits\StripePayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class ClassifiedCustomerTableSeeder extends Seeder
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

        // Customer 1
        $data = [
            'first_name' => 'Jones',
            'last_name' => 'Smith',
            'email' => 'c.customer01@interapptive.com',
            'phone' => '+12025550141',
            'password' => Hash::make('12345678'),
            'user_type' => 'customer',
        ];

        $stripeCustomerId = $this->getStripeCustomerId($data);
        $data['stripe_customer_id'] = $stripeCustomerId;

        $customer01 =  User::updateOrCreate([
            'email' => $data['email']
        ], [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'password' => $data['password'],
            'user_type' => $data['user_type'],
            'phone' => '+12025550141',
            'email_verified_at' => now(),
            'stripe_customer_id' => $data['stripe_customer_id'],
            'stripe_connect_id' => null,
            'completed_stripe_onboarding' => 0,
        ]);

        $paymentMethod = $this->makePaymentMethod();
        $paymentAttach = $this->stripeClient->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $customer01->stripe_customer_id]
        );
        $cardData = [
            'user_name' => 'Customer Jones',
            'user_id' => $customer01->id,
            'payment_method_id' => $paymentMethod->id,
            'email' => 'c.customer01@interapptive.com',
            'customer_id' => $customer01->stripe_customer_id,
            'brand' => $paymentMethod->card->brand,
            'country' => $paymentMethod->card->country,
            'expiry_month' => $paymentMethod->card->exp_month,
            'expiry_year' => $paymentMethod->card->exp_year,
            'last_four' => $paymentMethod->card->last4,
            'default' => 1,
            'save_card' => 1
        ];
        CreditCard::updateOrCreate(['user_id' => $customer01->id],$cardData);


        // Customer 2
        $data = [
            'first_name' => 'Dopey',
            'last_name' => 'Trott',
            'email' => 'c.customer02@interapptive.com',
            'phone' => '+12025550982',
            'password' => Hash::make('12345678'),
            'user_type' => 'customer',
        ];

        $stripeCustomerId = $this->getStripeCustomerId($data);
        $data['stripe_customer_id'] = $stripeCustomerId;

        $customer02 =  User::updateOrCreate([
            'email' => $data['email']
        ], [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'password' => $data['password'],
            'user_type' => $data['user_type'],
            'phone' => '+12025550982',
            'email_verified_at' => now(),
            'stripe_customer_id' => $data['stripe_customer_id'],
            'stripe_connect_id' => null,
            'completed_stripe_onboarding' => 0,
        ]);

        $paymentMethod = $this->makePaymentMethod();
        $paymentAttach = $this->stripeClient->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $customer02->stripe_customer_id]
        );
        $cardData = [
            'user_name' => 'Customer Dopey',
            'user_id' => $customer02->id,
            'payment_method_id' => $paymentMethod->id,
            'email' => 'c.customer02@interapptive.com',
            'customer_id' => $customer02->stripe_customer_id,
            'brand' => $paymentMethod->card->brand,
            'country' => $paymentMethod->card->country,
            'expiry_month' => $paymentMethod->card->exp_month,
            'expiry_year' => $paymentMethod->card->exp_year,
            'last_four' => $paymentMethod->card->last4,
            'default' => 1,
            'save_card' => 1
        ];
        CreditCard::updateOrCreate(['user_id' => $customer02->id],$cardData);


        // Customer 3
        $data = [
            'first_name' => 'Evans',
            'last_name' => 'Frank',
            'email' => 'c.customer03@interapptive.com',
            'phone' => '+12025556673',
            'password' => Hash::make('12345678'),
            'user_type' => 'customer',
        ];

        $stripeCustomerId = $this->getStripeCustomerId($data);
        $data['stripe_customer_id'] = $stripeCustomerId;

        $customer03 =  User::updateOrCreate([
            'email' => $data['email']
        ], [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'password' => $data['password'],
            'user_type' => $data['user_type'],
            'phone' => '+12025556673',
            'email_verified_at' => now(),
            'stripe_customer_id' => $data['stripe_customer_id'],
            'stripe_connect_id' => null,
            'completed_stripe_onboarding' => 0,
        ]);

        $paymentMethod = $this->makePaymentMethod();
        $paymentAttach = $this->stripeClient->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $customer03->stripe_customer_id]
        );
        $cardData = [
            'user_name' => 'Customer Frank',
            'user_id' => $customer03->id,
            'payment_method_id' => $paymentMethod->id,
            'email' => 'c.customer03@interapptive.com',
            'customer_id' => $customer03->stripe_customer_id,
            'brand' => $paymentMethod->card->brand,
            'country' => $paymentMethod->card->country,
            'expiry_month' => $paymentMethod->card->exp_month,
            'expiry_year' => $paymentMethod->card->exp_year,
            'last_four' => $paymentMethod->card->last4,
            'default' => 1,
            'save_card' => 1
        ];
        CreditCard::updateOrCreate(['user_id' => $customer03->id],$cardData);
    }
}
