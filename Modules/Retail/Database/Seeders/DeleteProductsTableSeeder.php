<?php

namespace Modules\Retail\Database\Seeders;

use App\Models\Product;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class DeleteProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        // businesses list
        $businesses = Business::whereIn('slug', [
            'messengers-gifts',
            // 'victorias-toys-station',
            // 'the-keeping-room'
        ])->get();
        foreach ($businesses as $business) {
            $business->standardTags()->detach(
                $business->standardTags()->whereNotIn('name', ['retail'])->pluck('id')
            );

            foreach ($business->products as $product) {
                $product->tags()->detach();
                $product->standardTags()->detach();
                $product->media()->delete();
                $product->delete();
            }
        }
    }
}
