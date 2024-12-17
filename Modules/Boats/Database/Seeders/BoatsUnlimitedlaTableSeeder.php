<?php

namespace Modules\Boats\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BoatsUnlimitedlaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        if (!Business::whereSlug(Str::slug('Boats Unlimitedla'))->exists()) {
            $businessOwner = User::whereEmail('businessOwner2@interapptive.com')->first();
            $store = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "Boats Unlimitedla",
                "slug" => Str::slug('Boats Unlimitedla'),
                "email" => "jacey@boatsunlimitedla.com",
                "phone" => "225-357-3118",
                "mobile" => "",
                'status' => 'active',
                "message" => "Boating Is Our Business.",
                "short_description" => "Boats Unlimited is an award winning, family owned and operated business serving Louisiana and the Gulf Coast since 1983. Located in Baton Rouge, Louisiana, our highly trained and knowledgeable staff is committed to excellence, and our #1 goal is complete customer satisfaction.
                We carry the full lines from Xpress Boats and Gator Tail. We also stock Yamaha Marine and Gator Tail engines and can repower your present boat.",
                "long_description" => "Boats Unlimited is an award winning, family owned and operated business serving Louisiana and the Gulf Coast since 1983. Located in Baton Rouge, Louisiana, our highly trained and knowledgeable staff is committed to excellence, and our #1 goal is complete customer satisfaction.
                We carry the full lines from Xpress Boats and Gator Tail. We also stock Yamaha Marine and Gator Tail engines and can repower your present boat.
                With millions of dollars of new inventory, we are the largest volume dealer of Xpress Boats in the United States and the 'only' 9-time winner of Xpress Boats National Dealer of The Year Award.
                Visit our newly remodeled, 15,000 sq foot climate-controlled state of the art facility, browse in comfort and let us help find the perfect boat to meet all of your boating needs.",
                "address" => "7035 Airline Hwy, Baton Rouge, LA 70805",
            ]);

            $daysAndTiming = [
                'sunday' => [
                    'status' => 'inactive',
                ],
                'monday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '08:00:00',
                        'close_at' => '17:00:00'
                    ]
                ],
                'tuesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '08:00:00',
                        'close_at' => '17:00:00'
                    ]
                ],
                'wednesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '08:00:00',
                        'close_at' => '17:00:00'
                    ]
                ],
                'thursday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '08:00:00',
                        'close_at' => '17:00:00'
                    ]
                ],
                'friday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '08:00:00',
                        'close_at' => '17:00:00'
                    ]
                ],
                'saturaday' => [
                    'status' => 'inactive'
                ]
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
