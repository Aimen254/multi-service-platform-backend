<?php

namespace Modules\News\Database\Seeders;

use App\Models\User;
use Stripe\StripeClient;
use App\Models\CreditCard;
use App\Traits\StripePayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class NewsReporterSeederTableSeeder extends Seeder
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

        // Reporter 1
        $data = [
            'first_name' => 'Clark',
            'last_name' => 'Davis',
            'email' => 'reporter01@interapptive.com',
            'password' => Hash::make('12345678'),
            'user_type' => 'reporter',
        ];

        $stripeCustomerId = $this->getStripeCustomerId($data);
        $data['stripe_customer_id'] = $stripeCustomerId;

        $reporter01 =  User::updateOrCreate([
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
        ]);

        $paymentMethod = $this->makePaymentMethod();
        $paymentAttach = $this->stripeClient->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $reporter01->stripe_customer_id]
        );
        $cardData = [
            'user_name' => 'Reporter Davis',
            'user_id' => $reporter01->id,
            'payment_method_id' => $paymentMethod->id,
            'email' => 'reporter01@interapptive.com',
            'customer_id' => $reporter01->stripe_customer_id,
            'brand' => $paymentMethod->card->brand,
            'country' => $paymentMethod->card->country,
            'expiry_month' => $paymentMethod->card->exp_month,
            'expiry_year' => $paymentMethod->card->exp_year,
            'last_four' => $paymentMethod->card->last4,
            'default' => 1,
            'save_card' => 1
        ];
        CreditCard::updateOrCreate(['user_id' => $reporter01->id],$cardData);


        // Reporter 2
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'reporter02@interapptive.com',
            'password' => Hash::make('12345678'),
            'user_type' => 'reporter',
        ];

        $stripeCustomerId = $this->getStripeCustomerId($data);
        $data['stripe_customer_id'] = $stripeCustomerId;

        $reporter02 =  User::updateOrCreate([
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
        ]);

        $paymentMethod = $this->makePaymentMethod();
        $paymentAttach = $this->stripeClient->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $reporter02->stripe_customer_id]
        );
        $cardData = [
            'user_name' => 'Reporter Davis',
            'user_id' => $reporter02->id,
            'payment_method_id' => $paymentMethod->id,
            'email' => 'reporter02@interapptive.com',
            'customer_id' => $reporter02->stripe_customer_id,
            'brand' => $paymentMethod->card->brand,
            'country' => $paymentMethod->card->country,
            'expiry_month' => $paymentMethod->card->exp_month,
            'expiry_year' => $paymentMethod->card->exp_year,
            'last_four' => $paymentMethod->card->last4,
            'default' => 1,
            'save_card' => 1
        ];
        CreditCard::updateOrCreate(['user_id' => $reporter02->id],$cardData);
    }
}
