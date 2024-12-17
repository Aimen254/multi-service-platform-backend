<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::updateOrCreate(['code' => 'en'], [
            'name' => 'English',
            'status' => 'active',
            'is_default' => 1
        ]);
    }
}
