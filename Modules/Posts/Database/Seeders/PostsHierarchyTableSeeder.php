<?php

namespace Modules\Posts\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class PostsHierarchyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $post = StandardTag::where('slug', 'posts')->first();

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
                'name' => 'Business',
                'children' => [
                    [
                        'name' => 'Local Economy',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'New Businesses',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Main Street',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Wall Street',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Healthcare',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Petro-Chemical',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Manufacturing',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Retail',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Real Estate',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Transportation',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Technology',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Farming',
                'children' => [
                    [
                        'name' => 'Agriculture',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Livestock',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Aquaculture',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Horticulture',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Government',
                'children' => [
                    [
                        'name' => 'President',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Congress',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Supreme Court',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Governor',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Federal',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'State',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Local',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Crime',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Courts',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Public Works',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Traffic',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Health',
                'children' => [
                    [
                        'name' => 'Covid',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Hospitals',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Physical Health',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Mental Health',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Biotechnology',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Addiction',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
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
                ]
            ],
            [
                'name' => 'Communities',
                'children' => [
                    [
                        'name' => 'Downtown BR',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Mid-City',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Southeast',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'North BR',
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
                        'name' => 'Baker',
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
                ]
            ],
            [
                'name' => 'Events',
                'children' => [
                    [
                        'name' => 'Concerts',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Fundraisers',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Fairs',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Conventions',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Trade Shows',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Farmers Markets',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Education',
                'children' => [
                    [
                        'name' => 'School Board',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Elementary',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Middle',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Secondary',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Higher',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Entertainment',
                'children' => [
                    [
                        'name' => 'Books',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Movies',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Music',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Theater',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Television',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Comics',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Horoscopes',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Life',
                'children' => [
                    [
                        'name' => 'Food & Drink',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Outdoors',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Travel',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Opinion',
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
                        'name' => 'Environmental',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Social',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Economics',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Public Policy',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Health',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Science',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Diplomacy',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Culture',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Local',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
        ];

        function postsStandardTags($data, $post, $level) {
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
                    postsStandardTags($item['children'], $post, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                    [
                        'L1' => $post->id, 
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
        }
        postsStandardTags($data, $post, []);
    }
}
