<?php

namespace Modules\Events\Database\Seeders;

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

        $event = StandardTag::where('slug', 'events')->where('type', 'module')->first();

        $data = [
            [
                'name' => 'Entertainment',
                'children' => [
                    [
                        'name' => 'Concerts',
                        'children' => [
                            ['name' => 'Rock'],
                            ['name' => 'Pop'],
                            ['name' => 'Classical'],
                            ['name' => 'Jazz'],
                            ['name' => 'Hip-Hop'],
                            ['name' => 'Country'],
                            ['name' => 'EDM'],
                            ['name' => 'Indie'],
                            ['name' => 'Folk'],
                            ['name' => 'Reggae'],
                        ]
                    ],
                    [
                        'name' => 'Shows',
                        'children' => [
                            ['name' => 'Broadway'],
                            ['name' => 'Comedy'],
                            ['name' => 'Magic'],
                            ['name' => 'Circus'],
                            ['name' => 'Fireworks'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Sports',
                'children' => [
                    [
                        'name' => 'Soccer',
                        'children' => [
                            ['name' => 'Leagues'],
                            ['name' => 'Tournament'],
                            ['name' => 'Charity Matches'],
                            ['name' => 'Coaching']
                        ]
                    ],
                    [
                        'name' => 'Basketball',
                        'children' => [
                            ['name' => 'Leagues'],
                            ['name' => 'Tournament'],
                            ['name' => 'Charity Matches'],
                            ['name' => 'Coaching']
                        ]
                    ],
                    [
                        'name' => 'Football',
                        'children' => [
                            ['name' => 'Leagues'],
                            ['name' => 'Tournament'],
                            ['name' => 'Charity Matches'],
                            ['name' => 'Coaching']
                        ]
                    ],
                    [
                        'name' => 'Baseball',
                        'children' => [
                            ['name' => 'Leagues'],
                            ['name' => 'Tournament'],
                            ['name' => 'Charity Matches'],
                            ['name' => 'Coaching']
                        ]
                    ],
                    [
                        'name' => 'Hockey',
                        'children' => [
                            ['name' => 'Leagues'],
                            ['name' => 'Tournament'],
                            ['name' => 'Charity Matches'],
                            ['name' => 'Coaching']
                        ]
                    ],
                    [
                        'name' => 'Tennis',
                        'children' => [
                            ['name' => 'Leagues'],
                            ['name' => 'Tournament'],
                            ['name' => 'Charity Matches'],
                            ['name' => 'Coaching']
                        ]
                    ],
                    [
                        'name' => 'Golf',
                        'children' => [
                            ['name' => 'Leagues'],
                            ['name' => 'Tournament'],
                            ['name' => 'Charity Matches'],
                            ['name' => 'Coaching']
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Community',
                'children' => [
                    [
                        'name' => 'Public',
                        'children' => [
                            ['name' => 'Town Hall Meetings'],
                            ['name' => 'Policy Forums'],
                            ['name' => 'Civic Discussions']
                        ]
                    ],
                    [
                        'name' => 'Charity',
                        'children' => [
                            ['name' => 'Auctions'],
                            ['name' => 'Dinners'],
                            ['name' => 'Donations'],
                        ]
                    ],
                    [
                        'name' => 'Market',
                        'children' => [
                            ['name' => 'Farmers'],
                            ['name' => 'Night Market'],
                            ['name' => 'Artisan Market'],
                        ]
                    ],
                    [
                        'name' => 'Parades',
                        'children' => [
                            ['name' => 'Holiday Parades'],
                        ]
                    ],
                    [
                        'name' => 'Festivals',
                        'children' => [
                            ['name' => 'Street'],
                            ['name' => 'Art'],
                            ['name' => 'Cultural'],
                            ['name' => 'Seasonal'],
                        ]
                    ],
                    [
                        'name' => 'Volunteer',
                        'children' => [
                            ['name' => 'Clean-ups'],
                            ['name' => 'Food Bank'],
                            ['name' => 'Animal Shelter'],
                        ]
                    ],
                ]
            ]
        ];

        $this->eventsStandardTags($data, $event, []);
    }

    function eventsStandardTags($data, $event, $level) {
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
                $this->eventsStandardTags($item['children'], $event, $newLevel);
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
}
