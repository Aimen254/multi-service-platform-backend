<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateSaturdayInBusinessScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('business_schedules') 
        ->where('name', 'Saturaday')
        ->update(['name' => 'Saturday']);
    }
}
