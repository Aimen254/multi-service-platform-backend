<?php

namespace Modules\Obituaries\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ObituariesHierarchyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $obituaries = StandardTag::where('slug', 'obituaries')->first();


        $data = [
            [
                'name' => 'Sex',
                'children' => [
                    [
                        'name' => 'Male',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Female',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ],
            ],
            [
                'name' => 'Last Name Letter',
                'children' => [
                    [
                        'name' => 'A',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'B',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'C',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'D',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'E',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'F',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'G',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'H',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'I',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'J',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'K',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'L',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'M',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'N',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'O',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'P',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Q',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'R',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'S',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'T',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'U',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'V',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'W',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'x',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Y',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Z',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ],
            ],
            [
                'name' => 'Parishes',
                'children' => [
                    [
                        'name' => 'East Baton Rouge',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'West Feliciana',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'East Feliciana',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Livingston',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tangipahoa',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Iberville',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'West Baton Rouge',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Pointe Coupee',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ],
            ],
            [
                'name' => 'Communities',
                'children' => [
                    [
                        'name' => 'Baton Rouge',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Central',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Baker',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Zachary',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'St. Francisville',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Denham Springs',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Walker',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Gonzales',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Watson',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Hammond',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Ponchatoula',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Port Allen',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Plaquemine',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'False River',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Other',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ],
            ]
        ];
        function obituariesStandardTags($data, $obituaries, $level)
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
                    obituariesStandardTags($item['children'], $obituaries, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                        [
                            'L1' => $obituaries->id,
                            'L2' => $newLevel[0],
                            'L3' => $newLevel[1],
                        ],
                        [
                            'level_type' => 4,
                            'is_multiple' => 1,
                            'created_at' => Carbon::now(),
                        ]
                    );
                    $heirarchy->standardTags()->sync($newLevel[2]);
                    $newLevel = [];
                }
            }
        }

        obituariesStandardTags($data, $obituaries, []);
    }
}
