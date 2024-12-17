<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->truncate();

        Setting::insert([
            [
                'group' => 'number_format_settings',
                'type' => null,
                'key' => 'decimal_length',
                'name' => 'Decimal Length',
                'value' => 2,
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'number_format_settings',
                'type' => null,
                'name' => 'Decimal Separator',
                'key' => 'decimal_separator',
                'value' => '.',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'time_format_settings',
                'type' => null,
                'name' => 'Time Format',
                'key' => 'time_format',
                'value' => '12 hours',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'tax_model_settings',
                'type' => null,
                'name' => 'Tax Model',
                'key' => 'tax_model',
                'value' => 'Tax included on price',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'stripe_connect_settings',
                'type' => null,
                'name' => 'Sandbox',
                'key' => 'sandbox',
                'value' => 'no',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'stripe_connect_settings',
                'type' => null,
                'name' => 'Client ID Sandbox',
                'key' => 'client_id_sandbox',
                'is_required' => null,
                'value' => 'pk_test_51NTJzJKlsypZe3F2Z997b0lxZJALdairiAmr9wgpoFwczZa9vJqpvMZD9GCodSrOOQMxYEzOZ1X17WS2D18bMgYl00YB1U8yfG',
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'stripe_connect_settings',
                'type' => null,
                'name' => 'Client Secret Sandbox',
                'key' => 'client_secret_sandbox',
                'value' => 'sk_test_51NTJzJKlsypZe3F2Up71xzRmhtWEgfJg2hcjFLs9pXHrP7FVD51vFHyB6RIYWYCBzZTxME7S8ly4moAgdE4K0aBh00tjceaU7k',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'stripe_connect_settings',
                'type' => null,
                'name' => 'Stripe Webhook Secret Sandbox',
                'key' => 'stripe_webhook_secret_sandbox',
                'value' => 'whsec_KyhJLYWPy2D2gR0aK9BHxLvpXrHtesfV',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'stripe_connect_settings',
                'type' => null,
                'name' => 'Client ID Production',
                'key' => 'client_id_production',
                'is_required' => null,
                'value' => 'pk_test_51NTJzJKlsypZe3F2Z997b0lxZJALdairiAmr9wgpoFwczZa9vJqpvMZD9GCodSrOOQMxYEzOZ1X17WS2D18bMgYl00YB1U8yfG',
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'stripe_connect_settings',
                'type' => null,
                'name' => 'Client secret Production',
                'key' => 'client_secret_production',
                'value' => 'sk_test_51NTJzJKlsypZe3F2Up71xzRmhtWEgfJg2hcjFLs9pXHrP7FVD51vFHyB6RIYWYCBzZTxME7S8ly4moAgdE4K0aBh00tjceaU7k',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'stripe_connect_settings',
                'type' => null,
                'name' => 'Stripe Webhook Secret Production',
                'key' => 'stripe_webhook_secret_production',
                'value' => 'whsec_KyhJLYWPy2D2gR0aK9BHxLvpXrHtesfV',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'driver_assignment_settings',
                'type' => null,
                'name' => 'Autoassign Type',
                'key' => 'autoassign_type',
                'value' => 'basic',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'driver_assignment_settings',
                'type' => null,
                'name' => 'Max Order Assignments',
                'key' => 'max_order_assignments',
                'value' => '50',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'driver_assignment_settings',
                'type' => null,
                'name' => 'Enable Auto Assignment',
                'key' => 'enable_auto_assignment',
                'value' => 'no',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'driver_assignment_settings',
                'type' => null,
                'name' => 'Auto assignment Max Distance (KM)',
                'key' => 'auto_assignment_max_distance',
                'value' => '30',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'driver_assignment_settings',
                'type' => null,
                'name' => 'Start Process In Status',
                'key' => 'start_process_in_status',
                'value' => 'ready for pickup',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'Name',
                'key' => 'name',
                'value' => 'name',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'Middle Name',
                'key' => 'middle_name',
                'value' => 'middle_name',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'Last Name',
                'key' => 'last_name',
                'value' => 'last_name',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'Neighborhood Name',
                'key' => 'neighborhood_name',
                'value' => 'neighborhood_name',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'Email',
                'key' => 'email',
                'value' => 'email',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'Mobile Phone',
                'key' => 'mobile_phone',
                'value' => 'mobile_phone',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'City And Zone Dropdowns',
                'key' => 'city_zone_dropdowns',
                'value' => 'city_zone_dropdowns',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'Address',
                'key' => 'address',
                'value' => 'address',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'Zipcode',
                'key' => 'zipcode',
                'value' => 'zipcode',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'Address Notes',
                'key' => 'address_notes',
                'value' => 'address_notes',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'Coupon',
                'key' => 'coupon',
                'value' => 'coupon',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'checkout_fields_settings',
                'type' => null,
                'name' => 'Driver Tip',
                'key' => 'driver_tip',
                'value' => 'driver_tip',
                'is_required' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'social_authentication',
                'type' => 'Facebook',
                'key' => 'enable_facebook',
                'name' => 'Enable Facebook',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'social_authentication',
                'type' => 'Facebook',
                'key' => 'application_id',
                'name' => 'Application ID',
                'value' => 'app-id-facebooktestkey1645',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'social_authentication',
                'type' => 'Facebook',
                'key' => 'application_secret',
                'name' => 'Application Secret',
                'value' => 'app-secret-facebooktestkeyk67464',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'social_authentication',
                'type' => 'Google',
                'key' => 'enable_google',
                'name' => 'Enable Google',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'social_authentication',
                'type' => 'Google',
                'key' => 'application_id',
                'name' => 'Application ID',
                'value' => '984211860678-kqqbe3ibfc6auucjuachc51k7v4e2shq.apps.googleusercontent.com',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'social_authentication',
                'type' => 'Google',
                'key' => 'application_secret',
                'name' => 'Application Secret',
                'value' => 'app-secret-googletestkey12345',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'group' => 'frontend_settings',
                'type' => null,
                'name' => 'Newspaper Logo',
                'key' => 'newspaper_logo',
                'value' => null,
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
        ]);

        // email settings and Push Notifications
        $groups = [
            'email_notification',
            'push_notification'
        ];

        $types = [
            'General',
            'Adminstrator',
            'Business Owners',
            'Customer',
            'Reporters',
        ];

        $settings = [
            [
                'key' => 'completed',
                'name' => 'Completed',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'rejected',
                'name' => 'Rejected',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'driver_arrived_to_Business',
                'name' => 'Driver Arrived To Business',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'ready_for_pickup',
                'name' => 'Ready for Pickup',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'rejected_by_business',
                'name' => 'Rejected by Business',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'rejected_by_driver',
                'name' => 'Rejected by Driver',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'accepted_by_business',
                'name' => 'Accepted by Business',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'accepted_by_driver',
                'name' => 'Accepted by Driver',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'pickup_completed_by_driver',
                'name' => 'Pickup Completed by Driver',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'pickup_failed_by_driver',
                'name' => 'Pickup Failed by Driver',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'delivery_completed_by_driver',
                'name' => 'Delivery Completed by Driver',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'delievery_failed_by_driver',
                'name' => 'Delivery Failed by Driver',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'new_business_owner_signup',
                'name' => 'New Business Owner Signup',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ]
        ];

        $generalForEmail = [
            [
                'key' => 'email_from_name',
                'name' => 'Email From Name',
                'value' => 'Hoopla',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'reply_to_name',
                'name' => 'Reply to Name',
                'value' => 'Hoopla',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'reply_to_email',
                'name' => 'Reply to Email',
                'value' => 'admin@hooplabuzz.com',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'mail_mailer',
                'name' => 'Mail Mailer',
                'value' => 'smtp',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'mail_host',
                'name' => 'Mail Host',
                'value' => 'smtp.mailtrap.io',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'mail_port',
                'name' => 'Mail Port',
                'value' => '2525',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'mail_username',
                'name' => 'Mail Username',
                'value' => 'a09ebb33bf5272',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'mail_password',
                'name' => 'Mail Password',
                'value' => 'e3c216d88f7d97',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'encryption_type',
                'name' => 'Encryption Type',
                'value' => 'tls',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
        ];

        $generalForNotification = [
            [
                'key' => 'enalble_push_notifications',
                'name' => 'Enable push notifications',
                'value' => '1',
                'is_required' => null,
                'created_at' => Carbon::now()
            ],
            [
                'key' => 'firebase_config_file',
                'name' => 'Firebase config file',
                'value' => '',
                'is_required' => null,
                'created_at' => Carbon::now()
            ]
        ];


        foreach ($groups as $group) {
            foreach ($types as $type) {
                if ($type == 'General') {
                    // general data stored
                    if ($group == 'email_notification') {
                        foreach ($generalForEmail as $setting) {
                            $setting = array_merge($setting, ['type' => $type, 'group' => $group]);
                            Setting::create($setting);
                        }
                    } else if ($group == 'push_notification') {
                        foreach ($generalForNotification as $setting) {
                            $setting = array_merge($setting, ['type' => $type, 'group' => $group]);
                            Setting::create($setting);
                        }
                    } else {
                    }
                } else {
                    // other types data stored
                    foreach ($settings as $setting) {
                        $setting = array_merge($setting, ['type' => $type, 'group' => $group]);
                        Setting::create($setting);
                    }
                }
            }
        }
    }
}
