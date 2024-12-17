<?php

namespace Modules\Classifieds\Database\Seeders;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ClassifiedAttributeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // module tag
        $classifieds = StandardTag::where('slug', Product::MODULE_MARKETPLACE)->where('type', 'module')->firstOrFail();
        // saving attribute
        $attribute = Attribute::updateOrCreate(
            ['slug' => Str::slug('Condition')],
            [
                'name' => 'Condition',
                'status' => 'active',
                'global_tag_id' => $classifieds->id
            ]
        );

        $attribute->moduleTags()->syncWithoutDetaching($classifieds->id);

        // attribute tags array
        $attributeTags = [
            [
                'name' => 'New',
                'slug' => Str::slug('New'),
                'priority' => '2',
                'type' => 'attribute',
                'attribute_id' => $attribute->id,
                'status' => 'active'
            ],
            [
                'name' => 'Used',
                'slug' => Str::slug('Used'),
                'priority' => '2',
                'type' => 'attribute',
                'attribute_id' => $attribute->id,
                'status' => 'active'
            ],
            [
                'name' => 'Average',
                'slug' => Str::slug('Average'),
                'priority' => '2',
                'type' => 'attribute',
                'attribute_id' => $attribute->id,
                'status' => 'active'
            ],
            [
                'name' => 'Rough',
                'slug' => Str::slug('Rough'),
                'priority' => '2',
                'type' => 'attribute',
                'attribute_id' => $attribute->id,
                'status' => 'active'
            ],
            [
                'name' => 'Clean',
                'slug' => Str::slug('Clean'),
                'priority' => '2',
                'type' => 'attribute',
                'attribute_id' => $attribute->id,
                'status' => 'active'
            ],
            [
                'name' => 'Near New',
                'slug' => Str::slug('Near New'),
                'priority' => '2',
                'type' => 'attribute',
                'attribute_id' => $attribute->id,
                'status' => 'active'
            ]
        ];

        // saving and mapping attribute tags
        foreach ($attributeTags as $tag) {
            $attr = StandardTag::updateOrCreate(
                ['slug' => $tag['slug'], 'type' => 'attribute'],
                $tag
            );

            $attr->attribute()->syncWithOutDetaching($attribute->id);
        }
    }
}
