<?php

namespace Modules\Retail\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Traits\BusinessStreetAddress;
use Illuminate\Database\Eloquent\Model;

class MessengersGiftsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('Messengers Gifts'))->exists()) {
            $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
            $messengersGiftsStore = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "Messengers Gifts",
                "slug" => Str::slug('Messengers Gifts'),
                "email" => "messengersgifts@gmail.com",
                "phone" => "(225) 250-5128",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.","long_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged",
                "shipping_and_return_policy" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged",
                "address" => "Messengers Gifts, George Oneal Road, Baton Rouge, LA, USA",
                "latitude" => "30.406699610349378",
                "longitude" => "-91.01561606067526"
            ]);

            BusinessStreetAddress::streetAddress($messengersGiftsStore);

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
                        'close_at' => '15:00:00'
                    ]
                ],
                'saturaday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '18:00:00'
                    ]
                ]
            ];

            foreach ($daysAndTiming as $key => $dayAndTime) {
                $schedule = $messengersGiftsStore->businessschedules()->where('name', $key)
                    ->first();
                if ($dayAndTime['status'] == 'active') {
                    $schedule->scheduletimes()->create($dayAndTime['timing']);
                    $schedule->update(['status' => $dayAndTime['status']]);
                }
            }

            $retailTag = StandardTag::whereSlug('retail')->first();
            $messengersGiftsStore->standardTags()->sync([$retailTag->id]);
        }
    }
}
