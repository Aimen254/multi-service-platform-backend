<?php

namespace Modules\Retail\Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Modules\Retail\Entities\SubscriptionPermission;

class SubscriptionPermissionTableSeeder extends Seeder
{
    protected $toTruncate = [
        'subscription_permissions',
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();
        foreach($this->toTruncate as $table) {
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();

        $subscriptionPermissions = [
            [
                'product_id' => 'prod_NLgbnZaaC9u5Et',
                'key' => 'total_businesses',
                'value' => 1,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLgbnZaaC9u5Et',
                'key' => 'featured_businesses',
                'value' => 1,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLgbnZaaC9u5Et',
                'key' => 'featured_products',
                'value' => null,
                'status' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLgbnZaaC9u5Et',
                'key' => 'total_products',
                'value' => 12,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLgbnZaaC9u5Et',
                'key' => 'delivery',
                'value' => null,
                'status' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLgbnZaaC9u5Et',
                'key' => 'type',
                'value' => null,
                'status' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhQ2ALdBOEU9Z',
                'key' => 'total_businesses',
                'value' => 2,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhQ2ALdBOEU9Z',
                'key' => 'featured_businesses',
                'value' => 2,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhQ2ALdBOEU9Z',
                'key' => 'featured_products',
                'value' => null,
                'status' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhQ2ALdBOEU9Z',
                'key' => 'total_products',
                'value' => -1,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhQ2ALdBOEU9Z',
                'key' => 'delivery',
                'value' => null,
                'status' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhQ2ALdBOEU9Z',
                'key' => 'type',
                'value' => null,
                'status' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhSz30qtAC9z6',
                'key' => 'total_businesses',
                'value' => 2,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhSz30qtAC9z6',
                'key' => 'featured_businesses',
                'value' => 2,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhSz30qtAC9z6',
                'key' => 'featured_products',
                'value' => null,
                'status' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhSz30qtAC9z6',
                'key' => 'total_products',
                'value' => -1,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhSz30qtAC9z6',
                'key' => 'delivery',
                'value' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhSz30qtAC9z6',
                'key' => 'type',
                'value' => null,
                'status' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhTF1CiPbSgG5',
                'key' => 'total_businesses',
                'value' => 3,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhTF1CiPbSgG5',
                'key' => 'featured_businesses',
                'value' => 3,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhTF1CiPbSgG5',
                'key' => 'featured_products',
                'value' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhTF1CiPbSgG5',
                'key' => 'total_products',
                'value' => -1,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhTF1CiPbSgG5',
                'key' => 'delivery',
                'value' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhTF1CiPbSgG5',
                'key' => 'type',
                'value' => 'L3',
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhVtMOVMPkvzo',
                'key' => 'total_businesses',
                'value' => 4,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhVtMOVMPkvzo',
                'key' => 'featured_businesses',
                'value' => 4,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhVtMOVMPkvzo',
                'key' => 'featured_products',
                'value' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhVtMOVMPkvzo',
                'key' => 'total_products',
                'value' => -1,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhVtMOVMPkvzo',
                'key' => 'delivery',
                'value' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhVtMOVMPkvzo',
                'key' => 'type',
                'value' => 'L2',
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhfJW8w3BWllD',
                'key' => 'total_businesses',
                'value' => 5,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhfJW8w3BWllD',
                'key' => 'featured_businesses',
                'value' => 5,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhfJW8w3BWllD',
                'key' => 'featured_products',
                'value' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhfJW8w3BWllD',
                'key' => 'total_products',
                'value' => -1,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhfJW8w3BWllD',
                'key' => 'delivery',
                'value' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NLhfJW8w3BWllD',
                'key' => 'type',
                'value' => 'L1',
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        foreach ($subscriptionPermissions as $permission) {
            $permissions =  SubscriptionPermission::create($permission);
        }
    }
}
