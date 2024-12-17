<?php

namespace Modules\RealEstate\Database\Seeders;

use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UpdateStandardTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $module = StandardTag::where('slug', 'real-estate')->first();

        // get level two tags of real estate
        $levelThreeTags = StandardTag::whereHas('levelThree', function ($query) use ($module) {
            $query->where('L1', $module->id);
        })->select('name', 'slug', 'id')->get();

        $newNames = [
            'Area 1 - Baker/Zachary',
            'Area 2 - Central',
            'Area 3 - North Baton Rouge',
            'Area 4 - Southeast Baton Rouge',
            'Area 5 - Downtown, LSU and South Baton Rouge',
            'Area 6 - Mid-City',
            'Area 7 - West Baton Rouge and Iberville',
            'Area 8 - Livingston and St. Helena',
            'Area 9 - Ascension',
            'Area 10 - Pointe Coupee'
        ];

        foreach ($levelThreeTags as $tag) {
            foreach ($newNames as $newName) {
                // Check if the tag's name contains a part of the $newName
                if (Str::contains($newName, $tag->name)) {
                    // Update the tag's name to match the new name
                    $tag->update([
                        'name' => $newName,
                        'slug' => Str::slug($newName)
                    ]);
                    break; // Break the inner loop if a match is found
                }
            }
        }
    }
}
