<?php

namespace Modules\Boats\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BennettBoatAndSkiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug("Three Eventstore"))->exists()) {
            $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
            $store = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "Three Eventstore",
                "slug" => Str::slug("Three Eventstore"),
                "email" => "sales@bennettsboatandski.com",
                "phone" => "225-654-9306",
                "mobile" => "",
                'status' => 'active',
                "message" => "The premier Master Craft boat dealer in South Louisiana.",
                "short_description" => "At Bennett's Boat and Ski, we are passionate about water.  We know that some of the best family experiences will happen on the water, and have experience this first hand.  We are a handful of people that love being on the water, boats and watersports.   All of us have experienced the joy of getting up in skis or wakeboard for the first time, or learning a cool new trick to impress, we love to have the opportunity to share our experiences with our customers, and we look forward to serving you for life!",
                "long_description" => "At Bennett's Boat and Ski, we are passionate about water.  We know that some of the best family experiences will happen on the water, and have experience this first hand.  We are a handful of people that love being on the water, boats and watersports.   All of us have experienced the joy of getting up in skis or wakeboard for the first time, or learning a cool new trick to impress, we love to have the opportunity to share our experiences with our customers, and we look forward to serving you for life!
                We have specialized in inboard towboats since 1979, and have focused our effort on providing friendly, familiar and consistent service through the years.  Our owner, Jay Bennett, is an experienced competitor, and US coach, his wife Anne and his daughter Danyelle are waterski World Champions, and they love to share their experiences and encourage families to be on the water.  Our general manager, shop manager and service manager are all highly involved on watersports, and are extremely knowledgeable about the products and services we offer.
                We look forward to helping you and your family have fun on the water, whether is with a new or used boat, a new wake set up, or by getting your current boat tuned up, you won't be disappointed with our services!",
                "address" => "18605 Barnett Rd, Zachary, LA 70791, United States",
                "latitude" => "-91.229003",
                "longitude" => "30.638527"
            ]);

            $daysAndTiming = [
                'sunday' => [
                    'status' => 'inactive',
                ],
                'monday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'tuesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'wednesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'thursday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'friday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'saturaday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '14:00:00'
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
