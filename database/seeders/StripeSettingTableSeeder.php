<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Modules\Retail\Entities\SubscriptionPermission;

class StripeSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('user_type', 'business_owner')->get();
        foreach ($users as $user) {
            $user->update([
                'stripe_customer_id' => null,
                'stripe_connect_id' => null,
                'stripe_bank_id' => null,
                'completed_stripe_onboarding' => 0
            ]);
            $user->cards()->delete();
        }
        Artisan::call('db:seed', ['--class' => 'DeleteAllSubscriptionPlansSeeder']);
        SubscriptionPermission::truncate();
    }
}
