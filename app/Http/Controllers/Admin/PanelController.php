<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Device;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Traits\PushNotifications;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use App\Http\Controllers\Controller;
use App\Enums\Business\Settings\DeliveryType;
use Illuminate\Support\Facades\Log;

class PanelController extends Controller
{
    use PushNotifications;
    use StripeSubscription;
    public StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function index($id = null)
    {
        $users = User::whereUserType('customer');
        $businessOwners = User::whereUserType('business_owner');
        $drivers = User::whereUserType('driver');
        $reporters = User::whereUserType('reporter');
        $totalUsers = [
            [
                'name' => $users->count() > 0 ? $users->first()->user_type : 'customer',
                'count' => $users->count()
            ],
            [
                'name' => $businessOwners->count() > 0 ? $businessOwners->first()->user_type : 'business_owner',
                'count' => $businessOwners->count()
            ],
            [
                'name' => $drivers->count() > 0 ? $drivers->first()->user_type : 'driver',
                'count' => $drivers->count()
            ],
            [
                'name' => $reporters->count() > 0 ? $reporters->first()->user_type : 'reporter',
                'count' => $reporters->count()
            ],
        ];
        $data = [
            'totalUsers' => $totalUsers,
        ];

        if (auth()->user()->role->name == "business_owner") {
            $deliveryZoneErrors = auth()->user()->businesses()->where('uuid', $id)->select('name')->whereHas('deliveryZone', function ($query) {
                $query->where('delivery_type', DeliveryType::PlatformDelivery)
                    ->where('platform_delivery_type', null);
            })->first();
            if ($deliveryZoneErrors)
                $data['deliveryZoneErrors'] = $deliveryZoneErrors->name;
        }
        return Inertia::render('Dashboard', $data);
    }

    public function moduleTags()
    {
        try {
            $allowedModules = [];
            if (auth()->user()->user_type == 'business_owner' || auth()->user()->user_type == 'newspaper' || auth()->user()->user_type == 'customer' || auth()->user()->user_type == 'government_staff') {
                $allowedModules = $this->checkAllowedModules();
            }
            $moduleTags = StandardTag::where('status', 'active')->whereType('module')
            ->when(request()->user()->user_type == 'business_owner' || request()->user()->user_type == 'government_employee' || request()->user()->user_type == 'customer'|| auth()->user()->user_type == 'government_staff', function ($query) use ($allowedModules) {
                $query->whereIn('slug', $allowedModules);
            })->oldest('name')->get();
            return \response()->json([
                'moduleTags' => $moduleTags,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sendNotification(Request $request)
    {
        try {
            // get devices of user i am getting devices of my chrome
            $devices = User::whereEmail('customer1@interapptive.com')->first()->devices()
                ->orderByDesc('id')->limit(1)->get();
            $notification = [
                'title' => "Testing",
                'message' => "Test Message"
            ];
            $response = $this->sendCloudMessage($devices, $notification, ['order_id' => '1']);
            if ($response == 'file_not_found') {
                return "Firebase configuration file not found";
            } else if ($response == 'sent') {
                return "Cloud Message send successfully";
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function notification()
    {
        return Inertia::render('Notifications/Index');
    }

    public function appNotificationsTesting()
    {
        $devices = Device::where('send_notifications', 1)->whereHas('user', function ($query) {
            $query->whereUserType('customer');
        })->get();

        $notification = [
            'title' => 'Test notification',
            'message' => 'Testing app notifications'
        ];

        $this->sendCloudMessage($devices, $notification);
    }
}
