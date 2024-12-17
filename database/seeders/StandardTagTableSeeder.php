<?php

namespace Database\Seeders;

use App\Models\GlobalTag;
use App\Models\StandardTag;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class StandardTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modulesTags = [
            [
                'name' => 'Retail',
                'slug' => Str::slug('Retail'),
                'type' => 'module',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        StandardTag::insert($modulesTags);

        $retailTag = StandardTag::where('name', 'Retail')->first();
        $autoFashionStoreTag = StandardTag::where('name', 'Fashion')->first();
        // $autoMotiveStoreTag = GlobalTag::where('name', 'Automotive')->first();
        // $beautyStoreTag = GlobalTag::where('name', 'Beauty & Cosmetics')->first();
        // $artworksStoreTag = GlobalTag::where('name', 'Artworks')->first();
        // $bicycleStoreTag = GlobalTag::where('name', 'Bicycling')->first();
        // $coinsStoreTag = GlobalTag::where('name', 'Coins & Collectibles')->first();
        // $electronicsStoreTag = GlobalTag::where('name', 'Electronics')->first();
        // $healthStoreTag = GlobalTag::where('name', 'Health & Wellness')->first();
        // $sportingStoreTag = GlobalTag::where('name', 'Sporting Goods')->first();
        // $hobbyStoreTag = GlobalTag::where('name', 'Hobby & Toys')->first();
        // $homeStoreTag = GlobalTag::where('name', 'Home & Kitchen')->first();
        // $outdoorStoreTag = GlobalTag::where('name', 'Outdoor Living')->first();
        $tags = [
            [
                'name' => 'Fashion',
                'slug' => Str::slug('Fashion'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Women',
                'slug' => Str::slug('Women'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Juniors',
                'slug' => Str::slug('Juniors'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Men',
                'slug' => Str::slug('Men'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Big & Tall',
                'slug' => Str::slug('Big & Tall'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Girls',
                'slug' => Str::slug('Girls'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Boys',
                'slug' => Str::slug('Boys'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Baby',
                'slug' => Str::slug('Baby'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Shoes',
                'slug' => Str::slug('Shoes'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Women Shoes',
                'slug' => Str::slug('Women Shoes'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Men Shoes',
                'slug' => Str::slug('Men Shoes'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Kids Shoes',
                'slug' => Str::slug('Kids Shoes'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Youth Girls',
                'slug' => Str::slug('Youth Girls'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Youth Boys',
                'slug' => Str::slug('Youth Boys'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Handbags',
                'slug' => Str::slug('Handbags'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Accessories',
                'slug' => Str::slug('Accessories'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Lingerie',
                'slug' => Str::slug('Lingerie'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Materinty',
                'slug' => Str::slug('Materinty'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Tops',
                'slug' => Str::slug('Tops'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bridal',
                'slug' => Str::slug('Bridal'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [   
                'name' => 'Swimsuits & Cover-ups',
                'slug' => Str::slug('Swimsuits & Cover-ups'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pants',
                'slug' => Str::slug('Pants'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Dressy pant sets',
                'slug' => Str::slug('Dressy pant sets'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Loungewear',
                'slug' => Str::slug('Loungewear'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Shorts',
                'slug' => Str::slug('Shorts'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Jeans',
                'slug' => Str::slug('Jeans'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Skirts',
                'slug' => Str::slug('Skirts'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Activewear',
                'slug' => Str::slug('Activewear'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Jackets & Coats',
                'slug' => Str::slug('Jackets & Coats'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sweaters',
                'slug' => Str::slug('Sweaters'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Homecoming',
                'slug' => Str::slug('homecoming'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Leggings',
                'slug' => Str::slug('Leggings'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Outdoor Wear',
                'slug' => Str::slug('Outdoor Wear'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Plus & Extended Sizes',
                'slug' => Str::slug('Plus & Extended Sizes'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Dresses',
                'slug' => Str::slug('Dresses'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Graphic Tees',
                'slug' => Str::slug('Graphic Tees'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Crop tops',
                'slug' => Str::slug('Crop tops'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'shirts',
                'slug' => Str::slug('shirts'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Suits',
                'slug' => Str::slug('Suits'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Blazers',
                'slug' => Str::slug('Blazers'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sportcoats',
                'slug' => Str::slug('Sportcoats'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Underwear',
                'slug' => Str::slug('Underwear'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Undershirts',
                'slug' => Str::slug('Undershirts'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Socks',
                'slug' => Str::slug('Socks'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Golf Wear',
                'slug' => Str::slug('Golf Wear'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Lounge',
                'slug' => Str::slug('Lounge'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pajamas',
                'slug' => Str::slug('Pajamas'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Robes',
                'slug' => Str::slug('Robes'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Swim trunk',
                'slug' => Str::slug('Swim trunk'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Gifts',
                'slug' => Str::slug('Gifts'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Outfits & Sets',
                'slug' => Str::slug('Outfits & Sets'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pants & Leggins',
                'slug' => Str::slug('Pants & Leggins'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Jumpsuits',
                'slug' => Str::slug('Jumpsuits'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Rompers',
                'slug' => Str::slug('Rompers'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Husky',
                'slug' => Str::slug('Husky'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Girl Apparel',
                'slug' => Str::slug('Girl Apparel'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Boy Apparel',
                'slug' => Str::slug('Boy Apparel'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Blankets & Swaddles',
                'slug' => Str::slug('Blankets & Swaddles'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sleepwear',
                'slug' => Str::slug('Sleepwear'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Swimwear',
                'slug' => Str::slug('Swimwear'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sandals',
                'slug' => Str::slug('Sandals'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sneakers',
                'slug' => Str::slug('Sneakers'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Party & Evening',
                'slug' => Str::slug('Party & Evening'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Heels',
                'slug' => Str::slug('Heels'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Wedges',
                'slug' => Str::slug('Wedges'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Flats',
                'slug' => Str::slug('Flats'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pumps',
                'slug' => Str::slug('Pumps'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Mules & Slides',
                'slug' => Str::slug('Mules & Slides'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Booties',
                'slug' => Str::slug('Booties'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Espadrilles',
                'slug' => Str::slug('Espadrilles'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Loafers',
                'slug' => Str::slug('Loafers'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Athletic',
                'slug' => Str::slug('Athletic'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Slippers',
                'slug' => Str::slug('Slippers'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Clogs',
                'slug' => Str::slug('Clogs'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Wedding',
                'slug' => Str::slug('Wedding'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Platform',
                'slug' => Str::slug('Platform'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Ankle wrap',
                'slug' => Str::slug('Ankle wrap'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Western',
                'slug' => Str::slug('Western'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Woven & Braided',
                'slug' => Str::slug('Woven & Braided'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Casual',
                'slug' => Str::slug('Casual'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Boots',
                'slug' => Str::slug('Boots'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Waterproof',
                'slug' => Str::slug('Waterproof'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Toddlers',
                'slug' => Str::slug('Toddlers'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Toddler Boys',
                'slug' => Str::slug('Toddler Boys'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Baby Boys',
                'slug' => Str::slug('Baby Boys'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Baby Girls',
                'slug' => Str::slug('Baby Girls'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Toddler Girls',
                'slug' => Str::slug('Toddler Girls'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Crossbody Bags',
                'slug' => Str::slug('Crossbody Bags'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Backpacks',
                'slug' => Str::slug('Backpacks'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Totes',
                'slug' => Str::slug('Totes'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Shoulder bags',
                'slug' => Str::slug('Shoulder bags'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Evening bags',
                'slug' => Str::slug('Evening bags'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Wallets',
                'slug' => Str::slug('Wallets'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Satchels',
                'slug' => Str::slug('Satchels'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Hobo bags',
                'slug' => Str::slug('Hobo bags'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Beach bags',
                'slug' => Str::slug('Beach bags'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Clutches',
                'slug' => Str::slug('Clutches'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Wrislets',
                'slug' => Str::slug('Wrislets'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Weekenders',
                'slug' => Str::slug('Weekenders'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Luxury',
                'slug' => Str::slug('Luxury'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Clear handbags',
                'slug' => Str::slug('Clear handbags'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Quilted',
                'slug' => Str::slug('Quilted'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sunglasses',
                'slug' => Str::slug('Sunglasses'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Hats',
                'slug' => Str::slug('Hats'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Scraves & wraps',
                'slug' => Str::slug('Scraves & wraps'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Belts',
                'slug' => Str::slug('Belts'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Surf & Skate',
                'slug' => Str::slug('Surf & Skate'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bras',
                'slug' => Str::slug('Bras'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'panties',
                'slug' => Str::slug('panties'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Shapewear',
                'slug' => Str::slug('Shapewear'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Intimate',
                'slug' => Str::slug('Intimate'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Caftans',
                'slug' => Str::slug('Caftans'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Patio Dresses',
                'slug' => Str::slug('Patio Dresses'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Slips',
                'slug' => Str::slug('Slips'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Camis',
                'slug' => Str::slug('Camis'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Tights',
                'slug' => Str::slug('Tights'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Hosiery',
                'slug' => Str::slug('Hosiery'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bodysuits',
                'slug' => Str::slug('Bodysuits'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Nightgowns',
                'slug' => Str::slug('Nightgowns'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Electronics',
                'slug' => Str::slug('Electronics'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Gaming',
                'slug' => Str::slug('Gaming'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Consoles',
                'slug' => Str::slug('Consoles'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Hobby & Toys',
                'slug' => Str::slug('Hobby & Toys'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Games & Puzzles',
                'slug' => Str::slug('Games & Puzzles'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Games',
                'slug' => Str::slug('Games'),
                'type' => 'product',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $autoMotiveStoreTag->id,
            //     'name' => 'Photography',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $autoMotiveStoreTag->id,
            //     'name' => 'Scupltures',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $autoMotiveStoreTag->id,
            //     'name' => 'In-Car Technology',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $autoMotiveStoreTag->id,
            //     'name' => 'Manuals and Literature',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $autoMotiveStoreTag->id,
            //     'name' => 'RVs & Campers',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $autoMotiveStoreTag->id,
            //     'name' => 'Salvage Parts',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $autoMotiveStoreTag->id,
            //     'name' => 'Trailers',
            //     'type' => 'product'
            // ],

            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $beautyStoreTag->id,
            //     'name' => 'Fragrances',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $beautyStoreTag->id,
            //     'name' => 'Makeup',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $beautyStoreTag->id,
            //     'name' => 'Skincare',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $beautyStoreTag->id,
            //     'name' => 'Bath & Body',
            //     'type' => 'product'
            // ],

            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $bicycleStoreTag->id,
            //     'name' => 'Bikes',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $bicycleStoreTag->id,
            //     'name' => 'Parts',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $bicycleStoreTag->id,
            //     'name' => 'Wheels',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $bicycleStoreTag->id,
            //     'name' => 'Tires-Tubes',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $bicycleStoreTag->id,
            //     'name' => 'Clothing',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $bicycleStoreTag->id,
            //     'name' => 'Helmets',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $bicycleStoreTag->id,
            //     'name' => 'Shoes',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $bicycleStoreTag->id,
            //     'name' => 'Car Racks',
            //     'type' => 'product'
            // ],

            
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $coinsStoreTag->id,
            //     'name' => 'Collectibles',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $coinsStoreTag->id,
            //     'name' => 'Collectibles',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $coinsStoreTag->id,
            //     'name' => 'Coins & Paper Money',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $coinsStoreTag->id,
            //     'name' => 'Antiques',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $coinsStoreTag->id,
            //     'name' => 'Art & Craft Supplies',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $coinsStoreTag->id,
            //     'name' => 'Dolls & Teddy Bears',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $coinsStoreTag->id,
            //     'name' => 'Pottery & Glass',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $coinsStoreTag->id,
            //     'name' => 'Art',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $coinsStoreTag->id,
            //     'name' => 'Entertainment Memorabilia',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $coinsStoreTag->id,
            //     'name' => 'Stamps',
            //     'type' => 'product'
            // ],

            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $electronicsStoreTag->id,
            //     'name' => 'Appliances',
            //     'type' => 'product'
            // ],
            
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $electronicsStoreTag->id,
            //     'name' => 'TV & Home Theater',
            //     'type' => 'product'
            // ],
            
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $electronicsStoreTag->id,
            //     'name' => 'Computers & Tablets',
            //     'type' => 'product'
            // ],
            
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $electronicsStoreTag->id,
            //     'name' => 'Cameras, Camcorders, & Drones',
            //     'type' => 'product'
            // ],
            
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $electronicsStoreTag->id,
            //     'name' => 'Audio',
            //     'type' => 'product'
            // ],
            
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $electronicsStoreTag->id,
            //     'name' => 'Video Games',
            //     'type' => 'product'
            // ],
            
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $electronicsStoreTag->id,
            //     'name' => 'Game Room',
            //     'type' => 'product'
            // ],
            
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $electronicsStoreTag->id,
            //     'name' => 'Car Electronics & GPS',
            //     'type' => 'product'
            // ],
            
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $electronicsStoreTag->id,
            //     'name' => 'Marine & Powersports',
            //     'type' => 'product'
            // ],
            
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $electronicsStoreTag->id,
            //     'name' => 'Wearable Technology',
            //     'type' => 'product'
            // ],
            
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $healthStoreTag->id,
            //     'name' => 'Health & Monitors',
            //     'type' => 'product'
            // ],
           
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $healthStoreTag->id,
            //     'name' => 'Personal Care & Beauty',
            //     'type' => 'product'
            // ],
           
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $healthStoreTag->id,
            //     'name' => 'Shaving & Hair Removal',
            //     'type' => 'product'
            // ],
           
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $healthStoreTag->id,
            //     'name' => 'Sleep Solutions',
            //     'type' => 'product'
            // ],
           
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $healthStoreTag->id,
            //     'name' => 'Smartwatches',
            //     'type' => 'product'
            // ],
           
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $healthStoreTag->id,
            //     'name' => 'Baby Care',
            //     'type' => 'product'
            // ],
           
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $sportingStoreTag->id,
            //     'name' => 'Electric Transporation',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $sportingStoreTag->id,
            //     'name' => 'Exercise & Fitness',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $sportingStoreTag->id,
            //     'name' => 'Golf Equipment',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $sportingStoreTag->id,
            //     'name' => 'Outdoor Recreation',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $sportingStoreTag->id,
            //     'name' => 'Swimming Pools',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $sportingStoreTag->id,
            //     'name' => 'Workout Recovery',
            //     'type' => 'product'
            // ],

            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $hobbyStoreTag->id,
            //     'name' => 'STEM & Educational Toys',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $hobbyStoreTag->id,
            //     'name' => 'Games & Puzzles',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $hobbyStoreTag->id,
            //     'name' => 'Electric Transporation',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $hobbyStoreTag->id,
            //     'name' => 'Characters',
            //     'type' => 'product'
            // ],

            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $homeStoreTag->id,
            //     'name' => 'Bedding',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $homeStoreTag->id,
            //     'name' => 'Kitchen & Appliances',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $homeStoreTag->id,
            //     'name' => 'Bath & Towels',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $homeStoreTag->id,
            //     'name' => 'Home Decor',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $homeStoreTag->id,
            //     'name' => 'Candles & Home Fragrance',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $homeStoreTag->id,
            //     'name' => 'Dining & Hosting',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Patio Furniture',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Grills & Outdoor Kitchens',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Outdoor Heating',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Outdoor Home Theater',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Outdoor Lighting',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Lawn & Garden',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Luggage & Travel',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Health & Fitness',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Pets',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Holiday Decor',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Vacuums & Floorcare',
            //     'type' => 'product'
            // ],
            // [
            //     'parent_id' => NULL,
            //     'global_tag_id' => $outdoorStoreTag->id,
            //     'name' => 'Furniture',
            //     'type' => 'product'
            // ],
            
        ];

        StandardTag::insert($tags);

        // $painting = StandardTag::where('name', 'Paintings')->first();
        // $accessories = StandardTag::where('name', 'Parts & Accessories')->first();
        // $fragrance = StandardTag::where('name', 'Fragrances')->first();
        // $makeup = StandardTag::where('name', 'Makeup')->first();
        // $skincare = StandardTag::where('name', 'Skincare')->first();
        // $bathAndBody = StandardTag::where('name', 'Bath & Body')->first();
        // $bikes = StandardTag::where('name', 'Bikes')->first();
        // $women = StandardTag::where('name', 'Women')->first();
        // $juniors = StandardTag::where('name', 'Juniors')->first();
        // $men = StandardTag::where('name', 'Men')->first();
        // $bigAndTall = StandardTag::where('name', 'Big & Tall')->first();
        // $girls = StandardTag::where('name', 'Girls')->first();
        // $boys = StandardTag::where('name', 'Boys')->first();
        // $baby = StandardTag::where('name', 'Baby')->first();
        // $womenShoes = StandardTag::where('name', 'Shoes')->first();
        // $menShoes = StandardTag::where('name', 'Shoes')->first();
        // $kidShoes = StandardTag::where('name', 'Shoes')->first();
        // $handbags = StandardTag::where('name', 'Handbags')->first();
        // $accessories = StandardTag::where('name', 'Accessories')->first();
        // $lingerie = StandardTag::where('name', 'Lingerie')->first();
        // $collectibles = StandardTag::where('name', 'Collectibles')->first();
        // $appliances = StandardTag::where('name', 'Appliances')->first();
        // $tvAndHome = StandardTag::where('name', 'TV & Home Theater')->first();
        // $computerAndTablets = StandardTag::where('name', 'Computers & Tablets')->first();
        // $cameras = StandardTag::where('name', 'Cameras, Camcorders, & Drones')->first();
        // $audio = StandardTag::where('name', 'Audio')->first();
        // $videoGames = StandardTag::where('name', 'Video Games')->first();
        // $gameRoom = StandardTag::where('name', 'Game Room')->first();
        // $carElectronics = StandardTag::where('name', 'Car Electronics & GPS')->first();
        // $wearable = StandardTag::where('name', 'Wearable Technology')->first();
        // $healthTechnology = StandardTag::where('name', 'Health & Monitors')->first();
        // $personalCare = StandardTag::where('name', 'Personal Care & Beauty')->first();
        // $shavingHair = StandardTag::where('name', 'Shaving & Hair Removal')->first();
        // $sleepSolutions = StandardTag::where('name', 'Sleep Solutions')->first();
        // $smartwatches = StandardTag::where('name', 'Smartwatches')->first();
        // $babyCare = StandardTag::where('name', 'Baby Care')->first();
        // $electronicTransprtation = StandardTag::where('name', 'Electric Transporation')->first();
        // $excercise = StandardTag::where('name', 'Exercise & Fitness')->first();
        // $outdoor = StandardTag::where('name', 'Outdoor Recreation')->first();
        // $swimmingPool = StandardTag::where('name', 'Swimming Pools')->first();
        // $workoutRecovery = StandardTag::where('name', 'Workout Recovery')->first();
        // $STEM = StandardTag::where('name', 'STEM & Educational Toys')->first();
        // $games = StandardTag::where('name', 'Games & Puzzles')->first();
        // // $businessWomenTags = StandardTag::where('name', 'Electric Transporation')->first();
        // $characters = StandardTag::where('name', 'Characters')->first();
        // $bedding = StandardTag::where('name', 'Bedding')->first();
        // $kitchenAndAppliances = StandardTag::where('name', 'Kitchen & Appliances')->first();
        // $bathAndTowels = StandardTag::where('name', 'Bath & Towels')->first();
        // $homeDecor = StandardTag::where('name', 'Home Decor')->first();
        // $candlesAndHomeFragrance = StandardTag::where('name', 'Candles & Home Fragrance')->first();
        // $diningAndHosting = StandardTag::where('name', 'Dining & Hosting')->first();
        // $patioFurniture = StandardTag::where('name', 'Patio Furniture')->first();
        // $grills = StandardTag::where('name', 'Grills & Outdoor Kitchens')->first();
        // $outdoorHeating = StandardTag::where('name', 'Outdoor Heating')->first();
        // $outdoorHomeTheater = StandardTag::where('name', 'Outdoor Home Theater')->first();
        // $outdoorLighting = StandardTag::where('name', 'Outdoor Lighting')->first();
        // $lawnAndGarden = StandardTag::where('name', 'Lawn & Garden')->first();
        // $luggageAndTravel = StandardTag::where('name', 'Luggage & Travel')->first();
        // $healthAndFitness = StandardTag::where('name', 'Health & Fitness')->first();
        // $pets = StandardTag::where('name', 'Pets')->first();
        // $holidayDecor = StandardTag::where('name', 'Holiday Decor')->first();
        // $vaccums = StandardTag::where('name', 'Vacuums & Floorcare')->first();
        // $furniture = StandardTag::where('name', 'Furniture')->first();
        
        $tagsLevelTwo =[
                // [
                //     'parent_id' => $painting->id,
                //     'global_tag_id' => $artworksStoreTag->id,
                //     'name' => 'Abstract',
                //     'slug' => Str::slug('Abstract'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $painting->id,
                //     'global_tag_id' => $artworksStoreTag->id,
                //     'name' => 'Figurative',
                //     'slug' => Str::slug('Figurative'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $painting->id,
                //     'global_tag_id' => $artworksStoreTag->id,
                //     'name' => 'Geometric',
                //     'slug' => Str::slug('Geometric'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $painting->id,
                //     'global_tag_id' => $artworksStoreTag->id,
                //     'name' => 'Minimalist',
                //     'slug' => Str::slug('Minimalist'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $painting->id,
                //     'global_tag_id' => $artworksStoreTag->id,
                //     'name' => 'Nature',
                //     'slug' => Str::slug('Nature'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $painting->id,
                //     'global_tag_id' => $artworksStoreTag->id,
                //     'name' => 'Pop',
                //     'slug' => Str::slug('Pop'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $painting->id,
                //     'global_tag_id' => $artworksStoreTag->id,
                //     'name' => 'Portraiture',
                //     'slug' => Str::slug('Portraiture'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $painting->id,
                //     'global_tag_id' => $artworksStoreTag->id,
                //     'name' => 'Still Life',
                //     'slug' => Str::slug('Still Life'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $painting->id,
                //     'global_tag_id' => $artworksStoreTag->id,
                //     'name' => 'Surrealist',
                //     'slug' => Str::slug('Surrealist'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $painting->id,
                //     'global_tag_id' => $artworksStoreTag->id,
                //     'name' => 'Typography',
                //     'slug' => Str::slug('Typography'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $painting->id,
                //     'global_tag_id' => $artworksStoreTag->id,
                //     'name' => 'Urban',
                //     'slug' => Str::slug('Urban'),
                //     'type' => 'product'
                // ],


                // [
                //     'parent_id' => $accessories->id,
                //     'global_tag_id' => $autoMotiveStoreTag->id,
                //     'name' => 'Boat Parts',
                //     'slug' => Str::slug('Boat Parts'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $accessories->id,
                //     'global_tag_id' => $autoMotiveStoreTag->id,
                //     'name' => 'Car Parts',
                //     'slug' => Str::slug('Car Parts'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $accessories->id,
                //     'global_tag_id' => $autoMotiveStoreTag->id,
                //     'name' => 'Commercial Truck Parts',
                //     'slug' => Str::slug('Commercial Truck Parts'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $accessories->id,
                //     'global_tag_id' => $autoMotiveStoreTag->id,
                //     'name' => 'Golf Cart Parts',
                //     'slug' => Str::slug('Golf Cart Parts'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $accessories->id,
                //     'global_tag_id' => $autoMotiveStoreTag->id,
                //     'name' => 'Motorcycle Accessories',
                //     'slug' => Str::slug('Motorcycle Accessories'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $accessories->id,
                //     'global_tag_id' => $autoMotiveStoreTag->id,
                //     'name' => 'Motorcycle Parts',
                //     'slug' => Str::slug('Motorcycle Parts'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $accessories->id,
                //     'global_tag_id' => $autoMotiveStoreTag->id,
                //     'name' => 'Personal Watercraft',
                //     'slug' => Str::slug('Personal Watercraft'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $accessories->id,
                //     'global_tag_id' => $autoMotiveStoreTag->id,
                //     'name' => 'Performance & Racing',
                //     'slug' => Str::slug('Performance & Racing'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $accessories->id,
                //     'global_tag_id' => $autoMotiveStoreTag->id,
                //     'name' => 'Scooter Parts',
                //     'slug' => Str::slug('Scooter Parts'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $accessories->id,
                //     'global_tag_id' => $autoMotiveStoreTag->id,
                //     'name' => 'Snowmobile Parts',
                //     'slug' => Str::slug('Snowmobile Parts'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $accessories->id,
                //     'global_tag_id' => $autoMotiveStoreTag->id,
                //     'name' => 'Truck Parts',
                //     'slug' => Str::slug('Truck Parts'),
                //     'type' => 'product'
                // ],


                // [
                //     'parent_id' => $fragrance->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Women\'s Perfume',
                //     'slug' => Str::slug('Women\'s Perfume'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $fragrance->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Men\'s Cologne',
                //     'slug' => Str::slug('Men\'s Cologne'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $fragrance->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Gift Sets',
                //     'slug' => Str::slug('Gift Sets'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $makeup->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Foundation',
                //     'slug' => Str::slug('Foundation'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $makeup->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Lips',
                //     'slug' => Str::slug('Lips'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $makeup->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Eyeshadow',
                //     'slug' => Str::slug('Eyeshadow'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $makeup->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Brushes',
                //     'slug' => Str::slug('Brushes'),
                //     'type' => 'product'
                // ],

                // [
                //     'parent_id' => $skincare->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Gifts & Sets',
                //     'slug' => Str::slug('Gifts & Sets'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $skincare->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Moisturizers',
                //     'slug' => Str::slug('Moisturizers'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $skincare->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Tinted Moisturizers',
                //     'slug' => Str::slug('Tinted Moisturizers'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $skincare->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Night Treatments',
                //     'slug' => Str::slug('Night Treatments'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $skincare->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Face Wash',
                //     'slug' => Str::slug('Face Wash'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $skincare->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Facial Cleansers',
                //     'slug' => Str::slug('Facial Cleansers'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $skincare->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Suncare',
                //     'slug' => Str::slug('Suncare'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $skincare->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Sunless Tanner',
                //     'slug' => Str::slug('Sunless Tanner'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $skincare->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Hand & Foot Care',
                //     'slug' => Str::slug('Hand & Foot Care'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $skincare->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Anti-Aging',
                //     'slug' => Str::slug('Anti-Aging'),
                //     'type' => 'product'
                // ],


                // [
                //     'parent_id' => $bathAndBody->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Body Wash',
                //     'slug' => Str::slug('Body Wash'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bathAndBody->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Shower Gel',
                //     'slug' => Str::slug('Shower Gel'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bathAndBody->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Bar Soap',
                //     'slug' => Str::slug('Bar Soap'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bathAndBody->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Men\'s Body Wash',
                //     'slug' => Str::slug('Men\'s Body Wash'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bathAndBody->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Bath Salts',
                //     'slug' => Str::slug('Bath Salts'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bathAndBody->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Bubble Bath',
                //     'slug' => Str::slug('Bubble Bath'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bathAndBody->id,
                //     'global_tag_id' => $beautyStoreTag->id,
                //     'name' => 'Travel Size',
                //     'slug' => Str::slug('Travel Size'),
                //     'type' => 'product'
                // ],

                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Road',
                //     'slug' => Str::slug('Road'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Mountain',
                //     'slug' => Str::slug('Mountain'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Cyclocross',
                //     'slug' => Str::slug('Cyclocross'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Commuter-Urban',
                //     'slug' => Str::slug('Commuter-Urban'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Comfort',
                //     'slug' => Str::slug('Comfort'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Cruiser',
                //     'slug' => Str::slug('Cruiser'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Fitness',
                //     'slug' => Str::slug('Fitness'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Hybrid',
                //     'slug' => Str::slug('Hybrid'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Children\'s',
                //     'slug' => Str::slug('Children\'s'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'BMX',
                //     'slug' => Str::slug('BMX'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Other',
                //     'slug' => Str::slug('Other'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Mountain',
                //     'slug' => Str::slug('Mountain'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'City & Recreation',
                //     'slug' => Str::slug('City & Recreation'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bikes->id,
                //     'global_tag_id' => $bicycleStoreTag->id,
                //     'name' => 'Cruiser',
                //     'slug' => Str::slug('Cruiser'),
                //     'type' => 'product'
                // ],


                
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Jeans',
                //     'slug' => Str::slug('Big Jeans'),
                //     'type' => 'product'
                // ],
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Shirts',
                //     'slug' => Str::slug('Big Shirts'),
                //     'type' => 'product'
                // ],
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Shorts',
                //     'slug' => Str::slug('Big Shorts'),
                //     'type' => 'product'
                // ],
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Pants',
                //     'slug' => Str::slug('Big Pants'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Blazers',
                //     'slug' => Str::slug('Big Blazers'),
                //     'type' => 'product'
                // ],
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Sportcoats',
                //     'slug' => Str::slug('Big Sportcoats'),
                //     'type' => 'product'
                // ],
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Underwear',
                //     'slug' => Str::slug('Big Underwear'),
                //     'type' => 'product'
                // ],
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Undershirts',
                //     'slug' => Str::slug('Big Undershirts'),
                //     'type' => 'product'
                // ],
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Socks',
                //     'slug' => Str::slug('Big Socks'),
                //     'type' => 'product'
                // ],
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Pajamas',
                //     'slug' => Str::slug('Big Pajamas'),
                //     'type' => 'product'
                // ],
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Robes',
                //     'slug' => Str::slug('Big Robes'),
                //     'type' => 'product'
                // ],
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Swim Trunks',
                //     'slug' => Str::slug('Big Swim Trunks'),
                //     'type' => 'product'
                // ],
                
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Golf Wear',
                //     'slug' => Str::slug('Golf Wear'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $bigAndTall->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Big Surf & Skate',
                //     'slug' => Str::slug('Big Surf & Skate'),
                //     'type' => 'product'
                // ],
                // [
                //     'parent_id' => $lingerie->id,
                //     'global_tag_id' => $autoFashionStoreTag->id,
                //     'name' => 'Rompers & Bodysuits',
                //     'slug' => Str::slug('Rompers & Bodysuits'),
                //     'type' => 'product'
                // ],
            ];

        StandardTag::insert($tagsLevelTwo);
    }
}
