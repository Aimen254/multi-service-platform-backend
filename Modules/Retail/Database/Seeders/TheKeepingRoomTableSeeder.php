<?php

namespace Modules\Retail\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Traits\BusinessStreetAddress;
use Illuminate\Database\Eloquent\Model;

class TheKeepingRoomTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('The Keeping Room'))->exists()) {
            $businessOwner = User::whereEmail('businessOwner1@interapptive.com')->first();
            $perlisStore = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "The Keeping Room",
                "slug" => Str::slug('The Keeping Room'),
                "email" => "KEEPINGROOM@GMAIL.COM",
                "phone" => "(703) 360-6399",
                "mobile" => "",
                "message" => "QUALITY FURNITURE SINCE 1981",
                "short_description" => "The Keeping Room is a family business that began in 1981, originally dealing with period antiques. As prices soared for good American antiques, we moved into high-quality replicas that have the look and feel of 18th Century pieces but at a fraction of the price of the originals.",
                "long_description" => "The Keeping Room is a family business that began in 1981, originally dealing with period antiques. As prices soared for good American antiques, we moved into high-quality replicas that have the look and feel of 18th Century pieces but at a fraction of the price of the originals.

                As leather furniture became more popular, we expanded our product lines to include fine leather furnishings and have become the DC Metro Area's largest Hancock & Moore dealer.  
                
                As we surpass 40 years of business, one thing we have observed is that high-quality, classic reproductions and fine leather furniture not only hold their value over time, but can actually appreciate in value, making them true investment grade items you can enjoy daily.
                
                Shipping from our manufacturers is available to 48 states and we have our own delivery trucks for areas within 100 miles of Washington, DC.
                
                We have sold and built pieces for several museums and historic properties such as The White House, The Treasury Dept., Historic Mt. Vernon, Gadsby's Tavern, Carlysle House, Pequot Indian Museum, and Woodlawn Plantation, as well as many of the props for “The Patriot”, the Mel Gibson Revolutionary War movie.",
                "shipping_and_return_policy" => "The Keeping Room Terms and Conditions of Sale:

                •    All sales are Final. We are not able to accept returns.
                •    50% deposit is required on all orders. Deposits are non-refundable. If you cancel your order, you will be issued a store credit that is good for two (2) years. 
                •    No cancellations once an item is in production or has been shipped. Refusal to complete your order will result in forfeiture of deposit.
                •    No modifications may be made to your order after two (2) weeks of order being placed.
                •    The Keeping Room does not warranty furniture or home décor. Manufacturer’s Warranty applies in the event of defects, returns to the factory for inspection are to be arranged and paid for by the customer. The manufacturer has sole discretion to replace or repair any item as they see fit. You may be responsible for return shipping as well depending on manufacturers' policies.
                •    Build and delivery times will vary greatly and are estimates only. While we are always glad to check on your order status, it will be based on best available information from the supplier on the date we check and is not guaranteed. Delivery quotes are at the discretion of the carrier and not guaranteed beyond thirty days of date requested. 
                •    Credit cards on file will be automatically charged upon notice your order is complete. If paying via check, it must arrive at our store within (5) days of notification of completion. Orders will not ship until paid for in full.
                •    Damages in transit or claims for losses are the responsibility of the third-party shipping / delivery company who assumes title to the goods upon pick up. The Keeping Room is not responsible for damages from third-party company or shipper / delivery service. 
                •    Delivery companies will not move your existing furniture, please be sure to have your space clear for your new pieces on the day of delivery.
                
                Home Delivery with The Keeping Room:
                
                Home delivery from The Keeping Room is available within a 90-mile radius of store.
                
                •    We will contact you with a 2-hour delivery window prior to the day of delivery. Traffic and weather can extend that time and is outside our control. Our team will call you if they are running late.
                •    We deliver on Saturdays. Late afternoon / evening deliveries may be available at an additional charge of $100.00. For the safety of our team, we do not deliver after dark. 
                •    We can help you move existing furniture within the same room but cannot relocate existing furniture to other rooms (including garages and basements) or haul it away for disposal. 
                •    Please arrange to have your space clear on the day of delivery, with pathways clear and wall hangings removed along the travel path.
                •    We use a 2-man delivery team. Some items may be too heavy for our crew and require you to have additional lifting assistance available during delivery. 
                •    If we cannot safely deliver a piece into your desired room, we will place it in an accessible portion of your home (garage, patio, etc.). We cannot determine clearances and pathways of your home ahead of time and do not guarantee placement in a specific room.
                •    The Keeping Room is not responsible for damages during delivery to furniture, floors, walls, landscaping, fences, doorways, or trim due to inadequate clearances.
                •    Any flaws or defects must be noted at time of delivery. Post delivery service requests will require additional pick up and re-delivery charges. 
                •    Due to limited warehouse space, we cannot hold special orders for longer than three (3) weeks.",
                "shipping_and_return_policy_short" => "The Keeping Room Terms and Conditions of Sale:
                •    All sales are Final. We are not able to accept returns.
                •    50% deposit is required on all orders. Deposits are non-refundable. If you cancel your order, you will be issued a store credit that is good for two (2) years. 
                •    No cancellations once an item is in production or has been shipped. Refusal to complete your order will result in forfeiture of deposit.
                •    No modifications may be made to your order after two (2) weeks of order being placed.
                
                Home Delivery with The Keeping Room:
                Home delivery from The Keeping Room is available within a 90-mile radius of store.
                •    We will contact you with a 2-hour delivery window prior to the day of delivery. Traffic and weather can extend that time and is outside our control. Our team will call you if they are running late.
                •    We deliver on Saturdays. Late afternoon / evening deliveries may be available at an additional charge of $100.00. For the safety of our team, we do not deliver after dark. 
                •    We can help you move existing furniture within the same room but cannot relocate existing furniture to other rooms (including garages and basements) or haul it away for disposal. 
                •    Please arrange to have your space clear on the day of delivery, with pathways clear and wall hangings removed along the travel path.",
                "address" => "8405 RICHMOND HWY, SUITE G, ALEXANDRIA, VA 22309",
                "latitude" => "38.728452,",
                "longitude" => "-77.108282"
            ]);

            BusinessStreetAddress::streetAddress($perlisStore);

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
                        'open_at' => '09:30:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'wednesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:30:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'thursday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:30:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'friday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:30:00',
                        'close_at' => '17:30:00'
                    ]
                ],
                'saturaday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:30:00',
                        'close_at' => '17:30:00'
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
