<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Automotive\Entities\DreamCar;

class DreamCarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DreamCar::whereNull('level_four_tag_id')->delete();
    }
}
