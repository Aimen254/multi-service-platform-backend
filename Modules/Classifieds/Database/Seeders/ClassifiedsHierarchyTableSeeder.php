<?php

namespace Modules\Classifieds\Database\Seeders;

use App\Models\Product;
use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ClassifiedsHierarchyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // L1 for classified
        $classified = StandardTag::where('slug', Product::MODULE_MARKETPLACE)->where('type', 'module')->first();

        // L1 for retail
        $retailId = StandardTag::where('type', 'module')->where('slug', 'retail')
            ->firstOrFail()->id;

        $retailHierarchy = TagHierarchy::where('L1', $retailId)->get();

        foreach ($retailHierarchy as $heirarchy) {
            // creating herarchy for classified replica of retail
            $classifiedHierarchy = TagHierarchy::updateOrCreate(
                [
                    'L1' => $classified->id,
                    'L2' => $heirarchy->L2,
                    'L3' => $heirarchy->L3
                ],
                [
                    'level_type' => 4,
                    'is_multiple' => \true
                ]
            );

            // creating level 4
            $classifiedHierarchy->standardTags()->syncWithoutDetaching($heirarchy->standardTags()->pluck('id'));
        }
    }
}
