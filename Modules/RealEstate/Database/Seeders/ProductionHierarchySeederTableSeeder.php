<?php

namespace Modules\RealEstate\Database\Seeders;

use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ProductionHierarchySeederTableSeeder extends Seeder
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
                        'name' => 'Area 1 - Baker/Zachary',
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
                        'name' => 'Area 2 - Central',
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
                        'name' => 'Area 3 - North Baton Rouge',
                        'children' => [
                            [
                                'name' => '70805',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 4 - Southeast Baton Rouge',
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
                        'name' => 'Area 5 - Downtown, LSU and South Baton Rouge',
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
                        'name' => 'Area 6 - Mid-City',
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
                    [
                        'name' => 'Area 7 - West Baton Rouge and Iberville',
                        'children' => [
                            [
                                'name' => '70710',
                            ],
                            [
                                'name' => '70719',
                            ],
                            [
                                'name' => '70740',
                            ],
                            [
                                'name' => '70757',
                            ],
                            [
                                'name' => '70764',
                            ],
                            [
                                'name' => '70767',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Area 8 - Livingston & St. Helena',
                        'children' => [
                            [
                                'name' => '70441',
                            ],
                            [
                                'name' => '70449',
                            ],
                            [
                                'name' => '70462',
                            ],
                            [
                                'name' => '70706',
                            ],
                            [
                                'name' => '70711',
                            ],
                            [
                                'name' => '70726',
                            ],
                            [
                                'name' => '70733',
                            ],
                            [
                                'name' => '70754',
                            ],
                            [
                                'name' => '70785',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 9 - Ascension',
                        'children' => [
                            [
                                'name' => '70346',
                            ],
                            [
                                'name' => '70725',
                            ],
                            [
                                'name' => '70734',
                            ],
                            [
                                'name' => '70737',
                            ],
                            [
                                'name' => '70769',
                            ],
                            [
                                'name' => '70774',
                            ],
                            [
                                'name' => '70778',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 10 - Pointe Coupee',
                        'children' => [
                            [
                                'name' => '70722',
                            ],
                            [
                                'name' => '70732',
                            ],
                            [
                                'name' => '70748',
                            ],
                            [
                                'name' => '70749',
                            ],
                            [
                                'name' => '70755',
                            ],
                            [
                                'name' => '70760',
                            ],
                            [
                                'name' => '70761',
                            ],
                            [
                                'name' => '70775',
                            ],
                            [
                                'name' => '70777',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Multi-Family',
                'children' => [
                    [
                        'name' => 'Area 1 - Baker/Zachary',
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
                        'name' => 'Area 2 - Central',
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
                        'name' => 'Area 3 - North Baton Rouge',
                        'children' => [
                            [
                                'name' => '70805',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 4 - Southeast Baton Rouge',
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
                        'name' => 'Area 5 - Downtown, LSU and South Baton Rouge',
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
                        'name' => 'Area 6 - Mid-City',
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
                    [
                        'name' => 'Area 7 - West Baton Rouge and Iberville',
                        'children' => [
                            [
                                'name' => '70710',
                            ],
                            [
                                'name' => '70719',
                            ],
                            [
                                'name' => '70740',
                            ],
                            [
                                'name' => '70757',
                            ],
                            [
                                'name' => '70764',
                            ],
                            [
                                'name' => '70767',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Area 8 - Livingston & St. Helena',
                        'children' => [
                            [
                                'name' => '70441',
                            ],
                            [
                                'name' => '70449',
                            ],
                            [
                                'name' => '70462',
                            ],
                            [
                                'name' => '70706',
                            ],
                            [
                                'name' => '70711',
                            ],
                            [
                                'name' => '70726',
                            ],
                            [
                                'name' => '70733',
                            ],
                            [
                                'name' => '70754',
                            ],
                            [
                                'name' => '70785',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 9 - Ascension',
                        'children' => [
                            [
                                'name' => '70346',
                            ],
                            [
                                'name' => '70725',
                            ],
                            [
                                'name' => '70734',
                            ],
                            [
                                'name' => '70737',
                            ],
                            [
                                'name' => '70769',
                            ],
                            [
                                'name' => '70774',
                            ],
                            [
                                'name' => '70778',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 10 - Pointe Coupee',
                        'children' => [
                            [
                                'name' => '70722',
                            ],
                            [
                                'name' => '70732',
                            ],
                            [
                                'name' => '70748',
                            ],
                            [
                                'name' => '70749',
                            ],
                            [
                                'name' => '70755',
                            ],
                            [
                                'name' => '70760',
                            ],
                            [
                                'name' => '70761',
                            ],
                            [
                                'name' => '70775',
                            ],
                            [
                                'name' => '70777',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Commercial',
                'children' => [
                    [
                        'name' => 'Area 1 - Baker/Zachary',
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
                        'name' => 'Area 2 - Central',
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
                        'name' => 'Area 3 - North Baton Rouge',
                        'children' => [
                            [
                                'name' => '70805',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 4 - Southeast Baton Rouge',
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
                        'name' => 'Area 5 - Downtown, LSU and South Baton Rouge',
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
                        'name' => 'Area 6 - Mid-City',
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
                    [
                        'name' => 'Area 7 - West Baton Rouge and Iberville',
                        'children' => [
                            [
                                'name' => '70710',
                            ],
                            [
                                'name' => '70719',
                            ],
                            [
                                'name' => '70740',
                            ],
                            [
                                'name' => '70757',
                            ],
                            [
                                'name' => '70764',
                            ],
                            [
                                'name' => '70767',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Area 8 - Livingston & St. Helena',
                        'children' => [
                            [
                                'name' => '70441',
                            ],
                            [
                                'name' => '70449',
                            ],
                            [
                                'name' => '70462',
                            ],
                            [
                                'name' => '70706',
                            ],
                            [
                                'name' => '70711',
                            ],
                            [
                                'name' => '70726',
                            ],
                            [
                                'name' => '70733',
                            ],
                            [
                                'name' => '70754',
                            ],
                            [
                                'name' => '70785',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 9 - Ascension',
                        'children' => [
                            [
                                'name' => '70346',
                            ],
                            [
                                'name' => '70725',
                            ],
                            [
                                'name' => '70734',
                            ],
                            [
                                'name' => '70737',
                            ],
                            [
                                'name' => '70769',
                            ],
                            [
                                'name' => '70774',
                            ],
                            [
                                'name' => '70778',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 10 - Pointe Coupee',
                        'children' => [
                            [
                                'name' => '70722',
                            ],
                            [
                                'name' => '70732',
                            ],
                            [
                                'name' => '70748',
                            ],
                            [
                                'name' => '70749',
                            ],
                            [
                                'name' => '70755',
                            ],
                            [
                                'name' => '70760',
                            ],
                            [
                                'name' => '70761',
                            ],
                            [
                                'name' => '70775',
                            ],
                            [
                                'name' => '70777',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Rentals',
                'children' => [
                    [
                        'name' => 'Area 1 - Baker/Zachary',
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
                        'name' => 'Area 2 - Central',
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
                        'name' => 'Area 3 - North Baton Rouge',
                        'children' => [
                            [
                                'name' => '70805',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 4 - Southeast Baton Rouge',
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
                        'name' => 'Area 5 - Downtown, LSU and South Baton Rouge',
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
                        'name' => 'Area 6 - Mid-City',
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
                    [
                        'name' => 'Area 7 - West Baton Rouge and Iberville',
                        'children' => [
                            [
                                'name' => '70710',
                            ],
                            [
                                'name' => '70719',
                            ],
                            [
                                'name' => '70740',
                            ],
                            [
                                'name' => '70757',
                            ],
                            [
                                'name' => '70764',
                            ],
                            [
                                'name' => '70767',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Area 8 - Livingston & St. Helena',
                        'children' => [
                            [
                                'name' => '70441',
                            ],
                            [
                                'name' => '70449',
                            ],
                            [
                                'name' => '70462',
                            ],
                            [
                                'name' => '70706',
                            ],
                            [
                                'name' => '70711',
                            ],
                            [
                                'name' => '70726',
                            ],
                            [
                                'name' => '70733',
                            ],
                            [
                                'name' => '70754',
                            ],
                            [
                                'name' => '70785',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 9 - Ascension',
                        'children' => [
                            [
                                'name' => '70346',
                            ],
                            [
                                'name' => '70725',
                            ],
                            [
                                'name' => '70734',
                            ],
                            [
                                'name' => '70737',
                            ],
                            [
                                'name' => '70769',
                            ],
                            [
                                'name' => '70774',
                            ],
                            [
                                'name' => '70778',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 10 - Pointe Coupee',
                        'children' => [
                            [
                                'name' => '70722',
                            ],
                            [
                                'name' => '70732',
                            ],
                            [
                                'name' => '70748',
                            ],
                            [
                                'name' => '70749',
                            ],
                            [
                                'name' => '70755',
                            ],
                            [
                                'name' => '70760',
                            ],
                            [
                                'name' => '70761',
                            ],
                            [
                                'name' => '70775',
                            ],
                            [
                                'name' => '70777',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Multi-Class',
                'children' => [
                    [
                        'name' => 'Area 1 - Baker/Zachary',
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
                        'name' => 'Area 2 - Central',
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
                        'name' => 'Area 3 - North Baton Rouge',
                        'children' => [
                            [
                                'name' => '70805',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 4 - Southeast Baton Rouge',
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
                        'name' => 'Area 5 - Downtown, LSU and South Baton Rouge',
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
                        'name' => 'Area 6 - Mid-City',
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
                    [
                        'name' => 'Area 7 - West Baton Rouge and Iberville',
                        'children' => [
                            [
                                'name' => '70710',
                            ],
                            [
                                'name' => '70719',
                            ],
                            [
                                'name' => '70740',
                            ],
                            [
                                'name' => '70757',
                            ],
                            [
                                'name' => '70764',
                            ],
                            [
                                'name' => '70767',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Area 8 - Livingston & St. Helena',
                        'children' => [
                            [
                                'name' => '70441',
                            ],
                            [
                                'name' => '70449',
                            ],
                            [
                                'name' => '70462',
                            ],
                            [
                                'name' => '70706',
                            ],
                            [
                                'name' => '70711',
                            ],
                            [
                                'name' => '70726',
                            ],
                            [
                                'name' => '70733',
                            ],
                            [
                                'name' => '70754',
                            ],
                            [
                                'name' => '70785',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 9 - Ascension',
                        'children' => [
                            [
                                'name' => '70346',
                            ],
                            [
                                'name' => '70725',
                            ],
                            [
                                'name' => '70734',
                            ],
                            [
                                'name' => '70737',
                            ],
                            [
                                'name' => '70769',
                            ],
                            [
                                'name' => '70774',
                            ],
                            [
                                'name' => '70778',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Area 10 - Pointe Coupee',
                        'children' => [
                            [
                                'name' => '70722',
                            ],
                            [
                                'name' => '70732',
                            ],
                            [
                                'name' => '70748',
                            ],
                            [
                                'name' => '70749',
                            ],
                            [
                                'name' => '70755',
                            ],
                            [
                                'name' => '70760',
                            ],
                            [
                                'name' => '70761',
                            ],
                            [
                                'name' => '70775',
                            ],
                            [
                                'name' => '70777',
                            ],
                        ]
                    ],
                ]
            ]
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
