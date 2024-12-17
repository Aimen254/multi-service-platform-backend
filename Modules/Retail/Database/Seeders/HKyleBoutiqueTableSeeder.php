<?php

namespace Modules\Retail\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Traits\BusinessStreetAddress;
use Illuminate\Database\Eloquent\Model;

class HKyleBoutiqueTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('H Kyle Boutique'))->exists()) {
            $businessOwner = User::whereEmail('businessOwner2@interapptive.com')->first();
            $hKyleStore = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "H Kyle Boutique",
                "slug" => Str::slug('H Kyle Boutique'),
                "email" => "hello@shophkyle.com",
                "phone" => "+1 225-744-7902",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "H Kyle was started in the fall of 2013 by Tia in memory of her late mother, Martha. H Kyle has always been a dream of hers and she hopes to pass along her love of fashion to her own daughter, Harper Kyle. Located on the outskirts of Baton Rouge, LA, H Kyle is a place where you can find cute gifts and accessories, as well as contemporary and fashionable ladies' apparel in the parish of Ascension.",
                "shipping_and_return_policy" => "1. What is your return policy?

                All sales final. No refunds. All exchanges must be made within 7 days of the ORIGINAL purchase date for in-store purchases. Items must be unworn with tags attached and must be in sellable condition. The following items are final sale and cannot be returned for store credit or exchanged for other items: all jewelry items, custom apparel, Corkcicles, sale or promotional (discounted) items, accessories (Haute Shore), undergarments (bralettes), and holiday/seasonal items.

                2. What is the return policy for online orders?

                Shipping charges are non-refundable. Returns are eligible for STORE CREDIT or EXCHANGE ONLY. Please contact us at hello@shophkyle.com with any issues and return package within 3 days of receiving. The note care must be in the package for your credit to be processed. After we have accessed the clothing, we will process your return in the form of a store credit",
                "shipping_and_return_policy_short" => "",
                "address" => "H Kyle Boutique 14601 Airline Hwy #106, Gonzales, LA 70737, United States",
                "latitude" => "30.28204188027182",
                "longitude" => "-90.95372143639504"
            ]);

            BusinessStreetAddress::streetAddress($hKyleStore);

            $daysAndTiming = [
                'sunday' => [
                    'status' => 'inactive'
                ],
                'monday' => [
                    'status' => 'inactive',
                ],
                'tuesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '12:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'wednesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '12:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'thursday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '12:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'friday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '12:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'saturaday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '11:00:00',
                        'close_at' => '14:00:00'
                    ]
                ]
            ];

            foreach ($daysAndTiming as $key => $dayAndTime) {
                $schedule = $hKyleStore->businessschedules()->where('name', $key)->first();
                if ($dayAndTime['status'] == 'active') {
                    $schedule->scheduletimes()->create($dayAndTime['timing']);
                    $schedule->update(['status' => $dayAndTime['status']]);
                }
            }

            $tags = StandardTag::whereIn('slug', ['retail', 'fashion'])->pluck('id');
            $hKyleStore->standardTags()->sync($tags);
        }
    }
}
