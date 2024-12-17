<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderStatusTableSeeder extends Seeder
{
    protected $toTruncate = [
        'order_statuses'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach($this->toTruncate as $table) {
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();
        
        $statuses = [
            ['status' => 'pending', 'status_type' =>'generic'],
            ['status' => 'accepted', 'status_type' =>'generic'],
            ['status' => 'ready_for_collection', 'status_type' => 'pick_up'],
            ['status' => 'ready_for_delivery', 'status_type' => 'delivery'],
            ['status' => 'out_for_delivery', 'status_type' => 'delivery'],
            ['status' => 'delivery_failed', 'status_type' => 'delivery'],
            ['status' => 'completed', 'status_type' =>'generic'],
            ['status' => 'cancelled', 'status_type' =>'generic'],
            ['status' => 'returned', 'status_type' =>'generic'],
            ['status' => 'refunded', 'status_type' =>'generic'],
            ['status' => 'Processing', 'status_type' =>'generic'],
            ['status' => 'partially_refunded', 'status_type' =>'generic'],
            ['status' => 'refund_failed', 'status_type' =>'generic'],
            ['status' => 'rejected', 'status_type' =>'generic'],

        ];

        OrderStatus::insert($statuses);

    }
}
