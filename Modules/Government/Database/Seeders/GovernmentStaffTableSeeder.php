<?php

namespace Modules\Government\Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\CreditCard;
use Illuminate\Support\Str;
use App\Traits\StripePayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class GovernmentStaffTableSeeder extends Seeder
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
        Model::unguard();
        $department = Business::whereSlug(Str::slug('education-department'))->first();
        $data = [
            [
                'first_name' => 'Government',
                'last_name' => 'Staff 1',
                'email' => 'governmentStaff1@interapptive.com',
                'user_type' => 'government_staff',
                'password' => Hash::make('12345678'),
            ],
            [
                'first_name' => 'Government',
                'last_name' => 'Staff 2',
                'email' => 'governmentStaff2@interapptive.com',
                'user_type' => 'government_staff',
                'password' => Hash::make('12345678'),
            ],
            [
                'first_name' => 'Government',
                'last_name' => 'Staff 3',
                'email' => 'governmentStaff3@interapptive.com',
                'user_type' => 'government_staff',
                'password' => Hash::make('12345678'),
            ]
        ];

        foreach ($data as $value) {
            $employee = User::updateOrcreate(
                [
                    'email' => $value['email'],
                ],
                [
                    'first_name' => $value['first_name'],
                    'last_name' => $value['last_name'],
                    'password' => $value['password'],
                    'user_type' => $value['user_type'],
                    "business_id" => $department->id,
                    'email_verified_at' => Carbon::now(),
                    'stripe_customer_id' => $this->getStripeCustomerId(['first_name' => $value['first_name'], 'email' => $value['email']]),
                    'stripe_connect_id' => null,
                    'completed_stripe_onboarding' => 0,
                ]
            );

            $paymentMethod = $this->makePaymentMethod();
            $paymentAttach = $this->stripeClient->paymentMethods->attach(
                $paymentMethod->id,
                ['customer' => $employee->stripe_customer_id]
            );

            $cardData = [
                'user_name' => 'Test Card',
                'user_id' => $employee->id,
                'email' => $value['email'],
                'payment_method_id' => $paymentMethod->id,
                'customer_id' => $employee->stripe_customer_id,
                'brand' => $paymentMethod->card->brand,
                'country' => $paymentMethod->card->country,
                'expiry_month' => $paymentMethod->card->exp_month,
                'expiry_year' => $paymentMethod->card->exp_year,
                'last_four' => $paymentMethod->card->last4,
                'default' => 1,
                'save_card' => 1
            ];
            CreditCard::updateOrCreate(['user_id' => $employee->id],$cardData);
        }
    }
}
