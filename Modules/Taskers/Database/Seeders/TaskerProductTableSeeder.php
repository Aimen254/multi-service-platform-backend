<?php

namespace Modules\Taskers\Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Database\Seeder;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;

class TaskerProductTableSeeder extends Seeder
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
        $taskers = StandardTag::where('slug', 'taskers')->first();

        // L2 tags
        $assembly = StandardTag::where('slug', 'assembly')->first();
        $tvMounts = StandardTag::where('slug', 'tv-mounts')->first();

        // L3 tags
        $furnitureAssembly = StandardTag::where('slug', 'furniture-assembly')->first();
        $exerciseEquipmentAssembly = StandardTag::where('slug', 'exercise-equipment-assembly')->first();
        $grillAssembly = StandardTag::where('slug', 'grill-assembly')->first();
        $fixedTvMounts = StandardTag::where('slug', 'fixed-tv-mounts')->first();

        // L4 tags
        $ikeaFurniture = StandardTag::where('slug', 'ikea-furniture')->first();
        $officeFurniture = StandardTag::where('slug', 'office-furniture')->first();
        $homeGyms = StandardTag::where('slug', 'home-gyms')->first();
        $electricalStyleGrill = StandardTag::where('slug', 'electrical-style-grill')->first();
        $lowProfileFixedMount = StandardTag::where('slug', 'low-profile-fixed-mount')->first();
        $tiltedFixedMount = StandardTag::where('slug', 'tilted-fixed-mount')->first();

        // Taskers
        $tasker1 = User::whereEmail('tasker01@interapptive.com')->with('cards')->first();
        $tasker2 = User::whereEmail('tasker02@interapptive.com')->with('cards')->first();
        $tasker3 = User::whereEmail('tasker03@interapptive.com')->with('cards')->first();

        $data = [
            [
                'name' => 'I Will Assemble Any IKEA Furniture',
                'description' => '<p>I can put together any IKEA furniture and accessories that require assembly or fixing to become functional.</p>',
                'user_id' =>  $tasker1->id,
                'price' => '55',
                'price_type' => 'hourly',
                'status' => 'active',
                'is_commentable' => true,
                'image' => 'https://images.pexels.com/photos/5217124/pexels-photo-5217124.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                'L1' => $taskers->id,
                'L2' => $assembly->id,
                'L3' => $furnitureAssembly->id,
                'L4' => $ikeaFurniture->id,
            ],
            [
                'name' => 'I Will Assemble Any Furniture, Indoor, Outdoor',
                'description' => '<p>I will assemble any indoor or outdoor furniture piece. (Couches, bedframes, dressers, desks, tables, gazebos, swings, patio sets, etc.)</p><br><p>If needed, I can locally (NY & NJ) deliver your furniture piece(s)</p>',
                'user_id' =>  $tasker1->id,
                'price' => '55',
                'price_type' => 'hourly',
                'status' => 'active',
                'is_commentable' => true,
                'image' => 'https://images.pexels.com/photos/5805491/pexels-photo-5805491.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                'L1' => $taskers->id,
                'L2' => $assembly->id,
                'L3' => $furnitureAssembly->id,
                'L4' => $officeFurniture->id,
            ],
            [
                'name' => 'Hey, I Am Handyman Specializing in TV Mounting.',
                'description' => '<p>I can customize your TV space just the way you want it. Whether your wall is made of wood, plaster, drywall, brick, or even concrete, I am able to mount your TV, no problem.</p>',
                'user_id' =>  $tasker2->id,
                'price' => '61.94',
                'price_type' => 'hourly',
                'status' => 'active',
                'is_commentable' => true,
                'image' => 'https://images.pexels.com/photos/6020432/pexels-photo-6020432.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                'L1' => $taskers->id,
                'L2' => $tvMounts->id,
                'L3' => $fixedTvMounts->id,
                'L4' => $tiltedFixedMount->id,
            ],
            [
                'name' => 'A Low Profile Mount, I Can Mount Your Appliances, Frames, Tools, and Equipments.',
                'description' => '<p>I  can mount your appliances, frames, tools, equipment, etc. Let me know if you need help mounting or if you have any questions at all. Rate of the TV Mounting rate will depend on the type of mounting that you need and where it is being mounted on. $45 - $60, depending on the type of mounting.</p>',
                'user_id' =>  $tasker2->id,
                'price' => '45.42',
                'price_type' => 'hourly',
                'status' => 'active',
                'is_commentable' => true,
                'image' => 'http://www.visolutiontv.com/wp-content/uploads/2010/06/low-profile-tv-mount-install-500.jpg',
                'L1' => $taskers->id,
                'L2' => $tvMounts->id,
                'L3' => $fixedTvMounts->id,
                'L4' => $lowProfileFixedMount->id,
            ],
            [
                'name' => 'I Have Experience in Assembling Treadmills, Elipticals, Spin Bikes etc.',
                'description' => '<p>Do you need help with: reading assembly instructions, assembling things (machines, tools, toys, or models), arranging exercise equipments, securing it to walls, or removing boxes? I&apos;m your guy!</p>',
                'user_id' =>  $tasker3->id,
                'price' => '69',
                'price_type' => 'hourly',
                'status' => 'active',
                'is_commentable' => true,
                'image' => 'https://i.pinimg.com/564x/53/f5/0b/53f50b651256ac1e60c24e83fcc1688f.jpg',
                'L1' => $taskers->id,
                'L2' => $assembly->id,
                'L3' => $exerciseEquipmentAssembly->id,
                'L4' => $homeGyms->id,
            ],
            [
                'name' => 'Need help with new grill assembly?. Let me take care of the assembly of your new grill for you.',
                'description' => '<p>There can\'t be a great barbeque without an excellent barbeque grill. I can assemble both gas style and electrical style grills.</p>',
                'user_id' =>  $tasker3->id,
                'price' => '77.05',
                'price_type' => 'fixed',
                'status' => 'active',
                'is_commentable' => true,
                'image' => 'https://images.thdstatic.com/productImages/3b6aa706-8153-4a44-8055-e0219eba06c3/svn/dyna-glo-propane-grills-dgp397cnp-d-76_600.jpg',
                'L1' => $taskers->id,
                'L2' => $assembly->id,
                'L3' => $grillAssembly->id,
                'L4' => $electricalStyleGrill->id,
            ],
        ];

        foreach ($data as $item) {
            ModuleSessionManager::setModule('taskers');
            $product = Product::updateOrCreate(['name' => $item['name']], [
                'name' => $item['name'],
                'description' => $item['description'],
                'user_id' => $item['user_id'],
                'price' => $item['price'],
                'price_type' => $item['price_type'],
                'status' => $item['status'],
                'is_commentable' => $item['is_commentable']
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
