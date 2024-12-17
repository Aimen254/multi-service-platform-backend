<?php

namespace Database\Seeders;

use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;

class HeirarchyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // L1
        $retail = StandardTag::where('slug', 'retail')->where('type', 'module')->first();

        $data = [
            [
                'name' => 'Fashion',
                'children' => [
                    [
                        'name' => 'Women',
                        'children' => [
                            [
                                'name' => 'Tops',
                            ],
                            [
                                'name' => 'Swimsuits & Cover-ups',
                            ],
                            [
                                'name' => 'Pants',
                            ],
                            [
                                'name' => 'Dressy Pant Sets',
                            ],
                            [
                                'name' => 'Loungewear',
                            ],
                            [
                                'name' => 'Jeans',
                            ],
                            [
                                'name' => 'Skirts',
                            ],
                            [
                                'name' => 'Activewear',
                            ],
                            [
                                'name' => 'Jackets & Coats',
                            ],
                            [
                                'name' => 'Sweaters',
                            ],
                            [
                                'name' => 'Leggings',
                            ],
                            [
                                'name' => 'Outdoor Wear',
                            ],
                            [
                                'name' => 'Dresses',
                            ],
                            [
                                'name' => 'Jumpsuits',
                            ],
                            [
                                'name' => 'Rompers',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Men',
                        'children' => [
                            [
                                'name' => 'Shorts',
                            ],
                            [
                                'name' => 'Pants',
                            ],
                            [
                                'name' => 'Jeans',
                            ],
                            [
                                'name' => 'Activewear',
                            ],
                            [
                                'name' => 'Dresses',
                            ],
                            [
                                'name' => 'Shirts',
                            ],
                            [
                                'name' => 'Suits',
                            ],
                            [
                                'name' => 'Blazers',
                            ],
                            [
                                'name' => 'Sportcoats',
                            ],
                            [
                                'name' => 'Underwear',
                            ],
                            [
                                'name' => 'Undershirts',
                            ],
                            [
                                'name' => 'Socks',
                            ],
                            [
                                'name' => 'Lounge',
                            ],
                            [
                                'name' => 'Pajamas',
                            ],
                            [
                                'name' => 'Robes',
                            ],
                            [
                                'name' => 'Swim Trunk',
                            ],
                            [
                                'name' => 'Gifts',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Juniors',
                        'children' => [
                            [
                                'name' => 'Tops',
                            ],
                            [
                                'name' => 'Swimsuits & Cover-ups',
                            ],
                            [
                                'name' => 'Pants',
                            ],
                            [
                                'name' => 'Dressy Pant Sets',
                            ],
                            [
                                'name' => 'Loungewear',
                            ],
                            [
                                'name' => 'Shorts',
                            ],
                            [
                                'name' => 'Jeans',
                            ],
                            [
                                'name' => 'Skirts',
                            ],
                            [
                                'name' => 'Activewear',
                            ],
                            [
                                'name' => 'Jackets & Coats',
                            ],
                            [
                                'name' => 'Sweaters',
                            ],
                            [
                                'name' => 'Homecoming',
                            ],
                            [
                                'name' => 'Leggings',
                            ],
                            [
                                'name' => 'Outdoor Wear',
                            ],
                            [
                                'name' => 'Plus & Extended Sizes',
                            ],
                            [
                                'name' => 'Dresses',
                            ],
                            [
                                'name' => 'Graphic Tees',
                            ],
                            [
                                'name' => 'Crop Tops',
                            ],
                            [
                                'name' => 'Jumpsuits',
                            ],
                            [
                                'name' => 'Rompers',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Big & Tall',
                        'children' => [
                            [
                                'name' => 'Shorts',
                            ],
                            [
                                'name' => 'Pants',
                            ],
                            [
                                'name' => 'Jeans',
                            ],
                            [
                                'name' => 'Activewear',
                            ],
                            [
                                'name' => 'Shirts',
                            ],
                            [
                                'name' => 'Outdoor Wear',
                            ],
                            [
                                'name' => 'Blazers',
                            ],
                            [
                                'name' => 'Sportcoats',
                            ],
                            [
                                'name' => 'Underwear',
                            ],
                            [
                                'name' => 'Undershirts',
                            ],
                            [
                                'name' => 'Socks',
                            ],
                            [
                                'name' => 'Pajamas',
                            ],
                            [
                                'name' => 'Robes',
                            ],
                            [
                                'name' => 'Swim Trunk',
                            ],
                            [
                                'name' => 'Surf & Skate',
                            ],
                            [
                                'name' => 'Golf Wear',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Girls',
                        'children' => [
                            [
                                'name' => 'Tops',
                            ],
                            [
                                'name' => 'Swimsuits & Cover-ups',
                            ],
                            [
                                'name' => 'Pants',
                            ],
                            [
                                'name' => 'Shorts',
                            ],
                            [
                                'name' => 'Jeans',
                            ],
                            [
                                'name' => 'Activewear',
                            ],
                            [
                                'name' => 'Leggings',
                            ],
                            [
                                'name' => 'Dresses',
                            ],
                            [
                                'name' => 'Outfits & Sets',
                            ],
                            [
                                'name' => 'Jumpsuits',
                            ],
                            [
                                'name' => 'Rompers',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Boys',
                        'children' => [
                            [
                                'name' => 'Pants',
                            ],
                            [
                                'name' => 'Shorts',
                            ],
                            [
                                'name' => 'Jeans',
                            ],
                            [
                                'name' => 'Activewear',
                            ],
                            [
                                'name' => 'Dresses',
                            ],
                            [
                                'name' => 'Graphic Tees',
                            ],
                            [
                                'name' => 'Shirts',
                            ],
                            [
                                'name' => 'Swim Trunk',
                            ],
                            [
                                'name' => 'Outfits & Sets',
                            ],
                            [
                                'name' => 'Husky',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Baby',
                        'children' => [
                            [
                                'name' => 'Girl Apparel',
                            ],
                            [
                                'name' => 'Boy Apparel',
                            ],
                            [
                                'name' => 'Blankets & Swaddies',
                            ],
                            [
                                'name' => 'Sleepwear',
                            ],
                            [
                                'name' => 'Swimwear',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Women Shoee',
                        'children' => [
                            [
                                'name' => 'Sneakers',
                            ],
                            [
                                'name' => 'Party & Evening',
                            ],
                            [
                                'name' => 'Heels',
                            ],
                            [
                                'name' => 'Wadges',
                            ],
                            [
                                'name' => 'Flats',
                            ],
                            [
                                'name' => 'Pumps',
                            ],
                            [
                                'name' => 'Mules & Slides',
                            ],
                            [
                                'name' => 'Booties',
                            ],
                            [
                                'name' => 'Espadrills',
                            ],
                            [
                                'name' => 'Loafers',
                            ],
                            [
                                'name' => 'Athletic',
                            ],
                            [
                                'name' => 'Slippers',
                            ],
                            [
                                'name' => 'Clogs',
                            ],
                            [
                                'name' => 'Wedding',
                            ],
                            [
                                'name' => 'Platform',
                            ],
                            [
                                'name' => 'Ankle Wrap',
                            ],
                            [
                                'name' => 'Western',
                            ],
                            [
                                'name' => 'Woven & Bralded',
                            ],
                            [
                                'name' => 'Boots',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Men Shoes',
                        'children' => [
                            [
                                'name' => 'Outdoor Wear',
                            ],
                            [
                                'name' => 'Dresses',
                            ],
                            [
                                'name' => 'Sandals',
                            ],
                            [
                                'name' => 'Sneakers',
                            ],
                            [
                                'name' => 'Athletic',
                            ],
                            [
                                'name' => 'Slippers',
                            ],
                            [
                                'name' => 'Casual',
                            ],
                            [
                                'name' => 'Boots',
                            ],
                            [
                                'name' => 'Waterproof',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Kids Shoes',
                        'children' => [
                            [
                                'name' => 'Youth Girls',
                            ],
                            [
                                'name' => 'Youth Boys',
                            ],
                            [
                                'name' => 'Toddler Girls',
                            ],
                            [
                                'name' => 'Toddler Boys',
                            ],
                            [
                                'name' => 'Baby Girls',
                            ],
                            [
                                'name' => 'Baby Boys',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Handbags',
                        'children' => [
                            [
                                'name' => 'Crossbody Bags',
                            ],
                            [
                                'name' => 'Backpacks',
                            ],
                            [
                                'name' => 'Totes',
                            ],
                            [
                                'name' => 'Shoulder Bags',
                            ],
                            [
                                'name' => 'Evening Bags',
                            ],
                            [
                                'name' => 'Wallets',
                            ],
                            [
                                'name' => 'Satchels',
                            ],
                            [
                                'name' => 'Hobo Bags',
                            ],
                            [
                                'name' => 'Beach Bags',
                            ],
                            [
                                'name' => 'Clutches',
                            ],
                            [
                                'name' => 'Wrislets',
                            ],
                            [
                                'name' => 'Weekenders',
                            ],
                            [
                                'name' => 'Luxury',
                            ],
                            [
                                'name' => 'Clear Handbags',
                            ],
                            [
                                'name' => 'Quilted',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Accessories',
                        'children' => [
                            [
                                'name' => 'Leggings',
                            ],
                            [
                                'name' => 'Sunglasses',
                            ],
                            [
                                'name' => 'Hats',
                            ],
                            [
                                'name' => 'Scraves & Wraps',
                            ],
                            [
                                'name' => 'Belts',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Lingerie',
                        'children' => [
                            [
                                'name' => 'Loungewear',
                            ],
                            [
                                'name' => 'Pajamas',
                            ],
                            [
                                'name' => 'Robes',
                            ],
                            [
                                'name' => 'Rompers',
                            ],
                            [
                                'name' => 'Sleepwear',
                            ],
                            [
                                'name' => 'Bras',
                            ],
                            [
                                'name' => 'Panties',
                            ],
                            [
                                'name' => 'Shapewear',
                            ],
                            [
                                'name' => 'Intimate',
                            ],
                            [
                                'name' => 'Caftans',
                            ],
                            [
                                'name' => 'Patio Dresses',
                            ],
                            [
                                'name' => 'Slips',
                            ],
                            [
                                'name' => 'Camis',
                            ],
                            [
                                'name' => 'Tights',
                            ],
                            [
                                'name' => 'Hoslery',
                            ],
                            [
                                'name' => 'Bodysuits',
                            ],
                            [
                                'name' => 'Nightgowns',
                            ],
                        ],
                    ],
                ]
            ]
        ];

        function retailStandardTags($data, $retail, $level)
        {
            foreach ($data as $item) {
                $tags = StandardTag::updateOrCreate(['slug' => Str::slug($item['name'])], [
                    'name' => $item['name'],
                    'type' => 'product',
                    'status' => 'active',
                    'priority' => 1,
                    'created_at' => now(),
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
                            'created_at' => now(),
                        ]
                    );
                    $heirarchy->standardTags()->syncWithoutDetaching($newLevel[2]);
                    $newLevel = [];
                }
            }
        }
        retailStandardTags($data, $retail, []);

        //     $retail = StandardTag::where('slug', 'retail')->first();
        //     $fashion = StandardTag::where('slug', 'fashion')->first();
        //     $women = StandardTag::where('slug', 'women')->first();
        //     $men = StandardTag::where('slug', 'men')->first();
        //     $juniors = StandardTag::where('slug', 'juniors')->first();
        //     $big_tall = StandardTag::where('slug', 'big-tall')->first();
        //     $girls = StandardTag::where('slug', 'girls')->first();
        //     $boys = StandardTag::where('slug', 'boys')->first();
        //     $baby = StandardTag::where('slug', 'baby')->first();
        //     $women_shoes = StandardTag::where('name', 'Women Shoes')->first();
        //     $men_shoes = StandardTag::where('name', 'Men Shoes')->first();
        //     $kid_shoes = StandardTag::where('name', 'Kids Shoes')->first();
        //     $handBags = StandardTag::where('slug', 'handbags')->first();
        //     $lingerie = StandardTag::where('slug', 'Lingerie')->first();

        //     // 4th level
        //     $tops = StandardTag::where('slug', 'tops')->first();
        //     $shirts = StandardTag::where('slug', 'shirts')->first();
        //     $dresses = StandardTag::where('slug', 'Dresses')->first();
        //     $skirts = StandardTag::where('slug', 'skirts')->first();
        //     $rompers = StandardTag::where('slug', 'rompers')->first();
        //     $jumpsuits = StandardTag::where('slug', 'jumpsuits')->first();
        //     $loungewear = StandardTag::where('slug', 'loungewear')->first();
        //     $accessories = StandardTag::where('slug', 'accessories')->first();
        //     $activewear = StandardTag::where('slug', 'activewear')->first();
        //     $sweaters = StandardTag::where('slug', 'sweaters')->first();
        //     $homecoming = StandardTag::where('slug', 'homecoming')->first();
        //     $jackets_coats = StandardTag::where('slug', 'jackets-coats')->first();
        //     $outdoor_wear = StandardTag::where('slug', 'outdoor-wear')->first();
        //     $crop_tops = StandardTag::where('slug', 'crop-tops')->first();
        //     $swimsuit = StandardTag::where('slug', 'swimsuits-cover-ups')->first();
        //     $graphic_tees = StandardTag::where('slug', 'graphic-tees')->first();
        //     $plus_extended_sizes = StandardTag::where('slug', 'plus-extended-sizes')->first();
        //     $pants = StandardTag::where('slug', 'pants')->first();
        //     $shoes = StandardTag::where('slug', 'shoes')->first();
        //     $drespaints = StandardTag::where('slug', 'dressy-pant-sets')->first();
        //     $shorts = StandardTag::where('slug', 'shorts')->first();
        //     $jeans = StandardTag::where('slug', 'jeans')->first();
        //     $leggings = StandardTag::where('slug', 'leggings')->first();
        //     $blazers = StandardTag::where('slug', 'blazers')->first();
        //     $sportcoats = StandardTag::where('slug', 'sportcoats')->first();
        //     $underwear = StandardTag::where('slug', 'underwear')->first();
        //     $undershirts = StandardTag::where('slug', 'undershirts')->first();
        //     $socks = StandardTag::where('slug', 'socks')->first();
        //     $lounge = StandardTag::where('slug', 'lounge')->first();
        //     $pajamas = StandardTag::where('slug', 'pajamas')->first();
        //     $robes = StandardTag::where('slug', 'robes')->first();
        //     $swim_trunk = StandardTag::where('slug', 'swim-trunk')->first();
        //     $suits = StandardTag::where('slug', 'suits')->first();
        //     $gifts = StandardTag::where('slug', 'gifts')->first();
        //     $golf_wear = StandardTag::where('slug', 'golf-wear')->first();
        //     $surf_skate = StandardTag::where('name', 'Surf & Skate')->first();
        //     $outfits_sets = StandardTag::where('name', 'Outfits & Sets')->first();
        //     $sandals = StandardTag::where('name', 'Sandals')->first();
        //     $sneakers = StandardTag::where('name', 'Sneakers')->first();
        //     $party_evening = StandardTag::where('name', 'Party & Evening')->first();
        //     $heels = StandardTag::where('name', 'Heels')->first();
        //     $wedges = StandardTag::where('name', 'Wedges')->first();
        //     $flats = StandardTag::where('name', 'Flats')->first();
        //     $pumps = StandardTag::where('name', 'Pumps')->first();
        //     $mules_slides = StandardTag::where('name', 'Mules & Slides')->first();
        //     $boots = StandardTag::where('name', 'Boots')->first();
        //     $booties = StandardTag::where('name', 'Booties')->first();
        //     $espadrilles = StandardTag::where('name', 'Espadrilles')->first();
        //     $loafers = StandardTag::where('name', 'Loafers')->first();
        //     $athletic = StandardTag::where('name', 'Athletic')->first();
        //     $slippers = StandardTag::where('name', 'Slippers')->first();
        //     $clogs = StandardTag::where('name', 'Clogs')->first();
        //     $wedding = StandardTag::where('name', 'Wedding')->first();
        //     $platform = StandardTag::where('name', 'Platform')->first();
        //     $ankle_wrap = StandardTag::where('name', 'Ankle Wrap')->first();
        //     $western = StandardTag::where('name', 'Western')->first();
        //     $woven_braided = StandardTag::where('name', 'Woven & Braided')->first();
        //     $husky = StandardTag::where('name', 'Husky')->first();
        //     $boyApparel = StandardTag::where('name', 'Boy Apparel')->first();
        //     $girlApparel = StandardTag::where('name', 'Girl Apparel')->first();
        //     $blankets = StandardTag::where('name', 'Blankets & Swaddles')->first();
        //     $sleepwear = StandardTag::where('name', 'Sleepwear')->first();
        //     $swimwear = StandardTag::where('name', 'Swimwear')->first();
        //     $casual = StandardTag::where('name', 'Casual')->first();
        //     $waterproof = StandardTag::where('name', 'Waterproof')->first();
        //     $youthGirls = StandardTag::where('name', 'Youth Girls')->first();
        //     $youthBoys = StandardTag::where('name', 'Youth Boys')->first();
        //     $toddlerBoys = StandardTag::where('name', 'Toddler Boys')->first();
        //     $toddlerGirls = StandardTag::where('name', 'Toddler Girls')->first();
        //     $babyGirls = StandardTag::where('name', 'Baby Girls')->first();
        //     $babyBoys = StandardTag::where('name', 'Baby Boys')->first();
        //     $crossbodyBags = StandardTag::where('name', 'Crossbody Bags')->first();
        //     $backpacks = StandardTag::where('name', 'Backpacks')->first();
        //     $totes = StandardTag::where('name', 'Totes')->first();
        //     $shoulderBags = StandardTag::where('name', 'Shoulder Bags')->first();
        //     $eveningBags = StandardTag::where('name', 'Evening Bags')->first();
        //     $wallets = StandardTag::where('name', 'Wallets')->first();
        //     $satchels = StandardTag::where('name', 'Satchels')->first();
        //     $hoboBags = StandardTag::where('name', 'Hobo Bags')->first();
        //     $beachBags = StandardTag::where('name', 'Beach Bags')->first();
        //     $clutches = StandardTag::where('name', 'Clutches')->first();
        //     $wrislets = StandardTag::where('name', 'Wrislets')->first();
        //     $weekenders = StandardTag::where('name', 'Weekenders')->first();
        //     $luxury = StandardTag::where('name', 'Luxury')->first();
        //     $clearHandbags = StandardTag::where('name', 'Clear Handbags')->first();
        //     $quilted = StandardTag::where('name', 'Quilted')->first();
        //     $sunglasses = StandardTag::where('name', 'Sunglasses')->first();
        //     $hats = StandardTag::where('name', 'Hats')->first();
        //     $scraves = StandardTag::where('name', 'Scraves & wraps')->first();
        //     $belts = StandardTag::where('name', 'Belts')->first();
        //     $bras = StandardTag::where('name', 'Bras')->first();
        //     $panties = StandardTag::where('name', 'panties')->first();
        //     $shapewear = StandardTag::where('name', 'Shapewear')->first();
        //     $intimate = StandardTag::where('name', 'Intimate')->first();
        //     $caftans = StandardTag::where('name', 'Caftans')->first();
        //     $patioDresses = StandardTag::where('name', 'Patio Dresses')->first();
        //     $slips = StandardTag::where('name', 'slips')->first();
        //     $camis = StandardTag::where('name', 'camis')->first();
        //     $tights = StandardTag::where('name', 'Tights')->first();
        //     $hosiery = StandardTag::where('name', 'Hosiery')->first();
        //     $bodysuits = StandardTag::where('name', 'Bodysuits')->first();
        //     $nightgowns = StandardTag::where('name', 'Nightgowns')->first();

        //     $elecltrincs = StandardTag::where('name', 'Electronics')->first();
        //     $gaming = StandardTag::where('name', 'Gaming')->first();
        //     $consoles = StandardTag::where('name', 'Consoles')->first();

        //     $hobby_toys = StandardTag::where('slug', 'hobby-toys')->first();
        //     $games_puzzles = StandardTag::where('slug', 'games-puzzles')->first();
        //     $games = StandardTag::where('slug', 'games')->first();

        //     $retailHierarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'level_type' => 2,
        //         'is_multiple' => 1
        //     ]);
        //     $womenids = [
        //         $dresses->id,
        //         $jumpsuits->id,
        //         $loungewear->id,
        //         $rompers->id,
        //         $skirts->id,
        //         $tops->id,
        //         $swimsuit->id,
        //         $pants->id,
        //         $jeans->id,
        //         $leggings->id,
        //         $drespaints->id,
        //         $activewear->id,
        //         $jackets_coats->id,
        //         $sweaters->id,
        //         $outdoor_wear->id
        //     ];

        //     $retailHierarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'level_type' => 3,
        //         'is_multiple' => 1
        //     ]);
        //     $womenHeirarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $women->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);


        //     $womenHeirarchy->standardTags()->sync($womenids);

        //     $juniorsHeirarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $juniors->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $junirosIds = [
        //         $dresses->id,
        //         $jumpsuits->id,
        //         $loungewear->id,
        //         $rompers->id,
        //         $skirts->id,
        //         $tops->id,
        //         $swimsuit->id,
        //         $pants->id,
        //         $jeans->id,
        //         $leggings->id,
        //         $drespaints->id,
        //         $activewear->id,
        //         $jackets_coats->id,
        //         $sweaters->id,
        //         $outdoor_wear->id,
        //         $shorts->id,
        //         $plus_extended_sizes->id,
        //         $homecoming->id,
        //         $graphic_tees->id,
        //         $crop_tops->id
        //     ];

        //     $juniorsHeirarchy->standardTags()->sync($junirosIds);

        //     $mensHeirarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $men->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $mensIds = [
        //         $shirts->id,
        //         $jeans->id,
        //         $pants->id,
        //         $activewear->id,
        //         $dresses->id,
        //         $suits->id,
        //         $blazers->id,
        //         $sportcoats->id,
        //         $underwear->id,
        //         $undershirts->id,
        //         $socks->id,
        //         $lounge->id,
        //         $pajamas->id,
        //         $robes->id,
        //         $shorts->id,
        //         $swim_trunk->id,
        //         $gifts->id,
        //     ];

        //     $mensHeirarchy->standardTags()->sync($mensIds);


        //     $bigTallHeirarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $big_tall->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $bigTallIds = [
        //         $shirts->id,
        //         $jeans->id,
        //         $shorts->id,
        //         $pants->id,
        //         $activewear->id,
        //         $blazers->id,
        //         $sportcoats->id,
        //         $underwear->id,
        //         $undershirts->id,
        //         $socks->id,
        //         $pajamas->id,
        //         $robes->id,
        //         $swim_trunk->id,
        //         $golf_wear->id,
        //         $outdoor_wear->id,
        //         $surf_skate ->id
        //     ];

        //     $bigTallHeirarchy->standardTags()->sync($bigTallIds);


        //     $girlsHeirarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $girls->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $girlsIds = [
        //         $dresses->id,
        //         $tops->id,
        //         $shorts->id,
        //         $outfits_sets->id,
        //         $pants->id,
        //         $leggings->id,
        //         $jumpsuits->id,
        //         $rompers->id,
        //         $activewear->id,
        //         $swimsuit->id,
        //     ];

        //     $girlsHeirarchy->standardTags()->sync($girlsIds);


        //     $boysHeirarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $boys->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $boysIds = [
        //         $dresses->id,
        //         $shirts->id,
        //         $graphic_tees->id,
        //         $shorts->id,
        //         $pants->id,
        //         $outfits_sets->id,
        //         $activewear->id,
        //         $husky->id,
        //         $swim_trunk->id,
        //     ];

        //     $boysHeirarchy->standardTags()->sync($boysIds);


        //     $womenShoesHeirarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $women_shoes->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $womenShoesIds = [
        //         $sneakers->id,
        //         $party_evening->id,
        //         $heels->id,
        //         $wedges->id,
        //         $flats->id,
        //         $pumps->id,
        //         $mules_slides->id,
        //         $boots->id,
        //         $booties->id,
        //         $espadrilles->id,
        //         $loafers->id,
        //         $athletic->id,
        //         $slippers->id,
        //         $clogs->id,
        //         $wedding->id,
        //         $platform->id,
        //         $ankle_wrap->id,
        //         $western->id,
        //         $woven_braided->id,
        //     ];

        //     $womenShoesHeirarchy->standardTags()->sync($womenShoesIds);


        //     $babyHeirarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $baby->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $babyIds = [
        //         $boyApparel->id,
        //         $girlApparel->id,
        //         $blankets->id,
        //         $sleepwear->id,
        //         $swimwear->id,
        //     ];

        //     $babyHeirarchy->standardTags()->sync($babyIds);


        //     $menShoesHeirarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $men_shoes->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $menShoesIds = [
        //         $casual->id,
        //         $sandals->id,
        //         $dresses->id,
        //         $sneakers->id,
        //         $slippers->id,
        //         $athletic->id,
        //         $boots->id,
        //         $waterproof->id,
        //         $outdoor_wear->id,
        //     ];

        //     $menShoesHeirarchy->standardTags()->sync($menShoesIds);


        //     $kidShoesHeirarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $kid_shoes->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $kidShoesIds = [
        //         $youthGirls->id,
        //         $youthBoys->id,
        //         $toddlerBoys->id,
        //         $toddlerGirls->id,
        //         $babyGirls->id,
        //         $babyBoys->id,
        //     ];

        //     $kidShoesHeirarchy->standardTags()->sync($kidShoesIds);

        //     $handbagHierarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $handBags->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $handbagIds = [
        //         $crossbodyBags->id,
        //         $backpacks->id,
        //         $totes->id,
        //         $shoulderBags->id,
        //         $eveningBags->id,
        //         $wallets->id,
        //         $satchels->id,
        //         $hoboBags->id,
        //         $beachBags->id,
        //         $clutches->id,
        //         $wrislets->id,
        //         $weekenders->id,
        //         $luxury->id,
        //         $clearHandbags->id,
        //         $quilted->id,
        //     ];

        //     $handbagHierarchy->standardTags()->sync($handbagIds);


        //     $accessoriesHierarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $accessories->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $accessoriesIds = [
        //         $sunglasses->id,
        //         $hats->id,
        //         $leggings->id,
        //         $scraves->id,
        //         $belts->id
        //     ];

        //     $accessoriesHierarchy->standardTags()->sync($accessoriesIds);


        //     $lingerieHierarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $fashion->id,
        //         'L3' => $lingerie->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        //     ]);

        //     $lingerieIds = [
        //         $bras->id,
        //         $pajamas->id,
        //         $sleepwear->id,
        //         $panties->id,
        //         $shapewear->id,
        //         $intimate->id,
        //         $caftans->id,
        //         $patioDresses->id,
        //         $loungewear->id,
        //         $robes->id,
        //         $slips->id,
        //         $camis->id,
        //         $tights->id,
        //         $hosiery->id,
        //         $rompers->id,
        //         $bodysuits->id,
        //         $nightgowns->id,
        //     ];

        //     $lingerieHierarchy->standardTags()->sync($lingerieIds);

        //     $electronicIds = [$consoles->id];
        //     // $retailHierarchy = TagHierarchy::create([
        //     //     'L1' => $retail->id,
        //     //     'L2' => $elecltrincs->id,
        //     //     'level_type' => 3,
        //     //     'is_multiple' => 1
        //     // ]);

        //     $elecltrincsHierarchy = TagHierarchy::create([
        //             'L1' => $retail->id,
        //             'L2' => $elecltrincs->id,
        //             'L3' => $gaming->id,
        //             'level_type' => 4,
        //             'is_multiple' => 1
        //     ]);

        //     $elecltrincsHierarchy->standardTags()->sync($electronicIds);
        //     // $retailHierarchy = TagHierarchy::create([
        //     //     'L1' => $retail->id,
        //     //     'L2' => $hobby_toys->id,
        //     //     'level_type' => 3,
        //     //     'is_multiple' => 1
        //     // ]);

        //     $hobbyAndToysHierarchy = TagHierarchy::create([
        //         'L1' => $retail->id,
        //         'L2' => $hobby_toys->id,
        //         'L3' => $games_puzzles->id,
        //         'level_type' => 4,
        //         'is_multiple' => 1
        // ]);

        // $hobbyAndToysHierarchy->standardTags()->sync([$games->id]);

    }
}
