<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Retail\Entities\SubscriptionPermission;

use Illuminate\Support\Facades\Schema;

class SubscriptionPermissionSeeder extends Seeder
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
        Schema::disableForeignKeyConstraints();
        foreach ($this->toTruncate as $table) {
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
                'product_id' => 'prod_NsgmiS4EDDMO6R',
                'key' => 'total_businesses',
                'value' => 4,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NsgmiS4EDDMO6R',
                'key' => 'featured_businesses',
                'value' => 4,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NsgmiS4EDDMO6R',
                'key' => 'featured_products',
                'value' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NsgmiS4EDDMO6R',
                'key' => 'total_products',
                'value' => -1,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NsgmiS4EDDMO6R',
                'key' => 'delivery',
                'value' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NsgmiS4EDDMO6R',
                'key' => 'type',
                'value' => 'L2',
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NsgniPaOnXErL1',
                'key' => 'total_businesses',
                'value' => 5,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NsgniPaOnXErL1',
                'key' => 'featured_businesses',
                'value' => 5,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NsgniPaOnXErL1',
                'key' => 'featured_products',
                'value' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NsgniPaOnXErL1',
                'key' => 'total_products',
                'value' => -1,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NsgniPaOnXErL1',
                'key' => 'delivery',
                'value' => null,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_id' => 'prod_NsgniPaOnXErL1',
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
