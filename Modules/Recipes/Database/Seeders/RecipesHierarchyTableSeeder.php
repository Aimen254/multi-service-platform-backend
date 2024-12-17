<?php

namespace Modules\Recipes\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecipesHierarchyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $recipes = StandardTag::where('slug', 'recipes')->first();

        $data = [
            [
                'name' => 'Appetizers',
                'children' => [
                    [
                        'name' => 'General',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Dips',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Quiches',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Bites',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Moulds',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Seafood',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Vegetables',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Cheeses',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Cajun',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Other',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Seafood',
                'children' => [
                    [
                        'name' => 'Shrimp',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Saltwater',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Crabs',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Crawfish',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Freshwater',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Oysters',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Salmon',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Catfish',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Frogs',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Other',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Soups',
                'children' => [
                    [
                        'name' => 'Beef',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Chicken',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Vegetables',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Seafood',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Poultry',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Gumbos',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Bisques',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Stews',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Other',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Vegetables',
                'children' => [
                    [
                        'name' => 'Casseroles',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Cremes',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Potatoes',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Beans',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Peppers',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Carrots',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Cauliflower',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Sprouts',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Corn',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Cucumbers',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Eggplants',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ], [
                        'name' => 'Tomatoes',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ], [
                        'name' => 'Onions',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ], [
                        'name' => 'Other',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Poultry',
                'children' => [
                    [
                        'name' => 'Chicken',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Turkey',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Duck',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Hen',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Meats',
                'children' => [
                    [
                        'name' => 'Gravy',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'BBQ',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Smoked',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Grilled',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Ground',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Hamburgers',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Roasts',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Chops',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Steaks',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Sausage',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Boudin',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Rices',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Wild Game',
                'children' => [
                    [
                        'name' => 'Venison',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Ducks',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Alligator',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Rabbit',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Quail',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Pheasant',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Turtle',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Desserts',
                'children' => [
                    [
                        'name' => 'Cakes',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Pies',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Icings',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Puddings',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Brownies',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Cookies',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Casseroles',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Pralines',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Macaroons',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Rolls',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Puffs',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Ice Cream',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Gelato',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Crockpot',
                'children' => [
                    [
                        'name' => 'Appetizers',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Soups',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Chilies',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Stews',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Pork',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Beef',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ], [
                        'name' => 'Lamb',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ], [
                        'name' => 'Chicken',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Turkey',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Fish',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ], [
                        'name' => 'Seafood',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ], [
                        'name' => 'Vegetarian',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ], [
                        'name' => 'Sides',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ], [
                        'name' => 'Desserts',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Pizzas',
                'children' => [
                    [
                        'name' => 'Red Sauce',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'White Sauce',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'BBQ Sauce',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Thin Crust',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Deep Dish',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Vegetable',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Breakfast',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Drinks',
                'children' => [
                    [
                        'name' => 'Teas',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Lemonades',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Cocktails',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Cordials',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Punches',
                        'children' => [
                            [
                                'name' => 'All',
                            ]
                        ]
                    ],
                ]
            ],
        ];

        function recipesStandardTags($data, $recipes, $level)
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
                    recipesStandardTags($item['children'], $recipes, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                        [
                            'L1' => $recipes->id,
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
        recipesStandardTags($data, $recipes, []);
    }
}
