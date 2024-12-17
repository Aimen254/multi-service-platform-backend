<?php

namespace Modules\Events\Database\Seeders;

use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventsHierarchyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $event = StandardTag::where('slug', 'events')->where('type', 'module')->first();

        return TagHierarchy::where('L1', $event->id)->delete();

        $data = [
            [
                'name' => 'Concerts',
                'children' => [
                    [
                        'name' => 'Music',
                        'children' => [
                            ['name' => 'Rock'],
                            ['name' => 'Jazz'],
                            ['name' => 'Classical'],
                            ['name' => 'Hip Hop'],
                        ]
                    ],
                    [
                        'name' => 'Shows',
                        'children' => [
                            ['name' => 'Circus'],
                            ['name' => 'Magic'],
                            ['name' => 'Comedy'],
                            ['name' => 'Automotive'],
                            ['name' => 'Zoo'],
                            ['name' => 'Firearms'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Sports',
                'children' => [
                    [
                        'name' => 'Football',
                        'children' => [
                            ['name' => 'Professional'],
                            ['name' => 'College'],
                            ['name' => 'High School'],
                        ],
                    ],
                    [
                        'name' => 'Basketball',
                        'children' => [
                            ['name' => 'Professional'],
                            ['name' => 'College'],
                            ['name' => 'High School'],
                        ],
                    ],
                    [
                        'name' => 'Gymnastics',
                        'children' => [
                            ['name' => 'Professional'],
                            ['name' => 'College'],
                            ['name' => 'High School'],
                        ],
                    ],
                    [
                        'name' => 'Swimming and Diving',
                        'children' => [
                            ['name' => 'Professional'],
                            ['name' => 'College'],
                            ['name' => 'High School'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Markets',
                'children' => [
                    [
                        'name' => 'Farmers',
                        'children' => [
                            ['name' => 'National Farmers Market Week'],
                        ]
                    ],
                    [
                        'name' => 'Retail',
                        'children' => [
                            ['name' => 'World Congress Retail'],
                        ]
                    ],
                    [
                        'name' => 'Artworks',
                        'children' => [
                            ['name' => 'Artworks Exhibition'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Conferences',
                'children' => [
                    [
                        'name' => 'Economic',
                        'children' => [
                            ['name' => 'Sales and Marketing'],
                            ['name' => 'Finance'],
                        ]
                    ],
                    [
                        'name' => 'Medical',
                        'children' => [
                            ['name' => 'Public Health'],
                            ['name' => 'Medical Research'],
                        ]
                    ],
                ]
            ],
        ];

        function eventsStandardTags($data, $event, $level)
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
                    eventsStandardTags($item['children'], $event, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                        [
                            'L1' => $event->id,
                            'L2' => $newLevel[0],
                            'L3' => $newLevel[1],
                        ],
                        [
                            'level_type' => 4,
                            'is_multiple' => 1,
                            'created_at' => Carbon::now(),
                        ]);
                    $heirarchy->standardTags()->syncWithoutDetaching($newLevel[2]);
                    $newLevel = [];
                }
            }
        }

        eventsStandardTags($data, $event, []);
    }
}
