<?php

namespace Modules\Boats\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BoatCityUsaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('Boats City'))->exists()) {
            $businessOwner = User::whereEmail('businessOwner1@interapptive.com')->first();
            $store = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "Boats City",
                "slug" => Str::slug('Boats City'),
                "email" => "bper@boatcityusa.com",
                "phone" => "985-542-2028",
                "mobile" => "",
                'status' => 'active',
                "message" => "ONE OF THE MOST TRUSTED BOAT DEALERS IN LOUISIANA.",
                "short_description" => "Boat City USA is a locally owned, full-service marine dealer.Our combination of store locations, product lines, professional sales staff, finance and insurance products, expert trained service and well-stocked parts and accessory departments allow us to provide what no other Louisiana boat dealer can.  Top it off with our private lake at the Hammond location, and it is easy to understand why Boat City USA is the best boat dealer for your needs.",
                "long_description" => "Boat City USA is a locally owned, full-service marine dealer.Our combination of store locations, product lines, professional sales staff, finance and insurance products, expert trained service and well-stocked parts and accessory departments allow us to provide what no other Louisiana boat dealer can.  Top it off with our private lake at the Hammond location, and it is easy to understand why Boat City USA is the best boat dealer for your needs.
                Boat City USA has spent years assembling a broad array of the finest boat brands in the industry.  We only represent manufacturers who build with quality, and who stand behind their products with superior customer and warranty service.  We are proud to offer Tracker, Sun Tracker, Regency, Nitro, Tahoe, Mako, Grizzly, Tidewater, Ranger, Tracker Off Road and Go Devil. Boat City USA has a boat brand and model to fit your need!
                When it comes to boats, Boat City USA has you covered. We would appreciate the opportunity to earn your business and trust.",
                "address" => "14113 Club Deluxe Road, Hammond, LA 70403",
                "latitude" => "-90.4969578",
                "longitude" => "30.476345"
            ]);

            $daysAndTiming = [
                'sunday' => [
                    'status' => 'inactive',
                ],
                'monday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '08:30:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'tuesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '08:30:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'wednesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '08:30:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'thursday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '08:30:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'friday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '08:30:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'saturaday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '13:00:00'
                    ]
                ],
            ];

            foreach ($daysAndTiming as $key => $dayAndTime) {
                $schedule = $store->businessschedules()->where('name', $key)
                    ->first();
                if ($dayAndTime['status'] == 'active') {
                    $schedule->scheduletimes()->create($dayAndTime['timing']);
                    $schedule->update(['status' => $dayAndTime['status']]);
                }
            }

            $tags = StandardTag::whereIn('slug', ['boats'])->pluck('id');
            $store->standardTags()->sync($tags);
        }
    }
}
