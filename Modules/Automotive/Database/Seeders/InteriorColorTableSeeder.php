<?php

namespace Modules\Automotive\Database\Seeders;

use Carbon\Carbon;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class InteriorColorTableSeeder extends Seeder
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
        $interior_color_attribute = Attribute::updateOrCreate(
            ['slug' => Str::slug('Interior Color')],
            [
                'name' => 'Interior Color',
                'status' => 'active',
                'global_tag_id' => $automotive->id
            ]
        );
        $interior_color_attribute->moduleTags()->syncWithoutDetaching($automotive->id);

        $balckColor = StandardTag::updateOrCreate([
            'slug' => Str::slug('Black')
        ], [
            'name' => 'Black',
            'type' => 'attribute',
            'priority' => 2,
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $interior_color_attribute->standardTags()->syncWithoutDetaching($balckColor->id);

        $gray = StandardTag::updateOrCreate([
            'slug' => Str::slug('Gray')
        ], [
            'name' => 'Gray',
            'type' => 'attribute',
            'priority' => 2,
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $interior_color_attribute->standardTags()->syncWithoutDetaching($gray->id);
    }
}
