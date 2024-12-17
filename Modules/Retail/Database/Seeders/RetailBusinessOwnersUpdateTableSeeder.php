<?php

namespace Modules\Retail\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RetailBusinessOwnersUpdateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $businesses = Business::whereHas('standardTags', function($query) {
            $query->where('slug', 'retail');
        })->get();

        $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
        $businessOwner1 = User::whereEmail('businessOwner1@interapptive.com')->first();
        $businessOwner2 = User::whereEmail('businessOwner2@interapptive.com')->first();

        $businessOwnerIds = [$businessOwner->id, $businessOwner1->id, $businessOwner2->id];

        foreach ($businesses as $business) {
            // Randomly select a business owner ID from the array
            $randomOwnerId = $businessOwnerIds[array_rand($businessOwnerIds)];
        
            // Update the business owner ID
            $business->owner_id = $randomOwnerId;
            $business->save();
        }
    }
}
