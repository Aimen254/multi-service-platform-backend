<?php

namespace Modules\Retail\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Traits\BusinessStreetAddress;
use Illuminate\Database\Eloquent\Model;

class CarriagesFineClothierTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('Carriages Fine Clothier'))->exists()) {
            $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
            $carriagesFineClothierStore = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "Carriages Fine Clothier",
                "slug" => Str::slug('Carriages Fine Clothier'),
                "email" => "will@carriagesbr.com",
                "phone" => "225-926-6892",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "We do our best to ensure that all product seen online has available inventory.  However, sometimes an item could be sold out shortly before or after your purchase.  If this happens, we will notify you by email when your product will be available to ship.  This is why an accurate email and phone number is crucial when placing your order.",
                "shipping_and_return_policy" => "If you are not 100% satisfied with your purchase, you may exchange the item or return it for a full refund. Items should be returned new, unworn and with all original tags and labels still attached.  In addition, shoes should be returned unworn in their original, undamaged shoe box.  We reserve the right to refuse returns which are not in the above specified conditions.  Returns and exchanges will only be accepted within 30 days of the purchase date. If a full price item is put on sale after date of purchase, the new sale price will be honored for the refund.  

                If you would like to return or exchange an item, please send an email to joey@carriagesbr.com expressing your concern. Once we receive your request, we'll send you a pre-paid return shipping label which you can use to send your merchandise to us.
                
                Please mail returns/exchanges to:
                
                Carriages Fine Clothier                      
                
                Attention: Return
                
                7606 Old Hammond Hwy
                
                Baton Rouge, LA 70809
                
                Please include a copy of your receipt with your return.  If you do not have your receipt, include your name, order number and the best phone number to be contacted by.  Once your return is received and processed, we'll refund you in the original form of payment.  Please allow 3 - 5 business days for your refund to be reflected on your account.
                
                Note: All sale items are final and non-refundable.
                
                *Note: All Holiday returns must be made by January 15th!!
                
                Please feel free to contact us anytime:
                
                By email: joey@carriagesbr.com      
                
                By phone: (225) 926-6892",
                "address" => "Carriage Fine Clothier BATON ROUGE, LA, 70809 7620 OLD HAMMOND HWY",
                "latitude" => "30.431028405223667",
                "longitude" => "-91.10766721649459"
            ]);

            BusinessStreetAddress::streetAddress($carriagesFineClothierStore);

            $daysAndTiming = [
                'sunday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '12:00:00',
                        'close_at' => '15:00:00'
                    ]
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
                        'open_at' => '10:00:00',
                        'close_at' => '18:00:00'
                    ]
                ]
            ];

            foreach ($daysAndTiming as $key => $dayAndTime) {
                $schedule = $carriagesFineClothierStore->businessschedules()->where('name', $key)
                    ->first();
                if ($dayAndTime['status'] == 'active') {
                    $schedule->scheduletimes()->create($dayAndTime['timing']);
                    $schedule->update(['status' => $dayAndTime['status']]);
                }
            }

            $tags = StandardTag::whereIn('slug', ['retail', 'fashion'])->pluck('id');
            $carriagesFineClothierStore->standardTags()->sync($tags);
        }
    }
}
