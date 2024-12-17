<?php

namespace Modules\Automotive\Database\Seeders;

use Carbon\Carbon;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ExteriorColorTableSeeder extends Seeder
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
        $exterior_color_attribute = Attribute::updateOrCreate(
            ['slug' => Str::slug('Exterior Color')],
            [
                'name' => 'Exterior Color',
                'status' => 'active',
                'global_tag_id' => $automotive->id
            ]
        );
        $exterior_color_attribute->moduleTags()->syncWithoutDetaching($automotive->id);

        $white = StandardTag::updateOrCreate([
            'slug' => Str::slug('white')
        ], [
            'name' => 'white',
            'type' => 'attribute',
            'priority' => 2,
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $exterior_color_attribute->standardTags()->syncWithOutDetaching($white->id);
        $silver = StandardTag::updateOrCreate([
            'slug' => Str::slug('silver')
        ], [
            'name' => 'silver',
            'type' => 'attribute',
            'priority' => 2,
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $exterior_color_attribute->standardTags()->syncWithOutDetaching($silver->id);
    }
}
