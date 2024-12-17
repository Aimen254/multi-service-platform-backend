<?php

namespace Modules\Retail\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Traits\BusinessStreetAddress;
use Illuminate\Database\Eloquent\Model;

class FrockCandyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('Frock Candy'))->exists()) {
            $businessOwner = User::whereEmail('businessOwner1@interapptive.com')->first();
            $frockCandyStore = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "Frock Candy",
                "slug" => Str::slug('Frock Candy'),
                "email" => "orders@frockcandy.com",
                "phone" => "+1 888-391-1165",
                "mobile" => "225 216 0216",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.","long_description" => "Frock Candy is a local women's fashion retailer founded in Baton Rouge, and we were named the 2019 Best of 225 winner for Best Local Women's Boutique.
                Our online Orders Department is separate from in store locations, and therefore cannot hold any items. If you'd like an item held for you in store, please call your preferred location, and a sales associate will be happy to check for your size. If your size is available, they can hold your item(s) until the end of the day.",
                "shipping_and_return_policy" => "Online returns are accepted on unwashed, unworn merchandise in original condition/tags attached within 21 days of the original invoice date for a full refund.  Returned packages must be postmarked within 14 days of the invoice date in order for us to receive within the 21-day timeframe.
                Shipped Orders placed online may be returned/exchanged in-store following the In-store Return Policy listed below.
                In-store Pickup orders placed online, will follow the In-store Return Policy(see below).
                If you receive an item that is defective or damaged, email us at orders@frockcandy.com with the damaged information so that we may issue a return label for the exchange.
                The following items will not be accepted for return/exchange due to hygienic reasons: tights, socks, intimates, and bodysuits.
                Giftcards are non-refundable.
                Shoes must be returned in their original, undamaged shoebox to receive a full refund. If a shoe is returned using the shoebox as the shipping box, we will only issue a refund of 50% in order to discount that item and resell.
                Shoes will not be accepted for return without their original, undamaged shoebox. Shoes received without their shoebox will be returned to the customer at their expense.
                Once your return is received and inspected, we will send you an email notifying you that we have received your returned item, along with the approved refund amount to the original form of payment.
                Please see the 'Online Shipping Information' section below to see the detailed return shipping process.",
                "address" => "Frok Candy 7474 Corporate Blvd, #305 Baton Rouge, LA 70809",
                "latitude" => "30.430522764689638",
                "longitude" => "-91.11368160300262"
            ]);

            BusinessStreetAddress::streetAddress($frockCandyStore);

            $daysAndTiming = [
                'sunday' => [
                    'status' => 'inactive'
                ],
                'monday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '19:00:00'
                    ]
                ],
                'tuesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '19:00:00'
                    ]
                ],
                'wednesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '19:00:00'
                    ]
                ],
                'thursday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '19:00:00'
                    ]
                ],
                'friday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '19:00:00'
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
                $schedule = $frockCandyStore->businessschedules()->where('name', $key)->first();
                if ($dayAndTime['status'] == 'active') {
                    $schedule->scheduletimes()->create($dayAndTime['timing']);
                    $schedule->update(['status' => $dayAndTime['status']]);
                }
            }

            $tags = StandardTag::whereIn('slug', ['retail', 'fashion'])->pluck('id');
            $frockCandyStore->standardTags()->sync($tags);
        }
    }
}
