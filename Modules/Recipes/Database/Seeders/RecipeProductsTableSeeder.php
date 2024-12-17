<?php

namespace Modules\Recipes\Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Database\Seeder;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;

class RecipeProductsTableSeeder extends Seeder
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
        $recipes = StandardTag::where('slug', 'recipes')->first();

        // L2 tags
        $appetizers = StandardTag::where('slug', 'appetizers')->first();
        $seaFood = StandardTag::where('slug', 'seafood')->first();
        $soups = StandardTag::where('slug', 'soups')->first();

        // L3 tags
        $bites = StandardTag::where('slug', 'bites')->first();
        $vegetables = StandardTag::where('slug', 'vegetables')->first();
        $shrimp = StandardTag::where('slug', 'shrimp')->first();

        // L4 tag
        $all = StandardTag::where('slug', 'all')->first();

        // Business Owner Users
        $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
        $businessOwner1 = User::whereEmail('businessOwner1@interapptive.com')->first();
        $businessOwner2 = User::whereEmail('businessOwner2@interapptive.com')->first();

        $data = [
            [
                'name' => 'Sushi rolls from Tsunami, potosinas and more: Best things we ate this week',
                'description' => '<p>My usual order at La Carreta is the al pastor and carnitas tacos. The potosinas were brand new to me.</p><p><br></p><p>The dish comes with three cheese enchiladas topped with salsa verde, queso fresco and sliced avocado, served with rice, beans and skirt steak on a bed of grilled onions. To be honest, the steak and onions is what sold me.</p><p><br></p><p>Everything was tasty and satisfied my craving for Mexican food. I\'ll definitely be ordering this again.</p>',
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://bloximages.newyork1.vip.townnews.com/theadvocate.com/content/tncms/assets/v3/editorial/f/d9/fd9425c2-32e5-11ee-abc6-735b11812b03/64cd2a9bcbb44.image.jpg?resize=666%2C500',
                'L1' =>  $recipes->id,
                'L2' => $seaFood->id,
                'L3' =>  $shrimp->id,
                'L4' =>   $all->id,
            ],
            [
                'name' => 'Jabby’s Pizza signs lease for Acadian-Perkins location',
                'description' => '<p>Jabby’s Pizza has signed a lease to open a location at the intersection of Acadian Thruway and Perkins Road.</p><p><br></p><p>The chain will move into a space at 3627 Perkins, in the Acadian/Perkins Plaza shopping center that was briefly occupied by Hive Pizza, said Mark Hebert of Kurz & Hebert Commercial Real Estate. Hebert handled the lease along with Judah Vedros.</p><p><br></p><p>Jabby’s opened its first location in 2018 in the Highland Park Marketplace on Old Perkins. Diners choose a crust, then customize their pizza with a choice of sauces and fresh toppings, then the pies are quickly cooked in a brick oven. The business has locations in Prairieville and Thibodaux.</p>',
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://bloximages.newyork1.vip.townnews.com/theadvocate.com/content/tncms/assets/v3/editorial/6/2e/62e37e84-2fb5-11ee-b795-9b953dd2f1d5/64c7d0cacbb87.image.jpg?resize=701%2C526',
                'L1' =>  $recipes->id,
                'L2' => $appetizers->id,
                'L3' =>  $bites->id,
                'L4' =>   $all->id,
            ],
            [
                'name' => ' Southern BLT, ahi tuna salad and lamb lollipops: Best things we ate this week',
                'description' => '<p>Willie’s offers a brunch menu on the weekends that ranges from eggs and waffles to chicken biscuits. But I’m a BLT gal and I settled on the Southern BLT. Tart fried green tomatoes, crispy bacon and crunchy lettuce are covered with a creamy au gratin sauce and served in between large slices of wheatberry toast. This one was a handful as all the crunch and flavor of the sandwich melded together with each bite. Be sure to get the fried hash browns on the side. These tiny little bites are seasoned and fun to eat.<p>',
                'user_id' =>  $businessOwner1->id,
                'status' => 'active',
                'image' => 'https://bloximages.newyork1.vip.townnews.com/theadvocate.com/content/tncms/assets/v3/editorial/9/26/926031e8-2d58-11ee-a947-afa33673cc98/64c3d9f0d7ea3.image.jpg?resize=1200%2C900',
                'L1' =>  $recipes->id,
                'L2' => $seaFood->id,
                'L3' =>  $shrimp->id,
                'L4' =>   $all->id,
            ],
            [
                'name' => '\'Yes, chef\' to cold summer soup and a chilled dessert to beat the heat',
                'description' => '<p>A friend from California asked if it was hot enough in Louisiana to fry an egg outside. Her daughter wanted to know.</p><p><br></p><p>It\'s a good question, but I have not tested outdoor egg preparation yet. Instead, I have been cooking foods that are made with the best summer ingredients and served cold.</p><p><br></p><p>Another strategy is avoiding the heat by staying inside more and watching the hottest summer shows. For example, "The Bear," a television series about an award-winning chef who returns to Chicago to run his family\'s sandwich shop, is my new favorite. I\'m now saying, “Yes, chef” to my family members when we cook creative summer meals.</p>',
                'user_id' =>  $businessOwner2->id,
                'status' => 'active',
                'image' => 'https://bloximages.newyork1.vip.townnews.com/theadvocate.com/content/tncms/assets/v3/editorial/f/32/f3285286-2294-11ee-9c34-43316f8c9eb2/64b1cab27cb29.image.jpg?resize=1200%2C900',
                'L1' =>  $recipes->id,
                'L2' => $soups->id,
                'L3' =>  $vegetables->id,
                'L4' =>   $all->id,
            ],


            [
                'name' => 'Sandwiches, burgers and hot dogs: Best things we ate this week',
                'description' => '<p>Put a perfect salad in between bread, and you get the Dream State sandwich from Reginelli’s.</p><p><br></p><p>Toasted focaccia bread is loaded with pancetta, roasted eggplant, Roma tomatoes, crunchy walnuts, mixed greens and herbed goat cheese and drizzled with a balsamic citrus vinaigrette. The pillowy soft yet crispy bread, the tangy dressing and crunchy walnuts meld together with the veggies and create a perfect bite. Don’t sleep on this sandwich!</p><p><br></p><p>The sandwich includes Gulf Coast grouper, Thai slaw, Korean mayo and pickles. Everything is set between a pillow of a brioche bun with crispy edges. The result is a soft crunch of fresh fish and savory flavors. Not to mention, I upgraded my regular fries to truffle fries with roasted garlic aioli.</p>',
                'user_id' =>  $businessOwner1->id,
                'status' => 'active',
                'image' => 'https://bloximages.newyork1.vip.townnews.com/theadvocate.com/content/tncms/assets/v3/editorial/5/af/5af54f26-1c2e-11ee-9107-cf88578fdabf/64a70dc0e1efc.image.jpg?resize=820%2C625',
                'L1' =>  $recipes->id,
                'L2' => $appetizers->id,
                'L3' =>  $bites->id,
                'L4' =>   $all->id,
            ],
        ];

        foreach($data as $item) {
            ModuleSessionManager::setModule('recipes');
            $product = Product::updateOrCreate(['name' => $item['name']], [
                'description' => $item['description'],
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
