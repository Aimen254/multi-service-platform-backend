<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BusinessSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $businesses = Business::whereNull('slug')->get();
        foreach ($businesses as $business) {
            $business->slug = Str::slug($business?->name);
            $business->save();
        }
    }
}
