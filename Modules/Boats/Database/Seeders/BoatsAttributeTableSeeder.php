<?php

namespace Modules\Boats\Database\Seeders;

use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BoatsAttributeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $boats = StandardTag::where('slug', 'boats')->firstOrFail();

        $engine = Attribute::updateOrCreate(
            ['slug' => Str::slug('Engine')],
            [
                'name' => 'Engine',
                'status' => 'active',
                'global_tag_id' => $boats->id
            ]
        );
        $engine->moduleTags()->syncWithoutDetaching($boats->id);

        // Fuel type
        $fuel_type = Attribute::updateOrCreate(
            ['slug' => Str::slug('Fuel Type')],
            [
                'name' => 'Fuel Type',
                'status' => 'active',
                'global_tag_id' => $boats->id
            ]
        );
        $fuel_type->moduleTags()->syncWithoutDetaching($boats->id);

        // color
        $color = Attribute::updateOrCreate(
            ['slug' => Str::slug('Color')],
            [
                'name' => 'Color',
                'status' => 'active',
                'global_tag_id' => $boats->id
            ]
        );
        $color->moduleTags()->syncWithoutDetaching($boats->id);

        // year
        $year = Attribute::updateOrCreate(
            ['slug' => Str::slug('Year')],
            [
                'name' => 'Year',
                'status' => 'active',
                'global_tag_id' => $boats->id
            ]
        );
        $year->moduleTags()->syncWithoutDetaching($boats->id);
    }
}
