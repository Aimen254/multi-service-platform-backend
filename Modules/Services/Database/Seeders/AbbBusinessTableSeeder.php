<?php

namespace Modules\Services\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AbbBusinessTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('abb-group'))->exists()) {
            $businessOwner = User::whereEmail('businessOwner1@interapptive.com')->first();
            $handyHomeServices = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "ABB Group",
                "slug" => Str::slug('ABB Group'),
                "email" => "",
                "phone" => "+1 225-755-8882",
                "mobile" => "",
                'status' => 'active',
                "message" => "best services for electronics",
                "short_description" => "ABB Group Louisiana is a premier Used Dealer serving the greater Louisiana area. Our services includes the electrition, plumbres and Ac technician.",
                "long_description" => "ABB Group Louisiana is a premier Used Dealer serving the greater Louisiana area. Our services includes the electrition, plumbres and Ac technician. 
                HandyHome Services Louisiana provides the Best Services available online.",
                "address" => "11042 CEDAR PARK AVE BATON ROUGE, LA 70809",
            ]);


            $daysAndTiming = [
                'sunday' => [
                    'status' => 'inactive'
                ],
                'monday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '19:00:00'
                    ]
                ],
                'tuesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '19:00:00'
                    ]
                ],
                'wednesday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '19:00:00'
                    ]
                ],
                'thursday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '19:00:00'
                    ]
                ],
                'friday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '09:00:00',
                        'close_at' => '19:00:00'
                    ]
                ],
                'saturaday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '17:00:00'
                    ]
                ]
            ];

            foreach ($daysAndTiming as $key => $dayAndTime) {
                $schedule = $handyHomeServices->businessschedules()->where('name', $key)
                    ->first();
                if ($dayAndTime['status'] == 'active') {
                    $schedule->scheduletimes()->create($dayAndTime['timing']);
                    $schedule->update(['status' => $dayAndTime['status']]);
                }
            }

            $tags = StandardTag::whereIn('slug', ['services', 'cleaning', 'electrical'])->pluck('id');
            $handyHomeServices->standardTags()->sync($tags);
        }
    }
}
