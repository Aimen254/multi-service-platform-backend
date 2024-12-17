<?php

namespace Modules\Taskers\Database\Seeders;

use App\Models\User;
use Stripe\StripeClient;
use App\Models\CreditCard;
use App\Traits\StripePayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class TaskerCustomerTableSeeder extends Seeder
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
            'first_name' => 'Tasker',
            'last_name' => '01',
            'email' => 'tasker01@interapptive.com',
            'phone' => '+12025589721',
            'password' => Hash::make('12345678'),
            'user_type' => 'customer',
        ];

        $stripeCustomerId = $this->getStripeCustomerId($data);
        $data['stripe_customer_id'] = $stripeCustomerId;

        $tasker01 =  User::updateOrCreate([
            'email' => $data['email']
        ], [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'password' => $data['password'],
            'user_type' => $data['user_type'],
            'phone' => '+12025589721',
            'email_verified_at' => now(),
            'stripe_customer_id' => $data['stripe_customer_id'],
            'stripe_connect_id' => null,
            'completed_stripe_onboarding' => 0,
        ]);

        $paymentMethod = $this->makePaymentMethod();
        $paymentAttach = $this->stripeClient->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $tasker01->stripe_customer_id]
        );
        $cardData = [
            'user_name' => 'Tasker 01',
            'user_id' => $tasker01->id,
            'payment_method_id' => $paymentMethod->id,
            'email' => 'tasker01@interapptive.com',
            'customer_id' => $tasker01->stripe_customer_id,
            'brand' => $paymentMethod->card->brand,
            'country' => $paymentMethod->card->country,
            'expiry_month' => $paymentMethod->card->exp_month,
            'expiry_year' => $paymentMethod->card->exp_year,
            'last_four' => $paymentMethod->card->last4,
            'default' => 1,
            'save_card' => 1
        ];
        CreditCard::updateOrCreate(['user_id' => $tasker01->id],$cardData);


        // Customer 2
        $data = [
            'first_name' => 'Tasker',
            'last_name' => '02',
            'email' => 'tasker02@interapptive.com',
            'phone' => '+12025047192',
            'password' => Hash::make('12345678'),
            'user_type' => 'customer',
        ];

        $stripeCustomerId = $this->getStripeCustomerId($data);
        $data['stripe_customer_id'] = $stripeCustomerId;

        $tasker02 =  User::updateOrCreate([
            'email' => $data['email']
        ], [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'password' => $data['password'],
            'user_type' => $data['user_type'],
            'phone' => '+12025047192',
            'email_verified_at' => now(),
            'stripe_customer_id' => $data['stripe_customer_id'],
            'stripe_connect_id' => null,
            'completed_stripe_onboarding' => 0,
        ]);

        $paymentMethod = $this->makePaymentMethod();
        $paymentAttach = $this->stripeClient->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $tasker02->stripe_customer_id]
        );
        $cardData = [
            'user_name' => 'Tasker 02',
            'user_id' => $tasker02->id,
            'payment_method_id' => $paymentMethod->id,
            'email' => 'tasker02@interapptive.com',
            'customer_id' => $tasker02->stripe_customer_id,
            'brand' => $paymentMethod->card->brand,
            'country' => $paymentMethod->card->country,
            'expiry_month' => $paymentMethod->card->exp_month,
            'expiry_year' => $paymentMethod->card->exp_year,
            'last_four' => $paymentMethod->card->last4,
            'default' => 1,
            'save_card' => 1
        ];
        CreditCard::updateOrCreate(['user_id' => $tasker02->id],$cardData);


        // Customer 3
        $data = [
            'first_name' => 'Tasker',
            'last_name' => '03',
            'email' => 'tasker03@interapptive.com',
            'phone' => '+12025098765',
            'password' => Hash::make('12345678'),
            'user_type' => 'customer',
        ];

        $stripeCustomerId = $this->getStripeCustomerId($data);
        $data['stripe_customer_id'] = $stripeCustomerId;

        $tasker03 =  User::updateOrCreate([
            'email' => $data['email']
        ], [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'password' => $data['password'],
            'user_type' => $data['user_type'],
            'phone' => '+12025098765',
            'email_verified_at' => now(),
            'stripe_customer_id' => $data['stripe_customer_id'],
            'stripe_connect_id' => null,
            'completed_stripe_onboarding' => 0,
        ]);

        $paymentMethod = $this->makePaymentMethod();
        $paymentAttach = $this->stripeClient->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $tasker03->stripe_customer_id]
        );
        $cardData = [
            'user_name' => 'Tasker 03',
            'user_id' => $tasker03->id,
            'payment_method_id' => $paymentMethod->id,
            'email' => 'tasker03@interapptive.com',
            'customer_id' => $tasker03->stripe_customer_id,
            'brand' => $paymentMethod->card->brand,
            'country' => $paymentMethod->card->country,
            'expiry_month' => $paymentMethod->card->exp_month,
            'expiry_year' => $paymentMethod->card->exp_year,
            'last_four' => $paymentMethod->card->last4,
            'default' => 1,
            'save_card' => 1
        ];
        CreditCard::updateOrCreate(['user_id' => $tasker03->id],$cardData);
    }
}
