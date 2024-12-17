<?php

namespace Modules\Notices\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NoticesHierarchyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $notices = StandardTag::where('slug', 'notices')->first();

        $data = [
            [
                'name' => 'Public Notices',
                'children' => [
                    [
                        'name' => 'Sherrif Sales',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Permits',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Lafayette',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Whereabout',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],

                ]
            ],
            [
                'name' => 'Legal Notices',
                'children' => [
                    [
                        'name' => 'Court summons',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Legal settlements',
                        'children' => [
                            [
                                'name' => 'All',
                            ],
                        ]
                    ],
                ]
            ],
        ];

        function noticesStandardTags($data, $notices, $level)
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
                    noticesStandardTags($item['children'], $notices, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                        [
                            'L1' => $notices->id,
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
        noticesStandardTags($data, $notices, []);
    }
}
