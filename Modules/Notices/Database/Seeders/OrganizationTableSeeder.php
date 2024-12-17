<?php

namespace Modules\Notices\Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class OrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if (!Business::whereSlug(Str::slug('Smith & Associates Law Firm'))->exists()) {
            $owner = User::whereEmail('businessOwner@interapptive.com')->first();
            $organization = Business::create([
                "owner_id" => $owner->id,
                "name" => "Smith & Associates Law Firm",
                "slug" => Str::slug('Smith & Associates Law Firm'),
                "email" => "smith&law@gmail.com",
                "phone" => "+1 245-795-8732",
                "mobile" => "",
                'status' => 'active',
                "message" => "Legal knowledge is an enduring asset, safeguarding your interests for a lifetime.",
                "short_description" => "Legal education is the systematic pursuit of knowledge, skills, and insights crucial to understanding and navigating the complex landscape of laws and regulations. It empowers individuals to approach legal matters with confidence, ensuring their rights and interests are protected.",
                "long_description" => "Welcome to Smit & Law Firm's Legal Education Department, a dynamic institution committed to excellence in legal research and education. Our firm is home to highly skilled legal professionals, many of whom are recognized for their expertise in various legal domains. We take pride in our leading legal education programs, positioning us at the forefront of legal knowledge dissemination. The cornerstone of our department is a rigorous inquiry into legal issues, with our faculty actively engaged in impactful research, often in collaboration with government bodies and international agencies. At Smit & Law Firm, our Legal Education Department offers exceptional opportunities for professional development to those aspiring to excel in the legal domain.",
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
                $schedule = $organization->businessschedules()->where('name', $key)
                    ->first();
                if ($schedule) {
                    if ($dayAndTime['status'] == 'active') {
                        $schedule->scheduletimes()->create($dayAndTime['timing']);
                        $schedule->update(['status' => $dayAndTime['status']]);
                    }
                }
            }

            $tags = StandardTag::whereIn('slug', ['notices', 'public-notices', 'legal-notices', 'court-summons', 'legal-settlements', 'permits', 'sherrif-sales'])->pluck('id');
            $organization->standardTags()->sync($tags);
        }
    }
}
