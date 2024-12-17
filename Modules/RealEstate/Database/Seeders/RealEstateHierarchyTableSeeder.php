<?php

namespace Modules\RealEstate\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RealEstateHierarchyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $realEstate = StandardTag::where('slug', 'real-estate')->first();

        $data = [
            [
                'name' => 'Residential',
                'children' => [
                    [
                        'name' => 'Baker/Zachary',
                        'children' => [
                            [
                                'name' => '70714',
                            ],
                            [
                                'name' => '70791'
                            ],
                            [
                                'name' => '70811'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Central',
                        'children' => [
                            [
                                'name' => '70739',
                            ],
                            [
                                'name' => '70770'
                            ],
                            [
                                'name' => '70791'
                            ],
                            [
                                'name' => '70811'
                            ],
                            [
                                'name' => '70812'
                            ],
                            [
                                'name' => '70818'
                            ]
                        ]
                    ],
                    [
                        'name' => 'North Baton Rouge',
                        'children' => [
                            [
                                'name' => '70805',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Southeast Baton Rouge',
                        'children' => [
                            [
                                'name' => '70814',
                            ],
                            [
                                'name' => '70815',
                            ],
                            [
                                'name' => '70816',
                            ],
                            [
                                'name' => '70817',
                            ],
                            [
                                'name' => '70819',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Downtown, LSU and South Baton Rouge',
                        'children' => [
                            [
                                'name' => '70802',
                            ],
                            [
                                'name' => '70806',
                            ],
                            [
                                'name' => '70808',
                            ],
                            [
                                'name' => '70809',
                            ],
                            [
                                'name' => '70810',
                            ],
                            [
                                'name' => '70820',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Mid-City',
                        'children' => [
                            [
                                'name' => '70806',
                            ],
                            [
                                'name' => '70808',
                            ],
                            [
                                'name' => '70808',
                            ],
                            [
                                'name' => '70809',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Apartments',
                'children' => [
                    [
                        'name' => 'Baker/Zachary',
                        'children' => [
                            [
                                'name' => '70714',
                            ],
                            [
                                'name' => '70791'
                            ],
                            [
                                'name' => '70811'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Central',
                        'children' => [
                            [
                                'name' => '70739',
                            ],
                            [
                                'name' => '70770'
                            ],
                            [
                                'name' => '70791'
                            ],
                            [
                                'name' => '70811'
                            ],
                            [
                                'name' => '70812'
                            ],
                            [
                                'name' => '70818'
                            ]
                        ]
                    ],
                    [
                        'name' => 'North Baton Rouge',
                        'children' => [
                            [
                                'name' => '70805',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Southeast Baton Rouge',
                        'children' => [
                            [
                                'name' => '70814',
                            ],
                            [
                                'name' => '70815',
                            ],
                            [
                                'name' => '70816',
                            ],
                            [
                                'name' => '70817',
                            ],
                            [
                                'name' => '70819',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Downtown, LSU and South Baton Rouge',
                        'children' => [
                            [
                                'name' => '70802',
                            ],
                            [
                                'name' => '70806',
                            ],
                            [
                                'name' => '70808',
                            ],
                            [
                                'name' => '70809',
                            ],
                            [
                                'name' => '70810',
                            ],
                            [
                                'name' => '70820',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Mid-City',
                        'children' => [
                            [
                                'name' => '70806',
                            ],
                            [
                                'name' => '70808',
                            ],
                            [
                                'name' => '70808',
                            ],
                            [
                                'name' => '70809',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Multi-Family',
                'children' => [
                    [
                        'name' => 'Baker/Zachary',
                        'children' => [
                            [
                                'name' => '70714',
                            ],
                            [
                                'name' => '70791'
                            ],
                            [
                                'name' => '70811'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Central',
                        'children' => [
                            [
                                'name' => '70739',
                            ],
                            [
                                'name' => '70770'
                            ],
                            [
                                'name' => '70791'
                            ],
                            [
                                'name' => '70811'
                            ],
                            [
                                'name' => '70812'
                            ],
                            [
                                'name' => '70818'
                            ]
                        ]
                    ],
                    [
                        'name' => 'North Baton Rouge',
                        'children' => [
                            [
                                'name' => '70805',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Southeast Baton Rouge',
                        'children' => [
                            [
                                'name' => '70814',
                            ],
                            [
                                'name' => '70815',
                            ],
                            [
                                'name' => '70816',
                            ],
                            [
                                'name' => '70817',
                            ],
                            [
                                'name' => '70819',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Downtown, LSU and South Baton Rouge',
                        'children' => [
                            [
                                'name' => '70802',
                            ],
                            [
                                'name' => '70806',
                            ],
                            [
                                'name' => '70808',
                            ],
                            [
                                'name' => '70809',
                            ],
                            [
                                'name' => '70810',
                            ],
                            [
                                'name' => '70820',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Mid-City',
                        'children' => [
                            [
                                'name' => '70806',
                            ],
                            [
                                'name' => '70808',
                            ],
                            [
                                'name' => '70808',
                            ],
                            [
                                'name' => '70809',
                            ],
                        ]
                    ],
                ]
            ],
        ];

        function realEstateStandardTags($data, $realEstate, $level)
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
                    realEstateStandardTags($item['children'], $realEstate, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                        [
                            'L1' => $realEstate->id,
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
        realEstateStandardTags($data, $realEstate, []);
    }
}
