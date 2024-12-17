<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attributes = [
            [
                'global_tag_id' => 1,
                'name' => 'Color',
            ],
            [
                'global_tag_id' => 1,
                'name' => 'Size',
            ],
            [
                'global_tag_id' => 1,
                'name' => 'Fabric',
            ],
            [
                'global_tag_id' => 1,
                'name' => 'Neck Type',
            ]
        ];
        foreach($attributes as $attribute){
            $attribute =  Attribute::updateOrCreate(['name' => $attribute['name']], $attribute);
            $attribute->moduleTags()->syncWithoutDetaching(1);
        }
    }
}
