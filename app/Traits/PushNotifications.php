<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\Setting;
use App\Enums\OrderStatus;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\RawMessageFromArray;

trait PushNotifications
{
    public function orderNotification($order)
    {
        $orderStatus = $order->orderTracking()->latest()->first();
        switch ($orderStatus->order_status_id) {
            case OrderStatus::Pending:
                $title = 'Order Placed';
                $message = 'Your order has been placed';
                break;

            case $orderStatus->order_status_id != 1:
                $title = 'Order Status Updated';
                $message = 'Your order has been ' . self::messageBasedOnStatus($orderStatus->order_status_id);
                break;
            default:
                return true;
        }
        $devices = $order->model->devices()->orderBy('id', 'desc')->get();
        $notification = [
            'title' => $title,
            'message' => $message
        ];
        $orderPayload = [
            'order_id' => (string)$order->id,
            'order_uuid' => (string)$order->uuid
        ];
        self::sendCloudMessage($devices, $notification, $orderPayload);
    }

    private function messageBasedOnStatus($orderStatusId)
    {
        return  strtolower(OrderStatus::getDescription($orderStatusId));
    }

    public function sendCloudMessage($devices, $notification, $payload = [])
    {
        //$notification['image'] = 'http://lorempixel.com/400/200/';
        $firebaseConfigFile = Setting::where('key', 'firebase_config_file')->first()->value;
        if (empty($firebaseConfigFile)) {
            return "file_not_found";
        }
        if (!empty($devices)) {
            $firebasePath = base_path($firebaseConfigFile);
            if (file_exists($firebasePath)) {
                $firebase = (new \Kreait\Firebase\Factory())->withServiceAccount($firebasePath);
                $messaging = $firebase->createMessaging();
                $userDevices = $devices->pluck('device_token')->toArray();
                // Assuming $devices is a collection and needs to be converted to an array
                $devicesArray = $devices->toArray();
                $devicesArray = array_merge($devicesArray, $userDevices);
                self::sendFirebaseMultiDevicesCloudMessage($messaging, $notification, $userDevices, $payload);
                return "sent";
            }
            
        }
        return null;
    }

    private function sendFirebaseMultiDevicesCloudMessage($messaging, $notification, $devicesData, $payload)
    {
        if ($devicesData) {
            $devicesChunks = array_chunk($devicesData, 90);
            $count = 0;
            foreach ($devicesChunks as $devices) {
                $message = new RawMessageFromArray([
                    'notification' => [
                        'title' => $notification['title'],
                        'body' => $notification['message']
                    ],
                    'data' => count($payload) > 0 ? $payload : null,
                    'android' => [
                        // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidconfig
                        'ttl' => '3600s',
                        'priority' => 'HIGH',
                        'notification' => [
                            'title' => $notification['title'],
                            'body' => $notification['message']
                        ],
                    ],
                    'apns' => [
                        // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#apnsconfig
                        'headers' => [
                            'apns-priority' => '10',
                        ],
                        'payload' => [
                            'aps' => [
                                'alert' => [
                                    'title' => $notification['title'],
                                    'body' => $notification['message']
                                ],
                                'badge' => 42,
                            ],
                        ],
                    ],
                    'webpush' => [
                        // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#webpushconfig
                        'notification' => [
                            'title' => $notification['title'],
                            'body' => $notification['message']
                        ],
                    ],
                    'fcm_options' => [
                        // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#fcmoptions
                        'analytics_label' => 'some-analytics-label'
                    ]
                ]);
                $result = $messaging->sendMulticast($message, $devices);
                $count += $result->count();
            }
            return $count;
        } else {
            return 0;
        }
    }

    // push notification of caontact form to business owner

    public function contactFormNotification($contactForm)
    {
        $product = Product::where('id', $contactForm->product_id)->with(['business.businessOwner', 'user'])->first();
        $owner = $product->business ? $product->business->businessOwner : $product->user;
        $devices = $owner->devices()->get();
        $title = str_replace('_', ' ', $contactForm->subject);

        // if ($contactForm->subject == 'availability') {
        //     $title = 'Check availability of';
        // } else if ($contactForm->subject == 'price_quote') {
        //     $title = 'Get price quote of';
        // } else if ($contactForm->subject == 'test_drive') {
        //     $title = 'Schedule test drive of';
        // } else if ($contactForm->subject == 'financing') {
        //     $title = 'Discuss financing of';
        // } else {
        //     $title = 'Ask question about';
        // }
        $notification = [
            'title' => (string) ucfirst($title . ' for ' . $product->name),
            'message' => (string) $contactForm->comment
        ];

        $contactPayload = [
            'contact_id' => (string) $contactForm->id,
            'module_id' => (string) $product->standardTags()->whereHas('levelOne')->first()->id
        ];
        if ($product->business) {
            $contactPayload['dealership_id'] = (string) $product->business->uuid;
        } else {
            $contactPayload['owner_id'] = (string) $product->user->id;
        }
        self::sendCloudMessage($devices, $notification, $contactPayload);
    }
}
