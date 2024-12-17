<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    protected $toTruncate = [
        'role_has_permissions',
        'permissions'
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

        $permissions = [
            // administrators
            ['group' => 'administrators', 'name' => 'view_administrators', 'title' => 'View administrators', 'guard_name' => 'web'],
            ['group' => 'administrators', 'name' => 'add_administrators', 'title' => 'Add administrators', 'guard_name' => 'web'],
            ['group' => 'administrators', 'name' => 'edit_administrators', 'title' => 'Edit administrators', 'guard_name' => 'web'],
            ['group' => 'administrators', 'name' => 'delete_administrators', 'title' => 'Delete administrators', 'guard_name' => 'web'],

            // business_owners
            ['group' => 'business_owners', 'name' => 'view_business_owners', 'title' => 'View Business Owners', 'guard_name' => 'web'],
            ['group' => 'business_owners', 'name' => 'add_business_owners', 'title' => 'Add Business Owners', 'guard_name' => 'web'],
            ['group' => 'business_owners', 'name' => 'edit_business_owners', 'title' => 'Edit Business Owners', 'guard_name' => 'web'],
            ['group' => 'business_owners', 'name' => 'delete_business_owners', 'title' => 'Delete Business Owners', 'guard_name' => 'web'],

            // customers
            ['group' => 'customers', 'name' => 'view_customers', 'title' => 'View customers', 'guard_name' => 'web'],
            ['group' => 'customers', 'name' => 'add_customers', 'title' => 'Add customers', 'guard_name' => 'web'],
            ['group' => 'customers', 'name' => 'edit_customers', 'title' => 'Edit customers', 'guard_name' => 'web'],
            ['group' => 'customers', 'name' => 'delete_customers', 'title' => 'Delete customers', 'guard_name' => 'web'],

            // reporters
            ['group' => 'reporters', 'name' => 'view_reporters', 'title' => 'View reporters', 'guard_name' => 'web'],
            ['group' => 'reporters', 'name' => 'add_reporters', 'title' => 'Add reporters', 'guard_name' => 'web'],
            ['group' => 'reporters', 'name' => 'edit_reporters', 'title' => 'Edit reporters', 'guard_name' => 'web'],
            ['group' => 'reporters', 'name' => 'delete_reporters', 'title' => 'Delete reporters', 'guard_name' => 'web'],

            // remote assistants
            ['group' => 'remote_assistants', 'name' => 'view_remote_assistants', 'title' => 'View remote assistants', 'guard_name' => 'web'],
            ['group' => 'remote_assistants', 'name' => 'add_remote_assistants', 'title' => 'Add remote assistants', 'guard_name' => 'web'],
            ['group' => 'remote_assistants', 'name' => 'edit_remote_assistants', 'title' => 'Edit remote assistants', 'guard_name' => 'web'],
            ['group' => 'remote_assistants', 'name' => 'delete_remote_assistants', 'title' => 'Delete remote assistants', 'guard_name' => 'web'],

            // languages
            ['group' => 'languages', 'name' => 'view_languages', 'title' => 'View languages', 'guard_name' => 'web'],
            ['group' => 'languages', 'name' => 'add_languages', 'title' => 'Add languages', 'guard_name' => 'web'],
            ['group' => 'languages', 'name' => 'edit_languages', 'title' => 'Edit languages', 'guard_name' => 'web'],
            ['group' => 'languages', 'name' => 'delete_languages', 'title' => 'Delete languages', 'guard_name' => 'web'],

            // settings
            ['group' => 'settings', 'name' => 'view_settings', 'title' => 'View settings', 'guard_name' => 'web'],
            ['group' => 'settings', 'name' => 'add_settings', 'title' => 'Add settings', 'guard_name' => 'web'],
            ['group' => 'settings', 'name' => 'edit_settings', 'title' => 'Edit settings', 'guard_name' => 'web'],
            ['group' => 'settings', 'name' => 'delete_settings', 'title' => 'Delete settings', 'guard_name' => 'web'],

            // drivers_manager
            ['group' => 'drivers_manager', 'name' => 'view_drivers_manager', 'title' => 'View drivers manager', 'guard_name' => 'web'],
            ['group' => 'drivers_manager', 'name' => 'add_drivers_manager', 'title' => 'Add drivers manager', 'guard_name' => 'web'],
            ['group' => 'drivers_manager', 'name' => 'edit_drivers_manager', 'title' => 'Edit drivers manager', 'guard_name' => 'web'],
            ['group' => 'drivers_manager', 'name' => 'delete_drivers_manager', 'title' => 'Delete drivers manager', 'guard_name' => 'web'],

            // drivers
            ['group' => 'drivers', 'name' => 'view_drivers', 'title' => 'View drivers', 'guard_name' => 'web'],
            ['group' => 'drivers', 'name' => 'add_drivers', 'title' => 'Add drivers', 'guard_name' => 'web'],
            ['group' => 'drivers', 'name' => 'edit_drivers', 'title' => 'Edit drivers', 'guard_name' => 'web'],
            ['group' => 'drivers', 'name' => 'delete_drivers', 'title' => 'Delete drivers', 'guard_name' => 'web'],

            // drivers group
            ['group' => 'drivers_group', 'name' => 'view_drivers_group', 'title' => 'View drivers group', 'guard_name' => 'web'],
            ['group' => 'drivers_group', 'name' => 'add_drivers_group', 'title' => 'Add drivers group', 'guard_name' => 'web'],
            ['group' => 'drivers_group', 'name' => 'edit_drivers_group', 'title' => 'Edit drivers group', 'guard_name' => 'web'],
            ['group' => 'drivers_group', 'name' => 'delete_drivers_group', 'title' => 'Delete drivers group', 'guard_name' => 'web'],

            // business
            ['group' => 'business', 'name' => 'view_business', 'title' => 'View business', 'guard_name' => 'web'],
            ['group' => 'business', 'name' => 'add_business', 'title' => 'Add business', 'guard_name' => 'web'],
            ['group' => 'business', 'name' => 'edit_business', 'title' => 'Edit business', 'guard_name' => 'web'],
            ['group' => 'business', 'name' => 'delete_business', 'title' => 'Delete business', 'guard_name' => 'web'],
            ['group' => 'business', 'name' => 'approve_business', 'title' => 'Approve business', 'guard_name' => 'web'],

            // business categories
            ['group' => 'global_tags', 'name' => 'view_global_tags', 'title' => 'View global tags', 'guard_name' => 'web'],
            ['group' => 'global_tags', 'name' => 'add_global_tags', 'title' => 'Add global tags', 'guard_name' => 'web'],
            ['group' => 'global_tags', 'name' => 'edit_global_tags', 'title' => 'Edit global tags', 'guard_name' => 'web'],
            ['group' => 'global_tags', 'name' => 'delete_global_tags', 'title' => 'Delete global tags', 'guard_name' => 'web'],

            // business Schedule Time
            ['group' => 'business_schedule_time', 'name' => 'view_business_schedule_time', 'title' => 'View business schedule time', 'guard_name' => 'web'],
            ['group' => 'business_schedule_time', 'name' => 'add_business_schedule_time', 'title' => 'Add business schedule time', 'guard_name' => 'web'],
            ['group' => 'business_schedule_time', 'name' => 'edit_business_schedule_time', 'title' => 'Edit business schedule time', 'guard_name' => 'web'],
            ['group' => 'business_schedule_time', 'name' => 'delete_business_schedule_time', 'title' => 'Delete business schedule time', 'guard_name' => 'web'],

            // Delivery Zones
            ['group' => 'delivery_zones', 'name' => 'view_delivery_zones', 'title' => 'View delivery zones', 'guard_name' => 'web'],
            ['group' => 'delivery_zones', 'name' => 'add_delivery_zones', 'title' => 'Add delivery zones', 'guard_name' => 'web'],
            ['group' => 'delivery_zones', 'name' => 'edit_delivery_zones', 'title' => 'Edit delivery zones', 'guard_name' => 'web'],
            ['group' => 'delivery_zones', 'name' => 'delete_delivery_zones', 'title' => 'Delete delivery zones', 'guard_name' => 'web'],

            // business settings
            ['group' => 'business_settings', 'name' => 'view_business_settings', 'title' => 'View business settings', 'guard_name' => 'web'],
            ['group' => 'business_settings', 'name' => 'add_business_settings', 'title' => 'Add business settings', 'guard_name' => 'web'],
            ['group' => 'business_settings', 'name' => 'edit_business_settings', 'title' => 'Edit business settings', 'guard_name' => 'web'],
            ['group' => 'business_settings', 'name' => 'delete_business_settings', 'title' => 'Delete business settings', 'guard_name' => 'web'],

            // business mailings
            ['group' => 'business_mailings', 'name' => 'view_business_mailings', 'title' => 'View business mailings', 'guard_name' => 'web'],
            ['group' => 'business_mailings', 'name' => 'add_business_mailings', 'title' => 'Add business mailings', 'guard_name' => 'web'],
            ['group' => 'business_mailings', 'name' => 'edit_business_mailings', 'title' => 'Edit business mailings', 'guard_name' => 'web'],
            ['group' => 'business_mailings', 'name' => 'delete_business_mailings', 'title' => 'Delete business mailings', 'guard_name' => 'web'],

            // business coupons
            ['group' => 'business_coupons', 'name' => 'view_business_coupons', 'title' => 'View business coupons', 'guard_name' => 'web'],
            ['group' => 'business_coupons', 'name' => 'add_business_coupons', 'title' => 'Add business coupons', 'guard_name' => 'web'],
            ['group' => 'business_coupons', 'name' => 'edit_business_coupons', 'title' => 'Edit business coupons', 'guard_name' => 'web'],
            ['group' => 'business_coupons', 'name' => 'delete_business_coupons', 'title' => 'Delete business coupons', 'guard_name' => 'web'],

            // business product categories
            ['group' => 'product_categories', 'name' => 'view_product_categories', 'title' => 'View product categories', 'guard_name' => 'web'],
            ['group' => 'product_categories', 'name' => 'add_product_categories', 'title' => 'Add product categories', 'guard_name' => 'web'],
            ['group' => 'product_categories', 'name' => 'edit_product_categories', 'title' => 'Edit product categories', 'guard_name' => 'web'],
            ['group' => 'product_categories', 'name' => 'delete_product_categories', 'title' => 'Delete product categories', 'guard_name' => 'web'],

            // Roles
            ['group' => 'roles', 'name' => 'view_roles', 'title' => 'View Roles', 'guard_name' => 'web'],
            ['group' => 'roles', 'name' => 'add_roles', 'title' => 'Add Roles', 'guard_name' => 'web'],
            ['group' => 'roles', 'name' => 'edit_roles', 'title' => 'Edit Roles', 'guard_name' => 'web'],
            ['group' => 'roles', 'name' => 'delete_roles', 'title' => 'Delete Roles', 'guard_name' => 'web'],

            // Products
            ['group' => 'products', 'name' => 'view_products', 'title' => 'View Products', 'guard_name' => 'web'],
            ['group' => 'products', 'name' => 'add_products', 'title' => 'Add Products', 'guard_name' => 'web'],
            ['group' => 'products', 'name' => 'edit_products', 'title' => 'Edit Products', 'guard_name' => 'web'],
            ['group' => 'products', 'name' => 'delete_products', 'title' => 'Delete Products', 'guard_name' => 'web'],

            // Additional Emails
            ['group' => 'additional_emails', 'name' => 'view_additional_emails', 'title' => 'View Additional Emails', 'guard_name' => 'web'],
            ['group' => 'additional_emails', 'name' => 'add_additional_emails', 'title' => 'Add Additional Emails', 'guard_name' => 'web'],
            ['group' => 'additional_emails', 'name' => 'edit_additional_emails', 'title' => 'Edit Additional Emails', 'guard_name' => 'web'],
            ['group' => 'additional_emails', 'name' => 'delete_additional_emails', 'title' => 'Delete Additional Emails', 'guard_name' => 'web'],

            // product coupons
            ['group' => 'product_coupons', 'name' => 'view_product_coupons', 'title' => 'View Product Coupons', 'guard_name' => 'web'],
            ['group' => 'product_coupons', 'name' => 'add_product_coupons', 'title' => 'Add Product Coupons', 'guard_name' => 'web'],
            ['group' => 'product_coupons', 'name' => 'edit_product_coupons', 'title' => 'Edit Product Coupons', 'guard_name' => 'web'],
            ['group' => 'product_coupons', 'name' => 'delete_product_coupons', 'title' => 'Delete Product Coupons', 'guard_name' => 'web'],

            // product variants
            ['group' => 'product_variants', 'name' => 'view_product_variants', 'title' => 'View Product Variants', 'guard_name' => 'web'],
            ['group' => 'product_variants', 'name' => 'add_product_variants', 'title' => 'Add Product Variants', 'guard_name' => 'web'],
            ['group' => 'product_variants', 'name' => 'edit_product_variants', 'title' => 'Edit Product Variants', 'guard_name' => 'web'],
            ['group' => 'product_variants', 'name' => 'delete_product_variants', 'title' => 'Delete Product Variants', 'guard_name' => 'web'],

            // product sizes
            ['group' => 'product_sizes', 'name' => 'view_product_sizes', 'title' => 'View Product Sizes', 'guard_name' => 'web'],
            ['group' => 'product_sizes', 'name' => 'add_product_sizes', 'title' => 'Add Product Sizes', 'guard_name' => 'web'],
            ['group' => 'product_sizes', 'name' => 'edit_product_sizes', 'title' => 'Edit Product Sizes', 'guard_name' => 'web'],
            ['group' => 'product_sizes', 'name' => 'delete_product_sizes', 'title' => 'Delete Product Sizes', 'guard_name' => 'web'],

            // Reviews
            ['group' => 'reviews', 'name' => 'view_reviews', 'title' => 'View Reviews', 'guard_name' => 'web'],
            ['group' => 'reviews', 'name' => 'add_reviews', 'title' => 'Add Reviews', 'guard_name' => 'web'],
            ['group' => 'reviews', 'name' => 'edit_reviews', 'title' => 'Edit Reviews', 'guard_name' => 'web'],
            ['group' => 'reviews', 'name' => 'delete_reviews', 'title' => 'Delete Reviews', 'guard_name' => 'web'],

            // product images
            ['group' => 'product_images', 'name' => 'view_product_images', 'title' => 'View Product Images', 'guard_name' => 'web'],
            ['group' => 'product_images', 'name' => 'add_product_images', 'title' => 'Add Product Images', 'guard_name' => 'web'],
            ['group' => 'product_images', 'name' => 'edit_product_images', 'title' => 'Edit Product Images', 'guard_name' => 'web'],
            ['group' => 'product_images', 'name' => 'delete_product_images', 'title' => 'Delete Product Images', 'guard_name' => 'web'],

            // product colors
            ['group' => 'product_colors', 'name' => 'view_product_colors', 'title' => 'View Product Colors', 'guard_name' => 'web'],
            ['group' => 'product_colors', 'name' => 'add_product_colors', 'title' => 'Add Product Colors', 'guard_name' => 'web'],
            ['group' => 'product_colors', 'name' => 'edit_product_colors', 'title' => 'Edit Product Colors', 'guard_name' => 'web'],
            ['group' => 'product_colors', 'name' => 'delete_product_colors', 'title' => 'Delete Product Colors', 'guard_name' => 'web'],

            // Orders
            ['group' => 'orders', 'name' => 'view_orders', 'title' => 'View Orders', 'guard_name' => 'web'],
            ['group' => 'orders', 'name' => 'add_orders', 'title' => 'Add Orders', 'guard_name' => 'web'],
            ['group' => 'orders', 'name' => 'edit_orders', 'title' => 'Edit Orders', 'guard_name' => 'web'],
            ['group' => 'orders', 'name' => 'delete_orders', 'title' => 'Delete Orders', 'guard_name' => 'web'],

            // News
            ['group' => 'news', 'name' => 'view_news', 'title' => 'View News', 'guard_name' => 'web'],
            ['group' => 'news', 'name' => 'add_news', 'title' => 'Add News', 'guard_name' => 'web'],
            ['group' => 'news', 'name' => 'edit_news', 'title' => 'Edit News', 'guard_name' => 'web'],
            ['group' => 'news', 'name' => 'delete_news', 'title' => 'Delete News', 'guard_name' => 'web'],

            // News Categories
            ['group' => 'news_categories', 'name' => 'view_news_categories', 'title' => 'View News Categories', 'guard_name' => 'web'],
            ['group' => 'news_categories', 'name' => 'add_news_categories', 'title' => 'Add News Categories', 'guard_name' => 'web'],
            ['group' => 'news_categories', 'name' => 'edit_news_categories', 'title' => 'Edit News Categories', 'guard_name' => 'web'],
            ['group' => 'news_categories', 'name' => 'delete_news_categories', 'title' => 'Delete News Categories', 'guard_name' => 'web'],

            // retail categoires mapper
            ['group' => 'categories_mapper', 'name' => 'view_categories_mapper', 'title' => 'View Categories Mapper', 'guard_name' => 'web'],
            ['group' => 'categories_mapper', 'name' => 'add_categories_mapper', 'title' => 'Add Categories Mapper', 'guard_name' => 'web'],
            ['group' => 'categories_mapper', 'name' => 'edit_categories_mapper', 'title' => 'Edit Categories Mapper', 'guard_name' => 'web'],
            ['group' => 'categories_mapper', 'name' => 'delete_categories_mapper', 'title' => 'Delete Categories Mapper', 'guard_name' => 'web'],

            // business owner admin settings
            ['group' => 'admin_settings', 'name' => 'view_admin_settings', 'title' => 'View Admin Settings', 'guard_name' => 'web'],
            ['group' => 'admin_settings', 'name' => 'add_admin_settings', 'title' => 'Add Admin Settings', 'guard_name' => 'web'],
            ['group' => 'admin_settings', 'name' => 'edit_admin_settings', 'title' => 'Edit Admin Settings', 'guard_name' => 'web'],
            ['group' => 'admin_settings', 'name' => 'delete_admin_settings', 'title' => 'Delete Admin Settings', 'guard_name' => 'web'],

            // user admin settings
            ['group' => 'user_settings', 'name' => 'view_user_settings', 'title' => 'View User Settings', 'guard_name' => 'web'],
            ['group' => 'user_settings', 'name' => 'add_user_settings', 'title' => 'Add User Settings', 'guard_name' => 'web'],
            ['group' => 'user_settings', 'name' => 'edit_user_settings', 'title' => 'Edit User Settings', 'guard_name' => 'web'],
            ['group' => 'user_settings', 'name' => 'delete_user_settings', 'title' => 'Delete User Settings', 'guard_name' => 'web'],

            // standard tag
            ['group' => 'tags', 'name' => 'view_standard_tag', 'title' => 'View Standard Tag', 'guard_name' => 'web'],
            ['group' => 'tags', 'name' => 'add_standard_tag', 'title' => 'Add Standard Tag', 'guard_name' => 'web'],
            ['group' => 'tags', 'name' => 'edit_standard_tag', 'title' => 'Edit Standard Tag', 'guard_name' => 'web'],
            ['group' => 'tags', 'name' => 'delete_standard_tag', 'title' => 'Delete Standard Tag', 'guard_name' => 'web'],

            // orphan tags mapper
            ['group' => 'tags_mapper', 'name' => 'view_tags_mapper', 'title' => 'View Tags Mapper', 'guard_name' => 'web'],
            ['group' => 'tags_mapper', 'name' => 'add_tags_mapper', 'title' => 'Add Tags Mapper', 'guard_name' => 'web'],
            ['group' => 'tags_mapper', 'name' => 'edit_tags_mapper', 'title' => 'Edit Tags Mapper', 'guard_name' => 'web'],
            ['group' => 'tags_mapper', 'name' => 'delete_tags_mapper', 'title' => 'Delete Tags Mapper', 'guard_name' => 'web'],

            // tag hierarchies
            ['group' => 'tag_hierarchies', 'name' => 'view_tag_hierarchies', 'title' => 'View Tags hierarchies', 'guard_name' => 'web'],
            ['group' => 'tag_hierarchies', 'name' => 'add_tag_hierarchies', 'title' => 'Add Tags hierarchies', 'guard_name' => 'web'],
            ['group' => 'tag_hierarchies', 'name' => 'edit_tag_hierarchies', 'title' => 'Edit Tags hierarchies', 'guard_name' => 'web'],
            ['group' => 'tag_hierarchies', 'name' => 'delete_tag_hierarchies', 'title' => 'Delete Tags hierarchies', 'guard_name' => 'web'],

            // Industry tags
            ['group' => 'industry_tags', 'name' => 'view_industry_tag', 'title' => 'View Industry Tag', 'guard_name' => 'web'],
            ['group' => 'industry_tags', 'name' => 'add_industry_tag', 'title' => 'Add Industry Tag', 'guard_name' => 'web'],
            ['group' => 'industry_tags', 'name' => 'edit_industry_tag', 'title' => 'Edit Industry Tag', 'guard_name' => 'web'],
            ['group' => 'industry_tags', 'name' => 'delete_industry_tag', 'title' => 'Delete Industry Tag', 'guard_name' => 'web'],
            // Attributes tags
            ['group' => 'attributes', 'name' => 'view_attributes', 'title' => 'View attributes', 'guard_name' => 'web'],
            ['group' => 'attributes', 'name' => 'add_attributes', 'title' => 'Add attributes', 'guard_name' => 'web'],
            ['group' => 'attributes', 'name' => 'edit_attributes', 'title' => 'Edit attributes', 'guard_name' => 'web'],
            ['group' => 'attributes', 'name' => 'delete_attributes', 'title' => 'Delete attributes', 'guard_name' => 'web'],

            //Payment Method
            ['group' => 'payment_method', 'name' => 'view_payment_method', 'title' => 'View payment method', 'guard_name' => 'web'],
            ['group' => 'payment_method', 'name' => 'add_payment_method', 'title' => 'Add payment method', 'guard_name' => 'web'],
            ['group' => 'payment_method', 'name' => 'edit_payment_method', 'title' => 'Edit payment method', 'guard_name' => 'web'],
            ['group' => 'payment_method', 'name' => 'delete_payment_method', 'title' => 'Delete payment method', 'guard_name' => 'web'],

            //Subscription Plan
            ['group' => 'subscription_plan', 'name' => 'view_subscription_plan', 'title' => 'View subscription plan', 'guard_name' => 'web'],
            ['group' => 'subscription_plan', 'name' => 'add_subscription_plan', 'title' => 'Add subscription plan', 'guard_name' => 'web'],
            ['group' => 'subscription_plan', 'name' => 'edit_subscription_plan', 'title' => 'Edit subscription plan', 'guard_name' => 'web'],
            ['group' => 'subscription_plan', 'name' => 'delete_subscription_plan', 'title' => 'Delete subscription plan', 'guard_name' => 'web'],

            //Subscription
            ['group' => 'subscription', 'name' => 'view_subscription', 'title' => 'View subscription', 'guard_name' => 'web'],
            ['group' => 'subscription', 'name' => 'add_subscription', 'title' => 'Add subscription', 'guard_name' => 'web'],
            ['group' => 'subscription', 'name' => 'edit_subscription', 'title' => 'Edit subscription', 'guard_name' => 'web'],
            ['group' => 'subscription', 'name' => 'delete_subscription', 'title' => 'Delete subscription', 'guard_name' => 'web'],

            // Store extra Tags
            ['group' => 'extra_tags', 'name' => 'view_extra_tags', 'title' => 'View extra tags', 'guard_name' => 'web'],
            ['group' => 'extra_tags', 'name' => 'add_extra_tags', 'title' => 'Add extra tags', 'guard_name' => 'web'],
            ['group' => 'extra_tags', 'name' => 'edit_extra_tags', 'title' => 'Edit extra tags', 'guard_name' => 'web'],
            ['group' => 'extra_tags', 'name' => 'delete_extra_tags', 'title' => 'Delete extra tags', 'guard_name' => 'web'],

            // vehicle contact form
            ['group' => 'contact_form', 'name' => 'view_contact_form', 'title' => 'View contact form', 'guard_name' => 'web'],
            ['group' => 'contact_form', 'name' => 'add_contact_form', 'title' => 'Add contact form', 'guard_name' => 'web'],
            ['group' => 'contact_form', 'name' => 'edit_contact_form', 'title' => 'Edit contact form', 'guard_name' => 'web'],
            ['group' => 'contact_form', 'name' => 'delete_contact_form', 'title' => 'Delete contact form', 'guard_name' => 'web'],

            // comment form
            ['group' => 'comment', 'name' => 'view_comment', 'title' => 'View comment', 'guard_name' => 'web'],
            ['group' => 'comment', 'name' => 'delete_comment', 'title' => 'Delete comment', 'guard_name' => 'web'],

            
            //goverment staff
            ['group' => 'government_staff', 'name' => 'view_government_staff', 'title' => 'View government staff', 'guard_name' => 'web'],
            ['group' => 'government_staff', 'name' => 'add_government_staff', 'title' => 'Add government staff', 'guard_name' => 'web'],
            ['group' => 'government_staff', 'name' => 'edit_government_staff', 'title' => 'Edit government staff', 'guard_name' => 'web'],
            ['group' => 'government_staff', 'name' => 'delete_government_staff', 'title' => 'Delete government staff', 'guard_name' => 'web'],

            // agents
            ['group' => 'agents', 'name' => 'view_agents', 'title' => 'View Agents', 'guard_name' => 'web'],
            ['group' => 'agents', 'name' => 'add_agents', 'title' => 'Add Agents', 'guard_name' => 'web'],
            ['group' => 'agents', 'name' => 'edit_agents', 'title' => 'Edit Agents', 'guard_name' => 'web'],
            ['group' => 'agents', 'name' => 'delete_agents', 'title' => 'Delete Agents', 'guard_name' => 'web'],

            // headlines
            ['group' => 'headlines', 'name' => 'view_headlines', 'title' => 'View Headlines', 'guard_name' => 'web'],
            ['group' => 'headlines', 'name' => 'add_headlines', 'title' => 'Add Headlines', 'guard_name' => 'web'],
            ['group' => 'headlines', 'name' => 'edit_headlines', 'title' => 'Edit Headlines', 'guard_name' => 'web'],
            ['group' => 'headlines', 'name' => 'delete_headlines', 'title' => 'Delete Headlines', 'guard_name' => 'web'],
            
            ['group' => 'chat_settings', 'name' => 'view_chat_settings', 'title' => 'View Chat Settings', 'guard_name' => 'web'],
            ['group' => 'chat_settings', 'name' => 'add_chat_settings', 'title' => 'Add Chat Settings', 'guard_name' => 'web'],
            ['group' => 'chat_settings', 'name' => 'edit_chat_settings', 'title' => 'Edit Chat Settings', 'guard_name' => 'web'],
            ['group' => 'chat_settings', 'name' => 'delete_chat_settings', 'title' => 'Delete Chat Settings', 'guard_name' => 'web'],
            
        ];

        Permission::insert($permissions);
        //Admin
        $admin = Role::updateOrCreate(['name' => 'admin'], ['title' => 'Admin']);
        $adminPermissions = Permission::whereNotIn('group', [
            'subscription', 'payment_method'
        ])->get();
        $admin->permissions()->sync($adminPermissions);

        //Newspaper
        $newsPaper = Role::updateOrCreate(['name' => 'newspaper'], ['title' => 'newspaper']);
        $newsPaperPermissions = Permission::whereNotIn('group', [
            'administrators', 'languages'
        ])->get();
        $newsPaper->permissions()->sync($newsPaperPermissions);

        //Business Owner
        $business_owner = Role::updateOrCreate(
            ['name' => 'business_owner'],
            ['title' => 'Business Owner']
        );
        $businessOwnerPermissions = Permission::whereNotIn(
            'group',
            [
                'orders',
                'administrators',
                'languages',
                'tags',
                'settings',
                'drivers_group',
                'drivers',
                'drivers_manager',
                'customers',
                'reporters',
                'remote_assistants',
                'business_owners',
                'admin_settings',
                'reviews',
                'tags',
                'industry_tags',
                'subscription_plan',
                'tag_hierarchies',
                'headlines',
                'chat_settings'
            ]
        )->where('name', '!=', 'approve_business')
        ->orWhere(function ($query) {
            $query->where('group', 'orders')->where('name', 'view_orders');
            $query->orWhere('group', 'orders')->where('name', 'edit_orders');
            $query->orWhere('group', 'reviews')->where('name', 'view_reviews');
        })->get();
        $business_owner->permissions()->sync($businessOwnerPermissions);

        //Customer
        $customer = Role::updateOrCreate(['name' => 'customer'], ['title' => 'Customer']);
        $customerPermissions = Permission::whereIn('group', [
            'business',
            'business_schedule_time',
            'business_settings',
            'business_mailing',
            'products',
            'additional_emails',
            'product_variants',
            'product_sizes',
            'reviews',
            'product_images',
            'product_colors',
            'orders',
            'payment_method',
            'subscription',
            'attributes',
            'view_user_settings',
        ])->where('name', '!=', 'approve_business')->get();
        $customer->permissions()->sync($customerPermissions);

        //Reporter
        $reporter = Role::updateOrCreate(['name' => 'reporter'], ['title' => 'Reporter']);
        $reporterPermissions = Permission::whereIn('group', ['products', 'product_images'])
            ->orWhere(function ($query) {
                $query->orWhere('group', 'product_coupons')->where('name', 'view_product_coupons');
                $query->orWhere('group', 'subscription')->where('name', 'view_subscription');
                $query->orWhere('group', 'payment_method')->where('name', 'view_payment_method');
            })->get();
        $reporter->permissions()->sync($reporterPermissions);

        //Remote Assistance
        $remoteAssistance = Role::updateOrCreate(
            ['name' => 'remote_assistant'],
            ['title' => 'remote Assistant']
        );
        $remoteAssistancePermissions = Permission::whereNotIn(
            'group',
            [
                'settings',
                'drivers_group',
                'customers',
                'drivers',
                'drivers_manager',
                'business_owners',
                'languages',
                'business',
                'reporters',
                'additional_emails',
                'business_mailings',
                'orders',
                'reviews',
                'tags',
                'industry_tags',
                'subscription_plan',
                'headlines',
                'chat_settings'
            ]
        )->where('name', '!=', 'approve_business')
        ->orWhere(function ($query) {
            $query->where('group', 'orders')->where('name', 'view_orders');
            $query->orWhere('group', 'reviews')->where('name', 'view_reviews');
        })->get();
        $remoteAssistance->permissions()->sync($remoteAssistancePermissions);

        //Driver Manager
        $driver_manager = Role::updateOrCreate(['name' => 'driver_manager'], [
            'title' => 'Driver Manager'
        ]);

        //Driver
        $driver = Role::updateOrCreate(['name' => 'driver'], ['title' => 'Driver']);

        // Government Staff
        $governmentStaff = Role::updateOrCreate(
            ['name' => 'government_staff'],
            ['title' => 'Government Staff']
        );
        $governmentStaffPermissions = Permission::whereIn('group', ['products', 'product_images', 'product_colors', 'product_sizes', 'product_coupons', 'product_categories'])->get();
        $governmentStaff->permissions()->sync($governmentStaffPermissions);

        // Agent => Real Estate Module
        $agent = Role::updateOrCreate(
            ['name' => 'agent'],
            ['title' => 'Agent']
        );

        $agentPermissions = Permission::whereIn('group', ['products', 'product_images', 'product_colors', 'product_sizes', 'product_coupons', 'product_categories'])->get();
        $agent->permissions()->sync($agentPermissions);
    }
}
