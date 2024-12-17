<?php

namespace Modules\Employment\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmploymentProductionHierarchySeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $employment = StandardTag::where('slug', 'employment')->first();
        
        $data = [
            [
                'name' => 'Transportation',
                'children' => [
                    [
                        'name' => 'Traffic controller',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Clerk',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Bus driver',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Conductor',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Dispatcher',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Transport manager',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Supply chain specialist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Truck driver',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Sales and Marketing',
                'children' => [
                    [
                        'name' => 'Brand ambassador',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Social media specialist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Telemarketer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Advertising executive',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Social media manager',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Digital campaign manager',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Account executive',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Sales representative',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Market researcher',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Information Technology',
                'children' => [
                    [
                        'name' => 'Data analyst',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Computer programmer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Web developer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Business intelligence analyst',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Network engineer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Database administrator',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Telecommunications engineer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Software engineer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Law Enforcement',
                'children' => [
                    [
                        'name' => 'Law enforcement officer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Security officer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Crime analyst',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'State trooper',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Criminal investigator',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Police sergeant',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Cyber crime investigator',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Business',
                'children' => [
                    [
                        'name' => 'Courier',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Clerk',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Finance specialist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'General manager',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Accountant',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Budget analyst',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Human resources specialist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Auditor',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Real estate agent',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Lawyer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Art',
                'children' => [
                    [
                        'name' => 'Actor',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Filmmaker',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Photographer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Choreographer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Musician',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Graphic designer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Dancer',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Curator',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Makeup artist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Artist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Communications',
                'children' => [
                    [
                        'name' => 'News reporter',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Journalist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Broadcaster',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Copywriter',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Public relations manager',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Publisher',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Production manager',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Hospitality',
                'children' => [
                    [
                        'name' => 'Receptionist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Barista',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Reservation agent',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Baggage handler',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Housekeeper',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Server',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Bartender',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Baker',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Event planner',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Chef',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Travel agent',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Health Care',
                'children' => [
                    [
                        'name' => 'Psychologist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Physical therapist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'School nurse',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Social worker',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Nutritionist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Veterinarian',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Dentist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Psychiatrist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Surgeon',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Science',
                'children' => [
                    [
                        'name' => 'Marine biologist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Meteorologist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Environmental scientist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Biologist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Chemist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Research scientist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Data scientist',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Education',
                'children' => [
                    [
                        'name' => 'Teacher',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Tutor',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Guidance counselor',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Librarian',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Associate professor',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'School principal',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ]
                ]
            ]
        ];

        function emplymentStandardTags($data, $employment, $level)
        {
            foreach ($data as $item) {
                $tags = StandardTag::updateOrCreate(['slug' => Str::slug($item['name']), 'type' => 'product'], [
                    'name' => $item['name'],
                    'type' => 'product',
                    'status' => 'active',
                    'priority' => 1,
                    'created_at' => Carbon::now(),
                ]);

                $newLevel = $level;

                if (isset($item['children'])) {
                    $newLevel[] = $tags->id;
                    emplymentStandardTags($item['children'], $employment, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                        [
                            'L1' => $employment->id,
                            'L2' => $newLevel[0],
                            'L3' => $newLevel[1],
                        ],
                        [
                            'level_type' => 4,
                            'is_multiple' => 1,
                            'created_at' => Carbon::now(),
                        ]
                    );
                    $heirarchy->standardTags()->syncWithoutDetaching($newLevel[2]);
                    $newLevel = [];
                }
            }
        }
        emplymentStandardTags($data, $employment, []);
    }
}
