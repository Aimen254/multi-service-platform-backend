<?php

namespace Modules\RealEstate\Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Database\Seeder;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;

class PropertyListingSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $agent1 = User::whereEmail('agent01@interapptive.com')->first();
        $agent2 = User::whereEmail('agent02@interapptive.com')->first();

        $business1 = Business::where('slug', 'pioneer-property-partners')->first();

        $business2 = Business::where('slug', 'dream-estates')->first();
        $realEstate = StandardTag::where('slug', 'real-estate')->where('type', 'module')->first();

        $residential = StandardTag::where('slug', 'residential')->first();
        $bakerzachary = StandardTag::where('slug', 'bakerzachary')->first();
        $central = StandardTag::where('slug', 'central')->first();

        $zachrayZipCode = StandardTag::where('slug', '70714')->first();
        $centralZipCode = StandardTag::where('slug', '70739')->first();

        $apartment = StandardTag::where('slug', 'apartments')->first();

        $data = [
            [
                'name' => 'HILLSIDE AVE',
                'description' => "From world-renowned architectural design firm, Saota, this 20,000 sq. ft. estate is set on a promontory with unparalleled 300 degree city skyline views boasting unmatched design and exquisite bespoke finishes throughout. Automated glass sliding doors create a seamless confluence from the interior living spaces to plentiful outside seating and dining areas, including a 175-foot linear pool culminating in a waterfall cascading into an atrium garden. A 15-foot outdoor television rises from the ground with horizontal and vertical rotation, visible from every room in the house. Enormous entertainer's rooftop deck, state-of-the-art theater, curated wine cellar, glass elevator, unrivaled security and audio/visual integration, and a spa retreat replete with wet and dry sauna, cold and hot water plunge pools, and massage therapy. Private driveway leads to a multi-car garage. Unquestionably the most impressive property ever built above the Sunset Strip.",
                'business_id' =>  $business2->id,
                'user_id' => $agent1->id,
                'type' => 'for-sale',
                'status' => 'active',
                'price' => 124300,
                'image' => 'https://images.unsplash.com/photo-1448630360428-65456885c650?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8cHJvcGVydHklMjBsaXN0aW5nfGVufDB8fDB8fHww',
                'L1' =>   $realEstate->id,
                'L2' => $apartment->id,
                'L3' => $bakerzachary->id,
                'L4' =>  $zachrayZipCode->id,
            ],
            [
                'name' => '2218 CHANNEL RD.',
                'description' => 'Rarely does a home fully embody the spirit of Newport Beach living. 2218 Channel Rd does this and more. The beautiful bayfront is set on a premier stretch of the coveted Balboa Peninsula Point. Steps from the iconic Wedge Beach on a quiet and broad tree lined street, this waterfront property is the epitome of luxury living. Timeless curb appeal makes a stunning first impression and sets the tone of coastal grandeur that continues as you enter the chic foyer with sweeping staircase. Prepare to be mesmerized by the magnificent kitchen and great room with sparkling bay views. Enjoy watching colorful sailboats and stunning yachts cruising by from a chef’s dream kitchen, perfect for preparing gourmet meals and hosting catered affairs. Coastal white cabinetry, sand-hued countertops, top-of-the-line appliances and walk-in pantry all surround a striking sea-green island. Opposite the kitchen, a gorgeous fireplace sits center in the expansive living area with coffered ceilings, bar and below ground wine cellar. The living area continues outdoors on a sprawling covered deck with built-in BBQ, fire-pit and private dock that can accommodate a Duffy boat or other vessel up to approx 35 ft, and boundless bay views that are the best Newport Beach has to offer. Completing the first floor is a spacious bedroom that can also serve as a den or office with an ensuite bathroom and an opulent half-bath.',
                'business_id' =>  $business2->id,
                'user_id' => $agent1->id,
                'status' => 'active',
                'type' => 'for-sale',
                'price' => 326378,
                'image' => 'https://plus.unsplash.com/premium_photo-1682145728214-dbd62535af3f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTl8fGVsZWN0cmljYWwlMjB3aXJpbmd8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>   $realEstate->id,
                'L2' => $residential->id,
                'L3' => $central->id,
                'L4' =>  $centralZipCode->id,
            ],
            [
                'name' => '2717 SHELL ST.',
                'description' => 'Some of the photos displayed are the approved renderings to view the before and after concept - Oceanfront property with approved plans to complete a beautiful contemporary beach house designed by the world-renowned architecture firm McClean Design. The remodel is ready to start immediately with all proper permits approved. The builder for this project is ready to meet with the new buyer and has over 30 years of experience building custom homes. This home is currently in great condition and a remodel is only optional. The current home is Mediterranean in style with a bright and airy open floorplan with high-end designer details such as French Limestone, Italian marble, and hand-wrought iron. The main level offers a spacious chef’s kitchen, a breakfast room, and a cozy living area leading to an expansive terrace with panoramic views overlooking the bay. The primary suite comes complete with a fireplace, a private terrace with views of the bay, and a spa-like bathroom with a large tub. Three additional bedrooms and bathrooms and a 4-car garage complete the home. This property occupies the landmark location of the original China Cove home. The China Cove beach is steps away from your door, perfect for a quick swim or paddle sports. NOTE- The timing of this sale offers a unique coincidence for a buyer to purchase 2 other active listings on the market. The 2 homes located on each side of this home are also for sale, making this a rare and maybe the only chance to buy 3 consecutive oceanfront homes in China Cove. These 3 homes for sale make up a total of 8 Parcels.',
                'business_id' =>  $business2->id,
                'user_id' => $agent1->id,
                'status' => 'active',
                'type' => 'for-sale',
                'price' => 78690,
                'image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fHByb3BlcnR5JTIwbGlzdGluZ3xlbnwwfHwwfHx8MA%3D%3D',
                'L1' =>   $realEstate->id,
                'L2' => $residential->id,
                'L3' => $bakerzachary->id,
                'L4' =>  $zachrayZipCode->id,
            ],
            [
                'name' => '1707 RISING GLEN RD.',
                'description' => "The most exquisitely conceived single-story estate with city/ocean/canyon views on one of the most sought after streets in the Hills. The owner spent more than $4.5M to remodel the home for himself, crafting a timeless masterpiece with soaring wood-lined vaulted ceilings, a master bedroom that turns into a movie theater, private library with a working bell-tower clock, master bath with 15 ft. dual-shower and unlacquered brass fixtures, custom sauna, copper outdoor tub, countertops and vanities made from solid steel, custom built and suede-lined walk-in closets, a hidden 85 living room TV that comes up from a concrete bunker under the house, floating stairs to a wine cellar, copper/steel custom bar with recessed steel and leather niches, four fireplaces, outdoor BBQ/bar, LED lighting and smart home technology throughout. An unrivaled chef's kitchen replete with Gaggenau appliances, Leicht imported German cabinetry, and Mavam coffee maker. Lighting throughout from Buster + Punch, London.",
                'business_id' =>  $business2->id,
                'user_id' => $agent2->id,
                'status' => 'active',
                'type' => 'for-rent',
                'price' => 5666789,
                'image' => 'https://images.unsplash.com/photo-1494526585095-c41746248156?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTZ8fHByb3BlcnR5JTIwbGlzdGluZ3xlbnwwfHwwfHx8MA%3D%3D',
                'L1' =>   $realEstate->id,
                'L2' => $apartment->id,
                'L3' => $bakerzachary->id,
                'L4' =>  $zachrayZipCode->id,
            ],
            [
                'name' => '2260 PARK AVE.',
                'description' => "xperience luxury at its finest with this meticulously crafted, newly renovated custom home nestled within the prestigious Park Avenue Estates of Laguna Beach. No detail has been overlooked in this exquisite 5 bed/5 bath, 5,500 sq. ft. property, boasting tasteful upgrades and elegant modern finishes throughout. Take in the breathtaking panoramic vistas of the ocean, canyon, and Catalina Island from every window and patio off of every room. Enjoy the open concept design, effortlessly blending spaciousness with style, creating a truly remarkable living experience. The impressive chef’s kitchen comes replete with a custom-designed oversized island featuring three sinks, bespoke European cabinetry, top-of-the-line Gaggenau appliances, and meticulously crafted countertops. Illuminated wine wall to showcase your 162-bottle wine collection. The tranquil primary suite offers a custom boutique-style walk-in closet and an ensuite spa bathroom with a large custom stone soaking tub and an infrared sauna salt cave, making it a perfect sanctuary for relaxation. Step outside into a private 1/3-acre backyard, offering multiple decks, lush landscaping with an organic herb garden, and fruit trees including a built-in outdoor L-shaped lounging pavilion surrounding a sleek rectangular fire pit perfect for enjoying sunsets and the stunning canyon views. The sparkling saltwater pool and jacuzzi are encircled by glass walls. The oversized driveway with a 3-car garage upgraded with new smart garage doors caters to your parking needs, accommodating up to 7 cars with ease. Recently added three-floor wireless mesh Wi-Fi system inside and out including high-tech security cameras. New water filtration system, remote-controlled electric custom curtains and blinds shade every room at the touch of a button. The bespoke custom furnishings are available to purchase separately upon request. The current owner is an artist/interior designer and welcomes the new owners to enjoy this unique turnkey property offering fully furnished.",
                'business_id' =>  $business2->id,
                'user_id' => $agent2->id,
                'status' => 'active',
                'type' => 'for-rent',
                'price' => 675444,
                'image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fHByb3BlcnR5JTIwbGlzdGluZ3xlbnwwfHwwfHx8MA%3D%3D',
                'L1' =>   $realEstate->id,
                'L2' => $apartment->id,
                'L3' => $bakerzachary->id,
                'L4' =>  $zachrayZipCode->id,
            ],
        ];
        foreach ($data as $item) {
            ModuleSessionManager::setModule('real-estate');
            $product = Product::updateOrCreate(['name' => $item['name']], [
                'description' => $item['description'],
                'business_id' => $item['business_id'],
                'user_id' => $item['user_id'],
                'price' => $item['price'],
                'status' => $item['status'],
                'type' => $item['type']
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
