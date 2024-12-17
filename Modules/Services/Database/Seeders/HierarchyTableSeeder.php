<?php

namespace Modules\Services\Database\Seeders;

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

        $services = StandardTag::where('slug', 'services')->first();
        $data = [
            [
                'name' => 'Electrical',
                'children' => [
                    [
                        'name' => 'Electrical Repairs',
                        'children' => [
                            [
                                'name' => 'Outlet Repair',
                            ],
                            [
                                'name' => 'Wiring Issues',
                            ],
                            [
                                'name' => 'Appliance Installation'
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Cleaning',
                'children' => [
                    [
                        'name' => 'House Cleaning',
                        'children' => [
                            [
                                'name' => 'Regular Cleaning'
                            ],
                            [
                                'name' => 'One Time Deep Cleaning'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        function servicesStandardTags($data, $services, $level)
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
                    servicesStandardTags($item['children'], $services, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                        [
                            'L1' => $services->id,
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
        servicesStandardTags($data, $services, []);
    }
}
