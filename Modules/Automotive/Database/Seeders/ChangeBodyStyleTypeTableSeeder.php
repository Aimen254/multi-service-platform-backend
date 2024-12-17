<?php

namespace Modules\Automotive\Database\Seeders;

use App\Models\StandardTag;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ChangeBodyStyleTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $bodystyles = StandardTag::whereHas('levelTwo')->whereIn('name', \config()->get('automotive.body_styles'))->get();
        foreach ($bodystyles as $style) {
            $style->update([
                'type' => 'product'
            ]);
        }
    }
}
