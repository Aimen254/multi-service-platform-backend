<?php

namespace Modules\Retail\Database\Seeders;

use App\Models\User;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Traits\BusinessStreetAddress;
use App\Models\Business;
use Illuminate\Database\Eloquent\Model;

class PerlisTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('Perlis'))->exists()) {
            $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
            $perlisStore = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "Perlis",
                "slug" => Str::slug('Perlis'),
                "email" => "crawfish@perlis.com",
                "phone" => "(800) 725-6070",
                "mobile" => "",
                "message" => "PERLIS CLOTHING, A LOUISIANA TRADITION SINCE 1939",
                "short_description" => "Established in 1939, Perlis has become the go-to store for all your southern style clothing needs. Started by Rogers Perlis, the company and stores have been passed from father to son. For over 70 years we have been successful because of our loyal custom.",
                "long_description" => "Established in 1939, Perlis has become the go-to store for all your southern style clothing needs. Started by Rogers Perlis, the company and stores have been passed from father to son. For over 70 years we have been successful because of our loyal customers. We are constantly seeking out the most classic, well-made garments available to put on our shelves. We carry designer brands, made-to-measure items and our crawfish logo collection all year round.

                We are not only committed to our customers, but also to their immediate and extended families. Perlis is a family business in every sense of the word and we are excited to be a part of the New Orleans, Baton Rouge and Louisiana communities for generations to come.
                
                Perlis has grown to four stores, and a full online store here at Perlis.com, but the comfort and service haven’t changed! Stop by and see for yourself why Perlis is the place for all your Mardi Gras, and southern style clothing needs!",
                "shipping_and_return_policy" => "All orders typically ship the next business day. Business days are Monday through Friday. We do not process orders on New Year's Day, Mardi Gras, Independence Day, Labor Day, Thanksgiving, or Christmas Day.

                Our standard shipping method is UPS Ground. If you choose to ship via express service we will also use UPS. You will receive tracking information once your order is shipped. Shipments to PO boxes will be shipped via the United States Postal Service.
                
                Standard Delivery: $8.00 [FREE on orders over $150] | Arrives 2-5 business days after shipping. Please see chart below to calculate expected arrival date. This chart is for a basic UPS ground shipment from New Orleans, LA.
                
                Express Delivery: $35.00 | Arrives 1-2 Days after shipping
                Express plus Saturday Delivery: $50.00 | Delivery by 1:30 p.m. in the contiguous 48 states.
                
                You may also return or exchange your perlis.com order at any of our store locations.
                
                RETURNS
                Once your return has been received and inspected, your refund will be issued in the original form of payment within five business days. Please note that banking institutions may require additional days to process and post this transaction to your account once they have received the information from us. If you are returning a gift, we will issue you a store credit redeemable at any Perlis store or on perlis.com.
                
                EXCHANGES
                We will gladly exchange your purchase for a different size or any other item. If there is a difference in price between the items you are exchanging, we will charge or credit you as necessary.",
                "shipping_and_return_policy_short" => "All orders typically ship the next business day. Business days are Monday through Friday. We do not process orders on New Year's Day, Mardi Gras, Independence Day, Labor Day, Thanksgiving, or Christmas Day.
                You may also return or exchange your perlis.com order at any of our store locations.",
                "address" => "6070 Magazine St, New Orleans, LA 70118, USA",
                "latitude" => "29.9224061",
                "longitude" => "-90.1231046"
            ]);

            BusinessStreetAddress::streetAddress($perlisStore);

            $daysAndTiming = [
                'sunday' => [
                    'status' => 'inactive'
                ],
                'monday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'tuesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'wednesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'thursday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'friday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '18:00:00'
                    ]
                ],
                'saturaday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '18:00:00'
                    ]
                ]
            ];

            foreach ($daysAndTiming as $key => $dayAndTime) {
                $schedule = $perlisStore->businessschedules()->where('name', $key)->first();
                if ($dayAndTime['status'] == 'active') {
                    $schedule->scheduletimes()->create($dayAndTime['timing']);
                    $schedule->update(['status' => $dayAndTime['status']]);
                }
            }

            $retailTag = StandardTag::whereSlug('retail')->first();
            $perlisStore->standardTags()->sync([$retailTag->id]);
        }
    }
}
