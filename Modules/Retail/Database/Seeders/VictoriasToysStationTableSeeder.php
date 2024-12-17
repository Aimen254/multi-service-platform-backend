<?php

namespace Modules\Retail\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Traits\BusinessStreetAddress;
use Illuminate\Database\Eloquent\Model;

class VictoriasToysStationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug("Victoria's Toys Station"))->exists()) {
            $businessOwner = User::whereEmail('businessOwner2@interapptive.com')->first();
            $victoriasToysStationStore = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "Victoria's Toys Station",
                "slug" => Str::slug("Victoria's Toys Station"),
                "email" => "vts1984@cox.net",
                "phone" => "+1 225-924-3632",
                "mobile" => "",
                "message" => "Every Child Dreams of a House Filled with Toys!",
                "short_description" => "Victoria's Toy Station was started in 1984 in a train station called Catfish Town in Downtown Baton Rouge. We relocated to our current location in 1987!",
                "long_description" => "Victoria's Toy Station was started in 1984 in a train station called Catfish Town in Downtown Baton Rouge. We relocated to our current location in 1987!
                Play and Toys are so very important in a child's development. It takes them away and makes them wonder, dream and think! Play will help develop their Personalities, special gifts, Talents, and of course Intelligence!",
                "shipping_and_return_policy" => "Merchandise may be returned within 30 days from the date the order was placed for exchange or store credit. All merchandise must be returned in its original box and packaging. Please note: All items that are marked down and/or are included in sales/promotions are FINAL SALE! These items cannot be returned for a store credit or be exchanged.

                Exchanges (if Applicable):
                If you need to exchange, please send us an email at vts1984@cox.net - shipping charges will apply to resend new product.
                
                Defective:
                If you received defective merchandise, please contact our returns department at vts1984@cox.net within two business days so we can correct the issue as soon as possible.
                
                Returns:
                Please return your item(s) via the shipping carrier of your choice. Return shipping is the customer's responsibility. There will be no reimbursement for any shipping costs. We recommend using a carrier that provides tracking information for your record. We are not responsible for any lost or damaged merchandise while in transit. Once your package is received, please allow our returns department up to five business days for processing. When your return is processed you will receive an email notification.
                
                Please Address Returns To:
                Victoria's Toy Station
                5466 Government Street
                Baton Rouge, La 70806
                If you have any questions or concerns, we can be reached at (225) 924-3632, Monday-Friday 10am-6pm CST",
                "shipping_and_return_policy_short" => "Merchandise may be returned within 30 days from the date the order was placed for exchange or store credit. All merchandise must be returned in its original box and packaging. Please note: All items that are marked down and/or are included in sales/promotions are FINAL SALE! These items cannot be returned for a store credit or be exchanged.",
                "address" => "5466 Government St, Baton Rouge, LA 70806, United States",
                "latitude" => "30.4444557",
                "longitude" => "-91.2037586"
            ]);

            BusinessStreetAddress::streetAddress($victoriasToysStationStore);

            $daysAndTiming = [
                'sunday' => [
                    'status' => 'inactive'
                ],
                'monday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'tuesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'wednesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'thursday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'friday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'saturaday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '16:00:00'
                    ]
                ]
            ];

            foreach ($daysAndTiming as $key => $dayAndTime) {
                $schedule = $victoriasToysStationStore->businessschedules()->where('name', $key)
                    ->first();
                if ($dayAndTime['status'] == 'active') {
                    $schedule->scheduletimes()->create($dayAndTime['timing']);
                    $schedule->update(['status' => $dayAndTime['status']]);
                }
            }

            $retailTag = StandardTag::whereSlug('retail')->first();
            $victoriasToysStationStore->standardTags()->sync([$retailTag->id]);
        }
    }
}
