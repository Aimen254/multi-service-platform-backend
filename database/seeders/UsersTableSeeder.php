<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Stripe\StripeClient;
use App\Models\CreditCard;
use App\Traits\StripePayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
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
        // admin account
        User::updateOrcreate(
            [
                'email' => 'admin@hoopla.com'
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'Hoopla',
                'password' => Hash::make('12345678'),
                'user_type' => 'admin',
                'email_verified_at' => Carbon::now()
            ]
        );

        // newsPaper Owner
        User::updateOrCreate(
            [
                'email' => 'newspaper@hoopla.com'
            ],
            [
                'first_name' => 'Newspaper',
                'last_name' => 'Hoopla',
                'password' => Hash::make('12345678'),
                'user_type' => 'newspaper',
                'email_verified_at' => Carbon::now()
            ]
        );

        // business owner

        // calling customer seeder
        // if server not production
        if (env('APP_ENV') != 'production') {
            $this->customerSeeder();
        }
    }

    function customerSeeder() {
        for ($i=1; $i < 10 ; $i++) {
            User::updateOrCreate(
                [
                    'email' => 'customer' . $i . '@interapptive.com'
                ],
                [
                    'first_name' => 'Customer',
                    'last_name' => $i,
                    'password' => Hash::make('12345678'),
                    'user_type' => 'customer',
                    'email_verified_at' => Carbon::now()
                ]
            );
        }
    }
}
