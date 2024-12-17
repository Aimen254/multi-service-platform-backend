<?php

namespace Modules\Government\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class HealthDepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('Health Department'))->exists()) {
            $governmentEmployee = User::whereEmail('governmentEmployee2@interapptive.com')->first();
            $department = Business::create([
                "owner_id" => $governmentEmployee->id,
                "name" => 'Health Department',
                "slug" => Str::slug('Health Department'),
                "email" => "healthdept@gmail.com",
                "phone" => "+1 275-595-6742",
                "mobile" => "",
                'status' => 'active',
                "message" => "Monitoring and regulating healthcare providers, facilities, and services to ensure they meet established standards of quality and safety.",
                "short_description" => "Tracking and monitoring the prevalence of diseases and health conditions within the population to identify and respond to outbreaks or public health threats.Developing and implementing public health campaigns and initiatives to educate the public about healthy behaviors and preventive measures. Preparing for and responding to public health emergencies, including natural disasters, pandemics, and other health crises.",
                "long_description" => "Formulating and implementing policies and strategies to improve healthcare access, affordability, and quality.Gathering and analyzing health data to inform decision-making and public health interventions.Monitoring and addressing environmental factors that can impact public health, such as air and water quality.Working to reduce health disparities and ensure that all members of the population have equal access to healthcare services and health outcomes.",
                "address" => "11042 CEDAR PARK AVE BATON ROUGE, LA 70809",
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
                $schedule = $department->businessschedules()->where('name', $key)
                    ->first();
                if($schedule) {
                    if ($dayAndTime['status'] == 'active') {
                        $schedule->scheduletimes()->create($dayAndTime['timing']);
                        $schedule->update(['status' => $dayAndTime['status']]);
                    }
                }
            }

            $tags = StandardTag::whereIn('slug', ['government'])->pluck('id');
            $department->standardTags()->sync($tags);
        }
    }
}
