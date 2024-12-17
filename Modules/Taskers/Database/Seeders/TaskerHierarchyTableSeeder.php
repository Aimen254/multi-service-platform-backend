<?php

namespace Modules\Taskers\Database\Seeders;

use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TaskerHierarchyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $taskers = StandardTag::where('slug', 'taskers')->where('type', 'module')->first();

        $data = [
            [
                'name' => 'Assembly',
                'children' => [
                    [
                        'name' => 'Furniture Assembly',
                        'children' => [
                            [
                                'name' => 'IKEA Furniture'
                            ],
                            [
                                'name' => 'Non-IKEA Furniture'
                            ],
                            [
                                'name' => 'Home Furniture'
                            ],
                            [
                                'name' => 'Office Furniture'
                            ],
                            [
                                'name' => 'Kitchen & Dining Furniture'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Exercise Equipment Assembly',
                        'children' => [
                            [
                                'name' => 'Treadmills'
                            ],
                            [
                                'name' => 'Rowing Machines'
                            ],
                            [
                                'name' => 'Ellipticals'
                            ],
                            [
                                'name' => 'Home Gyms'
                            ],
                            [
                                'name' => 'Spin Bikes'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Grill Assembly',
                        'children' => [
                            [
                                'name' => 'Gas Style Grill'
                            ],
                            [
                                'name' => 'Electrical Style Grill'
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Cleaning',
                'children' => [
                    [
                        'name' => 'Home Cleaning',
                        'children' => [
                            [
                                'name' => 'Sofa Cleaning'
                            ],
                            [
                                'name' => 'Carpet Cleaning'
                            ],
                            [
                                'name' => 'Mattress Cleaning'
                            ],
                            [
                                'name' => 'Chair Cleaning'
                            ],
                            [
                                'name' => 'Deep House Cleaning'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Office Cleaning',
                        'children' => [
                            [
                                'name' => 'Sofa Cleaning'
                            ],
                            [
                                'name' => 'Carpet Cleaning'
                            ],
                            [
                                'name' => 'Duct Cleaning'
                            ],
                            [
                                'name' => 'Chair Cleaning'
                            ],
                            [
                                'name' => 'Water Tank Cleaning'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Commercial Cleaning',
                        'children' => [
                            [
                                'name' => 'Multi-Tenant Office Buildings'
                            ],
                            [
                                'name' => 'Single-Tenant Buildings'
                            ],
                            [
                                'name' => 'Owner-Occupied Buildings'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Industrial Cleaning',
                        'children' => [
                            [
                                'name' => 'Factories'
                            ],
                            [
                                'name' => 'Warehouses'
                            ],
                            [
                                'name' => 'Warehouses'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Education & Tutoring',
                'children' => [
                    [
                        'name' => 'Private Tutors',
                        'children' => [
                            [
                                'name' => 'Math Tutors'
                            ],
                            [
                                'name' => 'Science Tutors'
                            ],
                            [
                                'name' => 'SAT/ACT Prep Tutors'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Academic Coaches',
                        'children' => [
                            [
                                'name' => 'Math Coaches'
                            ],
                            [
                                'name' => 'Science Coaches'
                            ],
                            [
                                'name' => 'Language Arts Coaches'
                            ],
                            [
                                'name' => 'Social Studies Coaches'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Language Instructors',
                        'children' => [
                            [
                                'name' => 'Curriculum Coordinators'
                            ],
                            [
                                'name' => 'Training Supervisors'
                            ],
                            [
                                'name' => 'Assessment Coordinators'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'TV Mounts',
                'children' => [
                    [
                        'name' => 'Fixed TV Mounts',
                        'children' => [
                            [
                                'name' => 'Low-Profile Fixed Mount'
                            ],
                            [
                                'name' => 'Tilted Fixed Mount'
                            ],
                            [
                                'name' => 'Flush Fixed Mount'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Articulating TV Mounts',
                        'children' => [
                            [
                                'name' => 'Full-Motion Articulating Mount'
                            ],
                            [
                                'name' => 'Dual-Arm Articulating Mount'
                            ],
                            [
                                'name' => 'Corner Articulating Mount'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Ceiling and Pole TV Mounts',
                        'children' => [
                            [
                                'name' => 'Ceiling Drop-Down Mount'
                            ],
                            [
                                'name' => 'Pole TV Mount'
                            ],
                            [
                                'name' => 'Motorized Ceiling Mount'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Outdoor TV Mounts',
                        'children' => [
                            [
                                'name' => 'Weatherproof Outdoor Mount'
                            ],
                            [
                                'name' => 'Tilted Outdoor Mount'
                            ],
                            [
                                'name' => 'Articulating Outdoor Mount'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Plumbing',
                'children' => [
                    [
                        'name' => 'Residential Plumbing',
                        'children' => [
                            [
                                'name' => 'Emergency Plumbing'
                            ],
                            [
                                'name' => 'Drain Cleaning'
                            ],
                            [
                                'name' => 'Pipe Repair and Replacement'
                            ],
                            [
                                'name' => 'Toilet Repair and Installation'
                            ],
                            [
                                'name' => 'Faucet and Fixture Repair/Replacement'
                            ],
                            [
                                'name' => 'Water Heater Installation and Repair'
                            ],
                            [
                                'name' => 'Garbage Disposal Repair and Installation'
                            ],
                            [
                                'name' => 'Sewer Line Cleaning and Repair'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Commercial Plumbing',
                        'children' => [
                            [
                                'name' => 'Commercial Pipe Installation and Repair'
                            ],
                            [
                                'name' => 'Boiler Installation and Repair'
                            ],
                            [
                                'name' => 'Backflow Prevention and Testing'
                            ],
                            [
                                'name' => 'Grease Trap Cleaning and Maintenance'
                            ],
                            [
                                'name' => 'Commercial Drain Services'
                            ],
                            [
                                'name' => 'Restaurant Plumbing Services'
                            ],
                            [
                                'name' => 'Garbage Disposal Repair and Installation'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        function taskerStandardTags($hierarchyTags, $moduleTag, $level)
        {
            foreach ($hierarchyTags as $item) {
                $tags = StandardTag::updateOrCreate(['slug' => Str::slug($item['name']), 'type' => 'product'], [
                    'name' => $item['name'],
                    'type' => 'product',
                    'status' => 'active',
                    'priority' => 1
                ]);

                $newLevel = $level;

                if (isset($item['children'])) {
                    $newLevel[] = $tags->id;
                    taskerStandardTags($item['children'], $moduleTag, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                        [
                            'L1' => $moduleTag->id,
                            'L2' => $newLevel[0],
                            'L3' => $newLevel[1],
                        ],
                        [
                            'level_type' => 4,
                            'is_multiple' => 1
                        ]
                    );
                    $heirarchy->standardTags()->syncWithoutDetaching($newLevel[2]);
                    $newLevel = [];
                }
            }
        }
        taskerStandardTags($data, $taskers, []);
    }
}
