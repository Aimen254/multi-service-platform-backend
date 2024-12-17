<?php

namespace Modules\News\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
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

        $news = StandardTag::where('slug', 'news')->first();

        $data = [
            [
                'name' => 'Sports',
                'children' => [
                    [
                        'name' => 'Saints',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Pelicans',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Astros',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'LSU',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Southern',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'High School',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'NBA',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'NFL',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'MLB',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'PGA',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'LIV',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'MLS',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'NHL',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Olympics',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Football',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Baseball',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Basketball',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Soccer',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Swimming',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Volleyball',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Track & Field',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Golf',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Other Sport',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'World',
                'children' => [
                    [
                        'name' => 'Politics',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Economy',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Environment',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'National',
                'children' => [
                    [
                        'name' => 'National Security',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Foreign Relations',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Law and Justice',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Metro',
                'children' => [
                    [
                        'name' => 'Public Services',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Real Estate and Housing',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Urban Development',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
        ];

        $this->hierarchyGenerator($data, $news, []);
    }

    function hierarchyGenerator($data, $news, $level) : Bool {
        foreach ($data as $item) {
            $tags = StandardTag::updateOrCreate(['slug' => Str::slug($item['name']), 'type' => 'product'],[
                'name' => $item['name'],
                'type' => 'product',
                'status' => 'active',
                'priority' => 1,
                'created_at' => Carbon::now(),
            ]);

            $newLevel = $level;

            if (isset($item['children'])) {
                $newLevel[] = $tags->id;
                $this->hierarchyGenerator($item['children'], $news, $newLevel);
            } else {
                $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                    [
                        'L1' => $news->id, 
                        'L2' => $newLevel[0], 
                        'L3' => $newLevel[1],
                    ],
                    [
                        'level_type' => 4,
                        'is_multiple' => 1,
                        'created_at' => Carbon::now(),
                    ]);
                $heirarchy->standardTags()->sync($newLevel[2]);
                $newLevel = [];
            }
        }
        return true;
    }
}
