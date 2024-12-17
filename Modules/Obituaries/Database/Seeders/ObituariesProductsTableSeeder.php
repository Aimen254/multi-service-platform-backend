<?php

namespace Modules\Obituaries\Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Database\Seeder;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;

class ObituariesProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $obituaries = StandardTag::where('slug', 'obituaries')->first();
        $sex = StandardTag::where('slug', 'sex')->first();
        $male = StandardTag::where('slug', 'male')->first();
        $female = StandardTag::where('slug', 'female')->first();
        $all = StandardTag::where('slug', 'all')->first();

        $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
        $businessOwner1 = User::whereEmail('businessOwner1@interapptive.com')->first();
        $businessOwner2 = User::whereEmail('businessOwner2@interapptive.com')->first();

        $data = [
            [
                'name' => 'John Doe',
                'description' => 'A beloved family member and friend.',
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://www.bridgemi.com/sites/default/files/styles/full_width_image/public/hero_images/kaiser_3.jpg?itok=qbN-Tju0',
                'date_of_birth' => Carbon::create(1992, 7, 20),
                'date_of_death' => Carbon::create(2023, 2, 28),
                'L1' =>  $obituaries->id,
                'L2' => $sex->id,
                'L3' =>  $male->id,
                'L4' =>   $all->id,
            ],
            [
                'name' => 'Jane Smith',
                'description' => 'Remembered for her kindness and generosity.',
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://media.npr.org/assets/img/2019/09/26/toraja-death----11_custom-7f2a54d79e9324f235be3c704c7e7e5e0d16211f-s1600-c85.webp',
                'date_of_birth' => Carbon::create(1970, 7, 20),
                'date_of_death' => Carbon::create(2000, 2, 28),
                'L1' =>  $obituaries->id,
                'L2' => $sex->id,
                'L3' =>  $female->id,
                'L4' =>   $all->id,
            ],
            [
                'name' => 'Jone Doe',
                'description' => 'Remembered for her kindness and generosity.',
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://static01.nyt.com/images/2020/12/21/magazine/22-mag-Death-03/22-mag-Death-03-jumbo-v3.jpg?quality=75&auto=webp',
                'date_of_birth' => Carbon::create(1978, 7, 20),
                'date_of_death' => Carbon::create(2009, 2, 28),
                'L1' =>  $obituaries->id,
                'L2' => $sex->id,
                'L3' =>  $female->id,
                'L4' =>   $all->id,
            ],
        ];

        foreach ($data as $item) {
            ModuleSessionManager::setModule('obituaries');
            $product = Product::updateOrCreate(['name' => $item['name'],], [
                'description' => $item['description'],
                'user_id' => $item['user_id'],
                'status' => $item['status'],
                'date_of_birth' => $item['date_of_birth']->format('Y-m-d'),
                'date_of_death' => $item['date_of_death']->format('Y-m-d')
            ]);

            if ($item['image']) {
                $product->media()->where('type', 'image')->delete();
                $product->media()->create([
                    'path' => $item['image'],
                    'type' => 'image',
                    'is_external' => 1
                ]);
            }

            $product->standardTags()->syncWithoutDetaching([$item['L1'], $item['L2'], $item['L3'], $item['L4']]);
            ProductTagsLevelManager::checkProductTagsLevel($product);
        }
    }
}
