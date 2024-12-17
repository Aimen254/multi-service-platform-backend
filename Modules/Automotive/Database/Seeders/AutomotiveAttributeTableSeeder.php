<?php

namespace Modules\Automotive\Database\Seeders;

use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AutomotiveAttributeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $automotive = StandardTag::where('slug', 'automotive')->firstOrFail();
        $engine = Attribute::updateOrCreate(
            ['slug' => Str::slug('Engine')],
            [
                'name' => 'Engine',
                'status' => 'active',
                'global_tag_id' => $automotive->id
            ]
        );
        $engine->moduleTags()->syncWithoutDetaching($automotive->id);

        // transmission
        $transmission = Attribute::updateOrCreate(
            ['slug' => Str::slug('Transmission')],
            [
                'name' => 'Transmission',
                'status' => 'active',
                'global_tag_id' => $automotive->id
            ]
        );
        $transmission->moduleTags()->syncWithoutDetaching($automotive->id);

        // drivetrain
        $drivetrain = Attribute::updateOrCreate(
            ['slug' => Str::slug('Drivetrain')],
            [
                'name' => 'Drivetrain',
                'status' => 'active',
                'global_tag_id' => $automotive->id
            ]
        );
        $drivetrain->moduleTags()->syncWithoutDetaching($automotive->id);

        // Fuel type
        $fuel_type = Attribute::updateOrCreate(
            ['slug' => Str::slug('Fuel Type')],
            [
                'name' => 'Fuel Type',
                'status' => 'active',
                'global_tag_id' => $automotive->id
            ]
        );
        $fuel_type->moduleTags()->syncWithoutDetaching($automotive->id);
    }
}
