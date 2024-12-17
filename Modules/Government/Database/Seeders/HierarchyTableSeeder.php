<?php

namespace Modules\Government\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class HierarchyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $government = StandardTag::where('slug', 'government')->first();
        $data = [
            [
                'name' => 'Education',
                'children' => [
                    [
                        'name' => 'Elementary Education',
                        'children' => [
                            [
                                'name' => 'Curriculum Development',
                            ],
                            [
                                'name' => 'Teacher Training'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Higher Education',
                        'children' => [
                            [
                                'name' => 'University Affairs',
                            ],
                            [
                                'name' => 'University Scholarships',
                            ]
                        ]
                    ],
                ],
                'name' => 'Health',
                'children' => [
                    [
                        'name' => 'Mental Health',
                        'children' => [
                            [
                                'name' => 'Anxiety Disorders',
                            ],
                            [
                                'name' => 'Eating Disorders'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Addiction',
                        'children' => [
                            [
                                'name' => 'Drug Addiction',
                            ],
                            [
                                'name' => 'Tobacco Addiction',
                            ]
                        ]
                    ],
                ]
            ],
        ];

        function governmentStandardTags($data, $government, $level)
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
                    governmentStandardTags($item['children'], $government, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                        [
                            'L1' => $government->id,
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
        governmentStandardTags($data, $government, []);
    }
}
