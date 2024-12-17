<?php

namespace Modules\Notices\Database\Seeders;

use App\Models\Product;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Database\Seeder;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;

class NoticesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $business = Business::where('slug', 'smith-associates-law-firm')->first();
        $notices = StandardTag::where('slug', 'notices')->where('type', 'module')->first();

        $public_notices = StandardTag::where('slug', 'public-notices')->first();
        $legal_notices = StandardTag::where('slug', 'legal-notices')->first();

        $court_summnons = StandardTag::where('slug', 'court-summons')->first();
        $legal_settlements = StandardTag::where('slug', 'legal-settlements')->first();

        $permits = StandardTag::where('slug', 'permits')->first();

        $all = StandardTag::where('slug', 'all')->first();

        $data = [
            [
                'name' => 'Notice of Intent to Sue',
                'description' => "notify of my intent to pursue legal action against you for the alleged breach of contract related to the agreement entered into on 1 dec, 2023, a copy of which is attached herewith.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1589391886645-d51941baf7fb?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'L1' =>  $notices->id,
                'L2' => $legal_notices->id,
                'L3' =>  $court_summnons->id,
                'L4' =>  $all->id,
                'type' => 'legal'
            ],
            [
                'name' => 'Legal Notice of Contract Termination',
                'description' => "Legal Notice of Contract Termination",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1589391886645-d51941baf7fb?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'L1' =>  $notices->id,
                'L2' => $legal_notices->id,
                'L3' =>  $legal_settlements->id,
                'L4' =>  $all->id,
                'type' => 'legal'
            ],
            [
                'name' => 'Public Notice of Property Liens',
                'description' => "Public Notice of Property Liens",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1589391886645-d51941baf7fb?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'L1' =>  $notices->id,
                'L2' => $public_notices->id,
                'L3' =>  $permits->id,
                'L4' =>  $all->id,
                'type' => 'public'
            ],
            [
                'name' => 'MetroNotice Board',
                'description' => "MetroNotice Board",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1589391886645-d51941baf7fb?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'L1' =>  $notices->id,
                'L2' => $public_notices->id,
                'L3' =>  $permits->id,
                'L4' =>  $all->id,
                'type' => 'public'
            ],
        ];

        foreach ($data as $item) {
            ModuleSessionManager::setModule('notices');
            $product = Product::updateOrCreate(['name' => $item['name']], [
                'description' => $item['description'],
                'business_id' => $item['business_id'],
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
