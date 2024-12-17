<?php

namespace Modules\Retail\Database\Seeders;

use Carbon\Carbon;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RetailHierarchyTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $retail = StandardTag::where('slug', 'retail')->where('type', 'module')->first();

        $data = [
            [
                'name' => 'Clothing',
                'children' => [
                    [
                        'name' => '	Men’s Apparel',
                        'children' => [
                            [
                                'name' => 'T-Shirts',
                            ],
                            [
                                'name' => 'Dress Shirts',
                            ],
                            [
                                'name' => 'Jeans',
                            ],
                            [
                                'name' => 'Suits',
                            ],
                            [
                                'name' => '	Jackets',
                            ],
                            [
                                'name' => 'Shorts',
                            ],
                            [
                                'name' => 'Sweaters',
                            ],
                            [
                                'name' => 'Activewear',
                            ],
                            [
                                'name' => 'Outerwear',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Women’s Apparel',
                        'children' => [
                            [
                                'name' => 'Dresses',
                            ],
                            [
                                'name' => 'Tops',
                            ],
                            [
                                'name' => 'Skirts',
                            ],
                            [
                                'name' => 'Pants',
                            ],
                            [
                                'name' => 'Outerwear',
                            ],
                            [
                                'name' => 'Sweaters',
                            ],
                            [
                                'name' => 'Activewear',
                            ],
                            [
                                'name' => 'Suits',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Kids’ Clothing',
                        'children' => [
                            [
                                'name' => 'Boys’ T-Shirts',
                            ],
                            [
                                'name' => 'Girls’ Dresses',
                            ],
                            [
                                'name' => 'Kids’ Jeans',
                            ],
                            [
                                'name' => 'Outerwear',
                            ],
                            [
                                'name' => 'School Uniforms',
                            ],
                            [
                                'name' => 'Sleepwear',
                            ],
                            [
                                'name' => 'Activewear',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Accessories',
                        'children' => [
                            [
                                'name' => 'Hats',
                            ],
                            [
                                'name' => 'Scarves',
                            ],
                            [
                                'name' => 'Gloves',
                            ],
                            [
                                'name' => 'Belts',
                            ],
                            [
                                'name' => 'Sunglasses',
                            ],
                            [
                                'name' => 'Socks & Hosiery',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Shoes',
                        'children' => [
                            [
                                'name' => 'Casual Shoes',
                            ],
                            [
                                'name' => 'Formal Shoes',
                            ],
                            [
                                'name' => 'Athletic Shoes',
                            ],
                            [
                                'name' => 'Boots',
                            ],
                            [
                                'name' => 'Sandals',
                            ],
                            [
                                'name' => 'Slippers',
                            ]
                        ]
                    ]
                ],
            ],
            [
                'name' => 'Electronics',
                'children' => [
                    [
                        'name' => 'Smartphones',
                        'children' => [
                            [
                                'name' => 'Apple',
                            ],
                            [
                                'name' => 'Samsung',
                            ],
                            [
                                'name' => 'Google',
                            ],
                            [
                                'name' => 'Accessories',
                            ],
                            [
                                'name' => 'Cases & Covers',
                            ],
                            [
                                'name' => 'Screen Protectors',
                            ],
                            [
                                'name' => 'Chargers',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Laptops',
                        'children' => [
                            [
                                'name' => 'Gaming Laptops',
                            ],
                            [
                                'name' => 'Business Laptops',
                            ],
                            [
                                'name' => 'Ultrabooks',
                            ],
                            [
                                'name' => 'Accessories',
                            ],
                            [
                                'name' => 'Laptop Bags',
                            ],
                            [
                                'name' => 'Docking Stations',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Home Audio',
                        'children' => [
                            [
                                'name' => 'Speakers',
                            ],
                            [
                                'name' => 'Soundbars',
                            ],
                            [
                                'name' => 'Home Theater Systems',
                            ],
                            [
                                'name' => 'Headphones',
                            ],
                            [
                                'name' => 'Earbuds',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Wearable Tech',
                        'children' => [
                            [
                                'name' => 'Smartwatches',
                            ],
                            [
                                'name' => 'Fitness Trackers',
                            ],
                            [
                                'name' => 'Smart Rings',
                            ],
                            [
                                'name' => 'Accessories',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Cameras',
                        'children' => [
                            [
                                'name' => 'Digital Cameras',
                            ],
                            [
                                'name' => 'Action Cameras',
                            ],
                            [
                                'name' => 'Camera Lenses',
                            ],
                            [
                                'name' => 'Tripods',
                            ],
                            [
                                'name' => 'Camera Bags  ',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Tablets',
                        'children' => [
                            [
                                'name' => 'iPads',
                            ],
                            [
                                'name' => 'Android Tablets',
                            ],
                            [
                                'name' => 'Accessories',
                            ],
                            [
                                'name' => 'Cases & Covers',
                            ]
                        ]
                    ]
                ],
            ],
            [
                'name' => '	Home Goods',
                'children' => [
                    [
                        'name' => 'Furniture',
                        'children' => [
                            [
                                'name' => 'Sofas',
                            ],
                            [
                                'name' => 'Dining Tables',
                            ],
                            [
                                'name' => 'Bed Frames',
                            ],
                            [
                                'name' => 'Chairs',
                            ],
                            [
                                'name' => 'Storage Solutions',
                            ],
                            [
                                'name' => 'Desks',
                            ],
                            [
                                'name' => 'Bookshelves',
                            ],
                            [
                                'name' => 'Office Furniture',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Kitchenware',
                        'children' => [
                            [
                                'name' => 'Cookware',
                            ],
                            [
                                'name' => 'Utensils',
                            ],
                            [
                                'name' => 'Small Appliances',
                            ],
                            [
                                'name' => 'Tableware',
                            ],
                            [
                                'name' => 'Storage Containers',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Bedding',
                        'children' => [
                            [
                                'name' => 'Bed Sheets',
                            ],
                            [
                                'name' => 'Comforters',
                            ],
                            [
                                'name' => 'Pillows',
                            ],
                            [
                                'name' => 'Mattress Protectors',
                            ],
                            [
                                'name' => 'Bed Skirts',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Decor',
                        'children' => [
                            [
                                'name' => '	Wall Art',
                            ],
                            [
                                'name' => 'Rugs',
                            ],
                            [
                                'name' => 'Curtains',
                            ],
                            [
                                'name' => 'Vases',
                            ],
                            [
                                'name' => 'Lighting Fixtures',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Lighting',
                        'children' => [
                            [
                                'name' => 'Table Lamps',
                            ],
                            [
                                'name' => 'Floor Lamps',
                            ],
                            [
                                'name' => 'Ceiling Lights',
                            ],
                            [
                                'name' => 'Outdoor Lighting',
                            ],
                            [
                                'name' => 'Light Bulbs',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Rugs & Carpets',
                        'children' => [
                            [
                                'name' => 'Area Rugs',
                            ],
                            [
                                'name' => 'Runners',
                            ],
                            [
                                'name' => 'Doormats',
                            ],
                            [
                                'name' => 'Carpet Tiles',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Storage & Organization',
                        'children' => [
                            [
                                'name' => 'Shelving Units',
                            ],
                            [
                                'name' => 'Storage Bins',
                            ],
                            [
                                'name' => 'Closet Organizers',
                            ],
                            [
                                'name' => '	Drawer Organizers',
                            ],
                            [
                                'name' => '	Hooks & Hangers',
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Beauty & Personal Care',
                'children' => [
                    [
                        'name' => 'Skincare',
                        'children' => [
                            [
                                'name' => 'Moisturizers',
                            ],
                            [
                                'name' => 'Cleansers',
                            ],
                            [
                                'name' => 'Serums',
                            ],
                            [
                                'name' => 'Sunscreens',
                            ],
                            [
                                'name' => 'Face Masks',
                            ],
                            [
                                'name' => 'Exfoliators',
                            ],
                            [
                                'name' => 'Toners',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Haircare',
                        'children' => [
                            [
                                'name' => 'Shampoos',
                            ],
                            [
                                'name' => 'Conditioners',
                            ],
                            [
                                'name' => 'Hair Treatments',
                            ],
                            [
                                'name' => 'Styling Products',
                            ],
                            [
                                'name' => 'Hair Tools',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Makeup',
                        'children' => [
                            [
                                'name' => 'Foundations',
                            ],
                            [
                                'name' => 'Eyeliners',
                            ],
                            [
                                'name' => 'Lipsticks',
                            ],
                            [
                                'name' => 'Mascaras',
                            ],
                            [
                                'name' => 'Blushes',
                            ],
                            [
                                'name' => 'Eyeshadows',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Fragrances',
                        'children' => [
                            [
                                'name' => 'Perfumes',
                            ],
                            [
                                'name' => 'Colognes',
                            ],
                            [
                                'name' => 'Body Sprays',
                            ],
                            [
                                'name' => 'Fragrance Sets',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Personal Hygiene',
                        'children' => [
                            [
                                'name' => 'Deodorants',
                            ],
                            [
                                'name' => 'Body Washes',
                            ],
                            [
                                'name' => 'Toothpastes',
                            ],
                            [
                                'name' => 'Shaving Creams',
                            ],
                            [
                                'name' => 'Hand Soaps',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Bath & Body',
                        'children' => [
                            [
                                'name' => 'Bath Bombs',
                            ],
                            [
                                'name' => 'Body Scrubs',
                            ],
                            [
                                'name' => 'Body Lotions',
                            ],
                            [
                                'name' => 'Bath Salts',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Nail Care',
                        'children' => [
                            [
                                'name' => 'Nail Polishes',
                            ],
                            [
                                'name' => 'Nail Tools',
                            ],
                            [
                                'name' => 'Nail Treatments',
                            ],
                            [
                                'name' => 'Manicure Kits',
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Sports & Outdoors',
                'children' => [
                    [
                        'name' => 'Fitness Equipment',
                        'children' => [
                            [
                                'name' => 'Treadmills',
                            ],
                            [
                                'name' => 'Dumbbells',
                            ],
                            [
                                'name' => '	Yoga Mats',
                            ],
                            [
                                'name' => '	Exercise Bikes',
                            ],
                            [
                                'name' => 'Resistance Bands',
                            ],
                            [
                                'name' => 'Ellipticals',
                            ],
                            [
                                'name' => 'Kettlebells',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Outdoor Gear',
                        'children' => [
                            [
                                'name' => 'Tents',
                            ],
                            [
                                'name' => 'Sleeping Bags',
                            ],
                            [
                                'name' => 'Backpacks',
                            ],
                            [
                                'name' => 'Hiking Poles',
                            ],
                            [
                                'name' => 'Camping Chairs',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Apparel',
                        'children' => [
                            [
                                'name' => 'Running Gear',
                            ],
                            [
                                'name' => 'Cycling Wear',
                            ],
                            [
                                'name' => 'Hiking Clothing',
                            ],
                            [
                                'name' => 'Swimwear',
                            ],
                            [
                                'name' => 'Outdoor Jackets',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Sports Accessories',
                        'children' => [
                            [
                                'name' => 'Water Bottles',
                            ],
                            [
                                'name' => 'Gym Bags',
                            ],
                            [
                                'name' => 'Sports Watches',
                            ],
                            [
                                'name' => 'Fitness Trackers',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Bikes & Scooters',
                        'children' => [
                            [
                                'name' => 'Mountain Bikes',
                            ],
                            [
                                'name' => 'Road Bikes',
                            ],
                            [
                                'name' => 'Electric Scooters',
                            ],
                            [
                                'name' => 'Bike Accessories',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Camping Gear',
                        'children' => [
                            [
                                'name' => 'Camping Stoves',
                            ],
                            [
                                'name' => 'Lanterns',
                            ],
                            [
                                'name' => 'Camping Furniture',
                            ],
                            [
                                'name' => 'Cooking Utensils',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Water Sports',
                        'children' => [
                            [
                                'name' => 'Paddleboards',
                            ],
                            [
                                'name' => 'Kayaks',
                            ],
                            [
                                'name' => 'Snorkeling Gear',
                            ],
                            [
                                'name' => 'Wetsuits',
                            ]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Toys & Games',
                'children' => [
                    [
                        'name' => 'Action Figures',
                        'children' => [
                            [
                                'name' => 'Superheroes',
                            ],
                            [
                                'name' => 'Anime Figures',
                            ],
                            [
                                'name' => 'Movie Characters',
                            ],
                            [
                                'name' => 'Collectibles',
                            ],
                            [
                                'name' => 'Limited Editions',
                            ],
                            [
                                'name' => 'Statues',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Board Games',
                        'children' => [
                            [
                                'name' => 'Strategy Games',
                            ],
                            [
                                'name' => 'Party Games',
                            ],
                            [
                                'name' => 'Family Games',
                            ],
                            [
                                'name' => 'Educational Games',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Puzzles',
                        'children' => [
                            [
                                'name' => 'Jigsaw Puzzles',
                            ],
                            [
                                'name' => '3D Puzzles',
                            ],
                            [
                                'name' => 'Brain Teasers',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Educational Toys',
                        'children' => [
                            [
                                'name' => 'STEM Toys',
                            ],
                            [
                                'name' => 'Learning Kits',
                            ],
                            [
                                'name' => 'Building Blocks',
                            ],
                            [
                                'name' => 'Science Kits',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Outdoor Toys',
                        'children' => [
                            [
                                'name' => 'Swing Sets',
                            ],
                            [
                                'name' => 'Trampolines',
                            ],
                            [
                                'name' => 'Sports Toys',
                            ],
                            [
                                'name' => 'Sand Toys',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Building Sets',
                        'children' => [
                            [
                                'name' => 'LEGO',
                            ],
                            [
                                'name' => 'Magnetic Tiles',
                            ],
                            [
                                'name' => 'Construction Kits',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Plush Toys',
                        'children' => [
                            [
                                'name' => 'Stuffed Animals',
                            ],
                            [
                                'name' => 'Plush Dolls',
                            ],
                            [
                                'name' => 'Character Plush',
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Groceries',
                'children' => [
                    [
                        'name' => 'Fresh Produce',
                        'children' => [
                            [
                                'name' => 'Fruits',
                            ],
                            [
                                'name' => 'Vegetables',
                            ],
                            [
                                'name' => 'Herbs',
                            ],
                            [
                                'name' => 'Organic Options',
                            ],
                            [
                                'name' => 'Exotic Produce',
                            ],
                            [
                                'name' => 'Salad Mixes',
                            ],
                            [
                                'name' => 'Root Vegetables',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Dairy Products',
                        'children' => [
                            [
                                'name' => 'Milk',
                            ],
                            [
                                'name' => 'Cheese',
                            ],
                            [
                                'name' => 'Yogurt',
                            ],
                            [
                                'name' => 'Butter',
                            ],
                            [
                                'name' => 'Cream',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Snacks',
                        'children' => [
                            [
                                'name' => 'Chips',
                            ],
                            [
                                'name' => 'Cookies',
                            ],
                            [
                                'name' => 'Nuts',
                            ],
                            [
                                'name' => 'Candy',
                            ],
                            [
                                'name' => 'Granola Bars',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Beverages',
                        'children' => [
                            [
                                'name' => 'Soft Drinks',
                            ],
                            [
                                'name' => 'Juices',
                            ],
                            [
                                'name' => 'Coffee & Tea',
                            ],
                            [
                                'name' => 'Alcoholic Beverages',
                            ],
                            [
                                'name' => 'Water',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Pantry Staples',
                        'children' => [
                            [
                                'name' => 'Pasta',
                            ],
                            [
                                'name' => 'Rice',
                            ],
                            [
                                'name' => 'Canned Goods',
                            ],
                            [
                                'name' => 'Spices & Herbs',
                            ],
                            [
                                'name' => 'Cooking Oils',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Frozen Foods',
                        'children' => [
                            [
                                'name' => 'Frozen Vegetables',
                            ],
                            [
                                'name' => 'Frozen Fruits',
                            ],
                            [
                                'name' => 'Frozen Meals',
                            ],
                            [
                                'name' => 'Ice Cream',
                            ],
                            [
                                'name' => 'Frozen Breads',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Meat & Seafood',
                        'children' => [
                            [
                                'name' => 'Fresh Meat',
                            ],
                            [
                                'name' => 'Frozen Meat',
                            ],
                            [
                                'name' => 'Fresh Seafood',
                            ],
                            [
                                'name' => 'Frozen Seafood',
                            ],
                            [
                                'name' => 'Processed Meats',
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Books',
                'children' => [
                    [
                        'name' => 'Fiction',
                        'children' => [
                            [
                                'name' => 'Mystery',
                            ],
                            [
                                'name' => 'Romance',
                            ],
                            [
                                'name' => 'Science Fiction',
                            ],
                            [
                                'name' => 'Fantasy',
                            ],
                            [
                                'name' => 'Historical Fiction',
                            ],
                            [
                                'name' => 'Literary Fiction',
                            ],
                            [
                                'name' => 'Thrillers',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Non-Fiction',
                        'children' => [
                            [
                                'name' => 'Biographies',
                            ],
                            [
                                'name' => 'Self-Help',
                            ],
                            [
                                'name' => 'Travel',
                            ],
                            [
                                'name' => 'Health & Wellness',
                            ],
                            [
                                'name' => 'History',
                            ],
                            [
                                'name' => 'True Crime',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Textbooks',
                        'children' => [
                            [
                                'name' => 'Science',
                            ],
                            [
                                'name' => 'Math',
                            ],
                            [
                                'name' => 'Literature',
                            ],
                            [
                                'name' => 'History',
                            ],
                            [
                                'name' => 'Language Arts',
                            ]
                        ]
                    ],
                    [
                        'name' => "Children's Books",
                        'children' => [
                            [
                                'name' => 'Picture Books',
                            ],
                            [
                                'name' => 'Early Readers',
                            ],
                            [
                                'name' => 'Chapter Books',
                            ],
                            [
                                'name' => 'Middle-Grade Books',
                            ]
                        ]
                    ],
                    [
                        'name' => 'eBooks',
                        'children' => [
                            [
                                'name' => 'Fiction',
                            ],
                            [
                                'name' => 'Non-Fiction',
                            ],
                            [
                                'name' => 'Educational',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Bestsellers',
                        'children' => [
                            [
                                'name' => 'Top Fiction',
                            ],
                            [
                                'name' => 'Top Non-Fiction',
                            ],
                            [
                                'name' => 'New Releases',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Cookbooks',
                        'children' => [
                            [
                                'name' => 'Baking',
                            ],
                            [
                                'name' => 'Healthy Eating',
                            ],
                            [
                                'name' => 'International Cuisine',
                            ],
                            [
                                'name' => 'Quick Meals',
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Health & Wellness',
                'children' => [
                    [
                        'name' => 'Vitamins & Supplements',
                        'children' => [
                            [
                                'name' => 'Multivitamins',
                            ],
                            [
                                'name' => 'Omega-3s',
                            ],
                            [
                                'name' => 'Protein Powders',
                            ],
                            [
                                'name' => 'Herbal Supplements',
                            ],
                            [
                                'name' => 'Joint Health',
                            ],
                            [
                                'name' => 'Immune Support',
                            ],
                            [
                                'name' => 'Weight Management ',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Personal Care',
                        'children' => [
                            [
                                'name' => 'Oral Care',
                            ],
                            [
                                'name' => 'Feminine Hygiene',
                            ],
                            [
                                'name' => "Men's Grooming",
                            ],
                            [
                                'name' => 'Shaving Products',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Fitness & Exercise',
                        'children' => [
                            [
                                'name' => 'Exercise Equipment',
                            ],
                            [
                                'name' => 'Workout Apparel',
                            ],
                            [
                                'name' => 'Fitness Accessories',
                            ],
                            [
                                'name' => 'Home Gym Equipment',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Child',
                        'children' => [
                            [
                                'name' => 'Adoption',
                            ],
                        ]
                    ],
                    [
                        'name' => 'First Aid',
                        'children' => [
                            [
                                'name' => 'Bandages',
                            ],
                            [
                                'name' => 'Antiseptics',
                            ],
                            [
                                'name' => 'Pain Relief',
                            ],
                            [
                                'name' => '	Medical Kits',
                            ],
                            [
                                'name' => 'First Aid Manuals',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Wellness Products',
                        'children' => [
                            [
                                'name' => 'Aromatherapy',
                            ],
                            [
                                'name' => 'Essential Oils',
                            ],
                            [
                                'name' => 'Massage Tools',
                            ],
                            [
                                'name' => 'Sleep Aids',
                            ]
                        ]
                    ],
                    [
                        'name' => '	Medical Equipment',
                        'children' => [
                            [
                                'name' => 'Blood Pressure Monitors',
                            ],
                            [
                                'name' => 'Thermometers',
                            ],
                            [
                                'name' => '	Glucose Meters',
                            ],
                            [
                                'name' => 'Mobility Aids',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Herbal Remedies',
                        'children' => [
                            [
                                'name' => 'Teas',
                            ],
                            [
                                'name' => 'Supplements',
                            ],
                            [
                                'name' => 'Topicals',
                            ],
                            [
                                'name' => 'Extracts',
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Jewelry & Watches',
                'children' => [
                    [
                        'name' => 'Necklaces',
                        'children' => [
                            [
                                'name' => 'Chains',
                            ],
                            [
                                'name' => 'Pendants',
                            ],
                            [
                                'name' => 'Lockets',
                            ],
                            [
                                'name' => 'Statement Necklaces',
                            ],
                            [
                                'name' => 'Chokers',
                            ],
                            [
                                'name' => 'Beaded Necklaces',
                            ],
                            [
                                'name' => 'Layered Necklaces',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Earrings',
                        'children' => [
                            [
                                'name' => 'Studs',
                            ],
                            [
                                'name' => 'Hoops',
                            ],
                            [
                                'name' => 'Drop Earrings',
                            ],
                            [
                                'name' => 'Dangle Earrings',
                            ],
                            [
                                'name' => 'Ear Cuffs',
                            ],
                            [
                                'name' => 'Huggies',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Bracelets',
                        'children' => [
                            [
                                'name' => 'Bangles',
                            ],
                            [
                                'name' => 'Cuffs',
                            ],
                            [
                                'name' => 'Charm Bracelets',
                            ],
                            [
                                'name' => 'Beaded Bracelets',
                            ],
                            [
                                'name' => 'Leather Bracelets',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Rings',
                        'children' => [
                            [
                                'name' => '	Engagement Rings',
                            ],
                            [
                                'name' => 'Wedding Bands',
                            ],
                            [
                                'name' => 'Fashion Rings',
                            ],
                            [
                                'name' => 'Stackable Rings',
                            ],
                            [
                                'name' => 'Statement Rings',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Watches',
                        'children' => [
                            [
                                'name' => 'Analog Watches',
                            ],
                            [
                                'name' => 'Digital Watches',
                            ],
                            [
                                'name' => '	Smartwatches',
                            ],
                            [
                                'name' => 'Luxury Watches',
                            ],
                            [
                                'name' => 'Sport Watches',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Brooches',
                        'children' => [
                            [
                                'name' => 'Vintage Brooches',
                            ],
                            [
                                'name' => 'Enamel Brooches',
                            ],
                            [
                                'name' => 'Decorative Brooches',
                            ],
                            [
                                'name' => 'Personalized Brooches',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Custom Jewelry',
                        'children' => [
                            [
                                'name' => 'Personalized Rings',
                            ],
                            [
                                'name' => 'Custom Necklaces',
                            ],
                            [
                                'name' => 'Engraved Bracelets',
                            ],
                            [
                                'name' => 'Bespoke Designs',
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Office Supplies',
                'children' => [
                    [
                        'name' => 'Stationery',
                        'children' => [
                            [
                                'name' => 'Notebooks',
                            ],
                            [
                                'name' => 'Planners',
                            ],
                            [
                                'name' => 'Greeting Cards',
                            ],
                            [
                                'name' => 'Envelopes',
                            ],
                            [
                                'name' => 'Pens & Pencils',
                            ],
                            [
                                'name' => 'Sticky Notes',
                            ],
                            [
                                'name' => 'Desk Calendars',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Desk Organizers',
                        'children' => [
                            [
                                'name' => 'Pen Holders',
                            ],
                            [
                                'name' => 'Paper Trays',
                            ],
                            [
                                'name' => 'Drawer Organizers',
                            ],
                            [
                                'name' => 'Desktop Caddies',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Office Furniture',
                        'children' => [
                            [
                                'name' => 'Desks',
                            ],
                            [
                                'name' => 'Chairs',
                            ],
                            [
                                'name' => 'Filing Cabinets',
                            ],
                            [
                                'name' => 'Bookcases',
                            ],
                            [
                                'name' => 'Conference Tables',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Writing Instruments',
                        'children' => [
                            [
                                'name' => 'Ballpoint Pens',
                            ],
                            [
                                'name' => 'Gel Pens',
                            ],
                            [
                                'name' => 'Fountain Pens',
                            ],
                            [
                                'name' => 'Highlighters',
                            ],
                            [
                                'name' => 'Markers',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Computer Accessories',
                        'children' => [
                            [
                                'name' => 'Keyboards',
                            ],
                            [
                                'name' => 'Mice',
                            ],
                            [
                                'name' => 'Monitors',
                            ],
                            [
                                'name' => 'Webcams',
                            ],
                            [
                                'name' => 'Laptop Stands',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Printers & Ink',
                        'children' => [
                            [
                                'name' => 'Inkjet Printers',
                            ],
                            [
                                'name' => '	Laser Printers',
                            ],
                            [
                                'name' => 'Printer Paper',
                            ],
                            [
                                'name' => 'Ink Cartridges',
                            ],
                            [
                                'name' => 'Toner Cartridges',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Office Electronics',
                        'children' => [
                            [
                                'name' => 'Shredders',
                            ],
                            [
                                'name' => 'Fax Machines',
                            ],
                            [
                                'name' => 'Laminators',
                            ],
                            [
                                'name' => 'Calculators',
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Pet Supplies',
                'children' => [
                    [
                        'name' => 'Pet Food',
                        'children' => [
                            [
                                'name' => 'Dry Food',
                            ],
                            [
                                'name' => '	Wet Food',
                            ],
                            [
                                'name' => 'Treats',
                            ],
                            [
                                'name' => 'Special Diets',
                            ],
                            [
                                'name' => 'Grain-Free Options',
                            ],
                            [
                                'name' => 'Organic Pet Food',
                            ],
                            [
                                'name' => 'Cat Food',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Toys',
                        'children' => [
                            [
                                'name' => 'Chew Toys',
                            ],
                            [
                                'name' => 'Interactive Toys',
                            ],
                            [
                                'name' => 'Balls',
                            ],
                            [
                                'name' => 'Plush Toys',
                            ],
                            [
                                'name' => 'Fetch Toys',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Beds & Furniture',
                        'children' => [
                            [
                                'name' => 'Dog Beds',
                            ],
                            [
                                'name' => 'Cat Trees',
                            ],
                            [
                                'name' => 'Pet Furniture',
                            ],
                            [
                                'name' => 'Crates',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Grooming Supplies',
                        'children' => [
                            [
                                'name' => 'Shampoos',
                            ],
                            [
                                'name' => 'Brushes',
                            ],
                            [
                                'name' => 'Nail Clippers',
                            ],
                            [
                                'name' => 'Grooming Wipes',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Health & Wellness',
                        'children' => [
                            [
                                'name' => 'Flea & Tick Treatments',
                            ],
                            [
                                'name' => 'Vitamins',
                            ],
                            [
                                'name' => 'Supplements',
                            ],
                            [
                                'name' => 'Dental Care',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Leashes & Collars',
                        'children' => [
                            [
                                'name' => 'Standard Leashes',
                            ],
                            [
                                'name' => 'Retractable Leashes',
                            ],
                            [
                                'name' => 'Collars',
                            ],
                            [
                                'name' => 'Harnesses',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Carriers & Crates',
                        'children' => [
                            [
                                'name' => 'Soft Carriers',
                            ],
                            [
                                'name' => '	Hard Crates',
                            ],
                            [
                                'name' => '	Travel Bags',
                            ],
                            [
                                'name' => '	Pet Strollers',
                            ]
                        ]
                    ]
                ]
            ],
        ];

        function retailStandardTags($data, $retail, $level) {
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
                    retailStandardTags($item['children'], $retail, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                    [
                        'L1' => $retail->id, 
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
        retailStandardTags($data, $retail, []);
    }
}
