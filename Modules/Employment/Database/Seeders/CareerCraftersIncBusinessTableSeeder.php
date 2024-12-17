<?php

namespace Modules\Employment\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CareerCraftersIncBusinessTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('careercrafters-inc'))->exists()) {
            $businessOwner = User::whereEmail('businessOwner2@interapptive.com')->first();
            $employmentBusiness = Business::create([
                "owner_id" => $businessOwner->id,
                "name" => "CareerCrafters Inc",
                "slug" => Str::slug('CareerCrafters Inc'),
                "email" => "",
                "phone" => "+1 335-982-6692",
                "mobile" => "",
                'status' => 'active',
                "message" => "We are committed to fostering a dynamic and inclusive work environment that values talent, creativity, and dedication.",
                "short_description" => "We believe in investing in our employees' professional development. Whether you're just starting your career or looking to take the next step, we offer ongoing training, mentorship programs, and opportunities for advancement within the company.",
                "long_description" => "We celebrate diversity and believe that a diverse workforce enhances creativity and innovation. Our inclusive culture ensures that every voice is heard, valued, and respected.We understand the importance of maintaining a healthy work-life balance. Our flexible work arrangements and supportive policies help you excel in your career while enjoying your personal life.
                We offer competitive salaries, bonuses, and comprehensive benefits packages to recognize and reward your hard work and commitment.",
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
                $schedule = $employmentBusiness->businessschedules()->where('name', $key)
                    ->first();
                if ($dayAndTime['status'] == 'active') {
                    $schedule->scheduletimes()->create($dayAndTime['timing']);
                    $schedule->update(['status' => $dayAndTime['status']]);
                }
            }

            $tags = StandardTag::whereIn('slug', ['employment', 'technology', 'software-development', 'information-security'])->pluck('id');
            $employmentBusiness->standardTags()->sync($tags);
        }
    }
}
