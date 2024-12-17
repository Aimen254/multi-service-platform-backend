<?php

namespace Modules\Services\Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Support\Str;
use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;

class ServicesProductSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $business = Business::where('slug', 'handyhome-services')->first();
        $service = StandardTag::where('slug', 'services')->where('type', 'module')->first();
        $electrical = StandardTag::where('slug', 'electrical')->first();
        $electrical_repairs = StandardTag::where('slug', 'electrical-repairs')->first();
        $wiring_issues = StandardTag::where('slug', 'wiring-issues')->first();
        $appliance_installation = StandardTag::where('slug', 'appliance-installation')->first();
        $outlet_repair = StandardTag::where('slug', 'outlet-repair')->first();

        $cleaning = StandardTag::where('slug', 'cleaning')->first();
        $house_cleaning = StandardTag::where('slug', 'house-cleaning')->first();
        $regular_cleaning = StandardTag::where('slug', 'regular-cleaning')->first();
        $one_time_deep_cleaning = StandardTag::where('slug', 'one-time-deep-cleaning')->first();

        $data = [
            [
                'name' => 'Electrical Wiring Repair',
                'description' => 'Electricity plays an integral role in our lives. We rely on it to power the lights in our homes and businesses, cook food, heat water, and more.Although our electrical services are available 24 hours a day.If you want to repair your electric wires or need a professional electrician to perform a simple job, contact us. Whether your household requires new electrical wiring installation or your office has an emergency that needs our attention, we are here for you. We are highly skilled in electrical work and can handle any job efficiently and precisely.',
                'business_id' =>  $business->id,
                'status' => 'active',
                'price' => 300,
                'image' => 'https://plus.unsplash.com/premium_photo-1682145728214-dbd62535af3f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTl8fGVsZWN0cmljYWwlMjB3aXJpbmd8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>   $service->id,
                'L2' => $electrical->id,
                'L3' =>  $electrical_repairs->id,
                'L4' =>   $wiring_issues->id,
            ],
            [
                'name' => 'Appliance Installation',
                'description' => "If you have an emergency electrical problem and are looking to upgrade your electric wires or install a generator, we can help. Booking an electrician at home on our website is simple and easy. We provide trustworthy, licensed electricians who will take care of any wiring, breaker, or switch problems your home may face.When buying a UPS, most people don't realize just how necessary proper installation is. After all, if your UPS is improperly connected to a circuit, it can do more damage than good. But with Karsaaz on call 24/7 for emergency services, you never have to worry about something like that happening to you.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'price' => 600,
                'image' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjB8fGVsZWN0cmljYWwlMjBhcHBsaWFuY2V8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>   $service->id,
                'L2' => $electrical->id,
                'L3' =>  $electrical_repairs->id,
                'L4' =>  $appliance_installation->id,
            ],
            [
                'name' => 'Outlet Repair',
                'description' => "If you're having problems with your electrical sockets, our professionals can help you identify and repair the problem.If your switchboard isn't working, our specialists can assess and fix any problems, ensuring your electrical system is up and running.We provide high-quality household switchboard installation services that adhere to all safety regulations. Our experts can install and update switchboards to match your property's electrical needs.We provide a variety of domestic switchboard installation services to fit your needs.Switchboards and electrical sockets are essential in every house or workplace. A switchboard is a critical part of your electrical system that distributes electricity throughout your home.To reduce the risk of electrical fires and shocks, you must have a proper electrical socket installation service to handle your property's electrical load. On the other hand, electrical sockets allow you to connect your products to the power supply in a safe way. They can represent a significant risk to your health and the safety of your property if not placed in a proper manner.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'price' => 100,
                'image' => 'https://plus.unsplash.com/premium_photo-1683141457896-367f0ad2ddcd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8b3V0bGV0JTIwcmVwYWlyfGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>   $service->id,
                'L2' => $electrical->id,
                'L3' =>  $electrical_repairs->id,
                'L4' =>  $outlet_repair->id,
            ],
            [
                'name' => 'Deep Cleaning',
                'description' => "A deep cleaning service is a thorough and comprehensive cleaning process that goes beyond regular or routine cleaning tasks. It involves cleaning areas and items that might not be cleaned regularly in standard cleaning routines. Deep cleaning services are typically performed periodically to maintain a higher level of cleanliness, hygiene, and overall well-being in a space.Depending on your preferences, the one-off cleaning session may cover the entire property or focus on a specific area, such as your bathroom and kitchen.Services a part of our domestic cleans include:Kitchen cabinets cleaning, Stove and hood cleaning, Washroom Cleaning, Toilets Behind, Bathtubs/Shower cabins, Carpet Vacuuming, Sofa Vacuuming, Switch boards, Fans cleaning, Furniture dusting, Cupboard cleaning, Skirting, Refrigerator/Oven cleaning.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'price' => 1200,
                'image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8ZGVlcCUyMGNsZWFuaW5nfGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>   $service->id,
                'L2' => $cleaning->id,
                'L3' =>  $house_cleaning->id,
                'L4' =>  $one_time_deep_cleaning->id,
            ],
            [
                'name' => 'Regular Cleaning',
                'description' => "A regular cleaning service refers to a professional cleaning arrangement in which our company provides routine cleaning and maintenance services for residential or commercial spaces on a consistent basis. These services can include a wide range of cleaning tasks to keep the environment clean, organized, and sanitized. The frequency of the service can vary based on the client's needs and preferences, and it could be scheduled weekly, bi-weekly, monthly, or at other intervals. Typical tasks included in a regular cleaning service may involve:, Dusting, Vacuuming, Mopping, Bathroom Cleaning, Kitchen Cleaning, Trash Removal, Surface Disinfection, Window Cleaning, Dishwashing & Bedroom Cleaning",
                'business_id' =>  $business->id,
                'status' => 'active',
                'price' => 800,
                'image' => 'https://images.unsplash.com/photo-1615561916422-7014e1078997?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8cmVndWxsYXIlMjBjbGVhbmluZ3xlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>   $service->id,
                'L2' => $cleaning->id,
                'L3' =>  $house_cleaning->id,
                'L4' =>  $regular_cleaning->id,
            ],
        ];

        foreach($data as $item) {
            ModuleSessionManager::setModule('services');
            $product = Product::updateOrCreate(['name' => $item['name']], [
                'description' => $item['description'],
                'business_id' => $item['business_id'],
                'price' => $item['price'],
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
