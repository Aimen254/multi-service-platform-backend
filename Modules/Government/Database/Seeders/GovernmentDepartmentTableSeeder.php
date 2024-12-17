<?php

namespace Modules\Government\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class GovernmentDepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('Department of Education'))->exists()) {
            $employee = User::whereEmail('governmentEmployee1@interapptive.com')->first();
            $department = Business::create([
                "owner_id" => $employee->id,
                "name" => "Department of Education",
                "slug" => Str::slug('Department of Education'),
                "email" => "edudept@gmail.com",
                "phone" => "+1 245-795-8732",
                "mobile" => "",
                'status' => 'active',
                "message" => "Education is one thing no one can take away from you.",
                "short_description" => "Education is the process of acquiring knowledge, skills, values, and insights through formal instruction, informal experiences, and self-directed learning. It empowers individuals to develop intellectually, socially, and personally, preparing them to contribute meaningfully to society and navigate the challenges of the world.",
                "long_description" => "The Department of Education is an active, research-oriented department, responsive to the growing demands of educators in the broad National and International spectrum. We are fortunate to present to you a Department staffed by highly skilled professionals, many of whom have National and International reputation in their area of teaching and research. The Department takes pride in its M. Ed and M. Phil Programs for its leading position in the field of education throughout the country.Specific inquiry and analysis of educational issues is the hallmark of the department and our faculty remains actively engaged in research, in collaboration with government and international agencies. The Department of Education offers extraordinary professional development opportunities to individuals who are interested to become efficient educationist.",
                "address" => "11042 CEDAR PARK AVE BATON ROUGE, LA 70809",
                "latitude" => "-91.229003",
                "longitude" => "30.638527"
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
                'saturday' => [
                    'status' => 'active',
                    'timing' => [
                        'open_at' => '10:00:00',
                        'close_at' => '17:00:00'
                    ]
                ]
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
