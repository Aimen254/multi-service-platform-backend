<?php

namespace Modules\Employment\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmploymentHierarachyTableSeeder extends Seeder
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
                'name' => 'Technology',
                'children' => [
                    [
                        'name' => 'Software Development',
                        'children' => [
                            [
                                'name' => 'Frontend Developer',
                            ],
                            [
                                'name' => 'Backend Developer'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Information Security',
                        'children' => [
                            [
                                'name' => 'Security Analyst',
                            ],
                            [
                                'name' => 'Network Security Engineer'
                            ]
                        ]
                    ],
                ]
            ],
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
