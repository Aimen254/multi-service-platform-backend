<?php

namespace Modules\Classifieds\Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Database\Seeder;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;

class ClassifiedProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // L1 tag
        $classifieds = StandardTag::where('slug', Product::MODULE_MARKETPLACE)->first();

        // L2 tags
        $fashion = StandardTag::where('slug', 'fashion')->first();

        // L3 tags
        $women = StandardTag::where('slug', 'women')->first();
        $menShoes = StandardTag::where('slug', 'men-shoes')->first();
        $girls = StandardTag::where('slug', 'girls')->first();

        // L4 tag
        $outdoorWear = StandardTag::where('slug', 'outdoor-wear')->first();
        $jeans = StandardTag::where('slug', 'jeans')->first();
        $rompers = StandardTag::where('slug', 'rompers')->first();

        // Customers
        $customer = User::whereEmail('c.customer01@interapptive.com')->with('cards')->first();
        $customer1 = User::whereEmail('c.customer02@interapptive.com')->with('cards')->first();
        $customer2 = User::whereEmail('c.customer03@interapptive.com')->with('cards')->first();

        $data = [
            [
                'name' => 'EXPLORE COMFORT AND STYLE WITH DUNHAM 8500BK MIDLAND BLACK SHOES',
                'description' => '<p>Step into sophistication with the Dunham 8500BK Midland Black shoes. Discover the perfect blend of durability and elegance in these waterproof midland oxfords. Elevate your style with Dunham\'s renowned craftsmanship. Get your pair of Dunham Midland shoes today.</p>',
                'user_id' =>  $customer->id,
                'price' => '134.99',
                'stock' => '5',
                'weight' => '1.5',
                'weight_unit' => 'pound',
                'status' => 'active',
                'image' => 'https://free-classifieds-usa.com/oc-content/uploads/5186/654553.jpg',
                'L1' =>  $classifieds->id,
                'L2' => $fashion->id,
                'L3' =>  $menShoes->id,
                'L4' =>   $outdoorWear->id,
            ],
            [
                'name' => 'WHOLESALE WOMEN’S 2-PIECE SETS IN NYC | JPT WHOLESALE CLOTHING',
                'description' => '<p>Shop women\'s wholesale sets on JPT Clothing. Perfect for women who want the whole outfit. at market leading prices for know more please visit at www.jptclothing.com</p>',
                'user_id' =>  $customer->id,
                'price' => '145',
                'stock' => '20',
                'status' => 'active',
                'image' => 'https://free-classifieds-usa.com/oc-content/uploads/5182/654181.jpg',
                'L1' =>  $classifieds->id,
                'L2' => $fashion->id,
                'L3' =>  $women->id,
                'L4' =>   $outdoorWear->id,
            ],
            [
                'name' => 'SILVER JEANS COUPON: GET DISCOUNT ON JEANS',
                'description' => '<p>Silver Jeans specializes in denim products. It manufactures a wide range of jeans for men, women and kids. You can buy skinny, straight, bootcut or tapered jeans according to your preference. Grab your Silver Jeans coupon from DontPayAll and enjoy budget-friendly shopping.</p>',
                'user_id' =>  $customer1->id,
                'price' => '50',
                'stock' => '1',
                'status' => 'active',
                'image' => 'https://free-classifieds-usa.com/oc-content/uploads/1802/201132.jpg',
                'L1' =>  $classifieds->id,
                'L2' => $fashion->id,
                'L3' =>  $girls->id,
                'L4' =>   $jeans->id,
            ],
            [
                'name' => 'FLORAL PRINT STRAP BACKLESS WOMENS ROMPERS',
                'description' => '<p>Offers High Quality Floral Print Strap Backless Womens Rompers, We have more styles for Jumpsuits&Rompers</p>',
                'user_id' =>  $customer2->id,
                'price' => '14.99',
                'stock' => '10',
                'status' => 'active',
                'image' => 'https://free-classifieds-usa.com/oc-content/uploads/1164/110711.jpg',
                'L1' =>  $classifieds->id,
                'L2' => $fashion->id,
                'L3' =>  $girls->id,
                'L4' =>   $rompers->id,
            ],
        ];

        foreach ($data as $item) {
            ModuleSessionManager::setModule(Product::MODULE_MARKETPLACE);
            $product = Product::updateOrCreate(['name' => $item['name']], [
                'description' => $item['description'],
                'price' => $item['price'],
                'stock' => $item['stock'],
                'user_id' => $item['user_id'],
                'status' => $item['status'],
            ]);

            $product->media()->where('type', 'image')->delete();
            $product->media()->create([
                'path' => $item['image'],
                'type' => 'image',
                'is_external' => 1
            ]);

            $product->standardTags()->syncWithoutDetaching([$item['L1'], $item['L2'], $item['L3'], $item['L4']]);
            ProductTagsLevelManager::checkProductTagsLevel($product);
        }
    }
}
