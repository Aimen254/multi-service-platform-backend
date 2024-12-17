<?php

namespace Modules\Automotive\Database\Seeders;

use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class YearAttributeTableSeeder extends Seeder
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
        $yearAttribute = Attribute::updateOrCreate(
            ['slug' => Str::slug('Year')],
            [
                'name' => 'Year',
                'status' => 'active',
                'global_tag_id' => $automotive->id
            ]
        );

        $yearAttribute->moduleTags()->syncWithoutDetaching($automotive->id);
    }
}
