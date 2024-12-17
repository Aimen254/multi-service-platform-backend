<?php

namespace Modules\Retail\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Traits\StripePayment;
use App\Events\OrderStatusEmail;
use App\Traits\PushNotifications;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Support\Renderable;
use App\Enums\OrderStatus as OrderStatusEnum;
use Modules\Retail\Http\Requests\OrderFilterRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    use StripePayment, PushNotifications;
    protected $business;
    protected StripeClient $stripeClient;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
        $this->business = isset(Route::current()->parameters['business_uuid'])
            ? getBusinessDetails(Route::current()->parameters['business_uuid'], 'retail') : NULL;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $businessUuid, $type, OrderFilterRequest $request)
    {
        $startDate = $request->form && isset($request->form['from']) ? new Carbon($request->form['from']) : null;
        $startDate = $startDate ? $startDate->addDays(1)->format('Y-m-d') : null;
        $endDate =  $request->form && isset($request->form['to']) ? new Carbon($request->form['to']) : null;
        $endDate = $endDate ? $endDate->addDays(1)->format('Y-m-d') : null;
        $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
        $decimalLength = Setting::where('key', 'decimal_length')->first();
        $decimalSeparator = Setting::where('key', 'decimal_separator')->first();
        $limit = \config()->get('settings.pagination_limit');
        $statusList = OrderStatus::get();
        $orders = Order::with(['model', 'orderStatus'])->where(function ($query) use ($startDate, $endDate) {
            if (request()->input('keyword')) {
                $keyword = request()->keyword;
                $query->where('order_id', $keyword)
                    ->orWhere('total', $keyword)
                    ->orWhereHas('model', function ($subQuery) use ($keyword) {
                        $subQuery->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"]);
                    });
            }
            if (request()->form && isset(request()->form['order_type'])) {
                $query->where('order_type', request()->form['order_type']);
            }
            if (request()->form && isset(request()->form['order_status_id'])) {
                $query->where('order_status_id', request()->form['order_status_id']);
            }
            if ($startDate && $endDate) {
                $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            }
        })->when($type == 'completed', function ($query) {
            $query->whereIn('order_status_id', [OrderStatusEnum::Completed]);
        })->when($type == 'cancelled', function ($query) {
            $query->whereIn('order_status_id', [OrderStatusEnum::Cancelled, OrderStatusEnum::Returned, OrderStatusEnum::Refunded, OrderStatusEnum::Partially_Refunded, OrderStatusEnum::Rejected]);
        })->when($type == 'active', function ($query) {
            $query->whereNotIn('order_status_id', [OrderStatusEnum::Completed, OrderStatusEnum::Cancelled, OrderStatusEnum::Returned, OrderStatusEnum::Refunded, OrderStatusEnum::Partially_Refunded, OrderStatusEnum::Rejected]);
        })->where('business_id', $this->business->id)->withCount('items')->orderBy('id', $orderBy)->paginate($limit);
        return Inertia::render('Retail::Orders/Index', [
            'ordersList' => $orders,
            'statusList' => $statusList,
            'searchedKeyword' => request()->keyword,
            'orderBy' => request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc',
            'deliveryMethod' => request()->form && isset(request()->form['order_type']) ? request()->form['order_type'] : '',
            'status' => request()->form && isset(request()->form['order_status_id']) ? request()->form['order_status_id'] : null,
            'from' => request()->form && isset(request()->form['from']) ? request()->form['from'] : null,
            'to' => request()->form && isset(request()->form['to']) ? request()->form['to'] : null,
            'orderType' => $type,
            'decimalLength' => $decimalLength,
            'decimalSeparator' => $decimalSeparator
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('retail::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('retail::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($moduleId, $businessUuid, $type, $id)
    {
        try {
            $statusList = OrderStatus::get();
            $order = Order::with(['items.product', 'items.product.mainImage', 'model', 'shippingAddress', 'billingAddress', 'orderStatus', 'items.productVariant', 'items.productVariant.color', 'items.productVariant.size'])->findOrFail($id);
            return Inertia('Retail::Orders/Edit', [
                'order' => $order,
                'statusList' => $statusList,
                'orderType' => $type
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this order', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $moduleId, $businessUuid, $id)
    {
        //Getting Pending order status from DB
        $orderStatusPending = OrderStatus::where('status', 'pending')->first();
        //Getting refunded order status from DB
        $orderStatusRefunded = OrderStatus::where('status', 'refunded')->first();
        //Gettings accepted order status from DB
        $orderStatusAccepted = OrderStatus::where('status', 'accepted')->first();
        //Gettings cancelled order status from DB
        $orderStatusCancelled = OrderStatus::where('status', 'cancelled')->first();
        //Gettings rejected order status from DB
        $orderStatusRejected = OrderStatus::where('status', 'rejected')->first();
        //Gettings processing order status from DB
        $orderStatusProcessing = OrderStatus::where('status', 'Processing')->first();
        //getting business owner
        $businessOwner = Business::where('uuid', $businessUuid)->firstOrFail()->businessOwner->load('addresses', 'deliveryZone');
        $message = null;
        $status = null;

        //Change status of order
        try {
            $order = Order::with('shippingAddress', 'orderTracking', 'coupon', 'orderStatus', 'items')->findOrFail($id);
            //get previous order status
            $previousStatusId = $order->order_status_id;
            //Emails will not be sent to these statuses from this code but will be sent from webhooks
            $statusIdsExcluded = [
                $orderStatusAccepted->id,
                $orderStatusRefunded->id,
                $orderStatusRejected->id
            ];
            $order->update($request->all());
            $status = 'success';
            $message = 'Order status updated successfully';

            if (!in_array($request->order_status_id, $statusIdsExcluded)) {
                //sending mail to customer.
                event(new OrderStatusEmail($order, $request->order_status_id, $businessOwner));

                // sending push notifications to devices
                $this->orderNotification($order);
            } elseif (!$order->selected_card) {
                //sending mail to customer.
                event(new OrderStatusEmail($order, $request->order_status_id, $businessOwner));

                // sending push notifications to devices
                $this->orderNotification($order);
            }
        } catch (ModelNotFoundException $e) {
            $status = 'danger';
            $message = 'Unable to find this order';
        } catch (\Exception $e) {
            $status = 'danger';
            $message = $e->getMessage();
        }
        //charge customer for order
        if ($order->charged && !$order->captured && $request->order_status_id == $orderStatusAccepted->id && $order->selected_card) {
            $capturedPayment = $this->capturePayment($order);
            $status = $capturedPayment['status'];
            $message = $capturedPayment['message'];
        }

        //Managing Customer's refund
        if ($request->order_status_id == $orderStatusRefunded->id) {
            if ($order->selected_card) {
                if ($order->captured) {
                    if (!$order->refunded) {
                        $refundedPayment = $this->refundPayment($order);
                        $status = $refundedPayment['status'];
                        $message = $refundedPayment['message'];
                    } else {
                        $order->update([
                            'order_status_id' => $previousStatusId,
                        ]);
                        $status = 'danger';
                        $message = 'You have already refunded this order.';
                    }
                } else {
                    $order->update([
                        'order_status_id' => $previousStatusId,
                    ]);
                    $status = 'danger';
                    $message = 'Can not make a refund before accepting the order.';
                }
            }
        }

        //Managing Order Cancel 
        if ($request->order_status_id == $orderStatusRejected->id) {
            if ($order->selected_card) {
                if ($order->charged) {
                    if (!$order->captured) {
                        $rejectedPayment = $this->rejectPayment($order, $request);
                        $status = $rejectedPayment['status'];
                        $message = $rejectedPayment['message'];
                    } else {
                        $order->update([
                            'order_status_id' => $previousStatusId,
                        ]);
                        $status = 'danger';
                        $message = 'You payment has been captured. You can not cancel this order.';
                    }
                } else {
                    $order->update([
                        'order_status_id' => $previousStatusId,
                    ]);
                    $status = 'danger';
                    $message = 'Card has not been charged.';
                }
            } else {
                $order->update([
                    'order_status_id' => $orderStatusRejected->id,
                    'rejection_reason' => $request->input('message'),
                ]);
                $status = 'success';
                $message = 'Order Status Changed Successfully.';
                //sending rejection email to user
                event(new OrderStatusEmail($order, (string)OrderStatusEnum::Rejected, $businessOwner));
            }
        }

        flash($message, $status);
        return \redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }


    public function itemRefund(Request $request)
    {
        try {
            $orderItem = OrderItem::findOrFail($request->input('id'));
            $order = $orderItem->order;
            //get previous order status
            $previousStatusId = $order->order_status_id;
            //all refunded items
            $allRefundedOrderItems = $order->items->where('refunded', 1);
            //get previous order status
            $previousStatusId = $order->order_status_id;
            //Gettings processing order status from DB
            $orderStatusProcessing = OrderStatus::where('status', 'Processing')->first();
            //Getting refunded order status from DB
            $orderStatusRefunded = OrderStatus::where('status', 'refunded')->first();
            //business owner
            $businessOwner = $order->business->businessOwner->load('addresses', 'deliveryZone');
            //new order item total
            $totalRefund = $orderItem->total;
            if (count($order->items) - count($allRefundedOrderItems) == 1) {
                $allItemsRefunded = true;
                $applicationFeeRefund = true;
                $totalRefund += $order->platform_commission + $order->delivery_fee;
                $totalRefund = (float)number_format((float)$totalRefund, 2, '.', '');
            } else {
                $applicationFeeRefund = false;
                $allItemsRefunded = false;
                $totalRefund = (float)number_format((float)$totalRefund, 2, '.', '');
            }
            if ($order->selected_card) {
                //item refund for online payment
                $refundedItemPayment = $this->refundItemPayment($order, $orderItem, $totalRefund, $applicationFeeRefund, $allItemsRefunded, $previousStatusId, $orderStatusProcessing, $businessOwner);
                $status = $refundedItemPayment['status'];
                $message = $refundedItemPayment['message'];
            } else {
                //Item refund for cash on pickup
                if ($allItemsRefunded == 'true') {
                    $status = $orderStatusRefunded->id;
                    $orderStatusEnum = (string)OrderStatusEnum::Refunded;
                    $orderRefunded = 1;
                    $proportionalPlatformFee = $order->platform_commission;
                    $proportionalDeliveryFee = $order->delivery_fee;
                } else {
                    $status = $previousStatusId;
                    $orderStatusEnum = (string)OrderStatusEnum::Partially_Refunded;
                    $orderRefunded = 0;
                    $proportionalPlatformFee = 0;
                    $proportionalDeliveryFee = 0;
                }
                $refundedTotal = $order->amount_refunded + $orderItem->total;
                $orderItem->update([
                    'refunded' => 1,
                ]);
                $order->update([
                    'refunded' => $orderRefunded,
                    'amount_refunded' => $refundedTotal,
                    'order_status_id' => $status,
                    'refunded_delivery_fee' => $proportionalDeliveryFee,
                    'refunded_platform_fee' => $proportionalPlatformFee,
                ]);
                //sending mail to customer.
                event(new OrderStatusEmail($order, $orderStatusEnum, $businessOwner));
                $status = 'success';
                $message = 'Item Refunded Successfully.';
            }
        } catch (ModelNotFoundException $e) {
            $status = 'danger';
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $status = 'danger';
            $message = $e->getMessage();
        }
        flash($message, $status);
        return \redirect()->back();
    }

    public function seedCustomers()
    {
        for ($i = 0; $i < 10; $i++) {
            $data = [
                'first_name' => 'Interapptive',
                'last_name' => 'Customer ' . $i,
                'email' => 'customer' . $i . '@interapptive.com',
                'password' => Hash::make('12345678'),
                'user_type' => 'customer',
                'email_verified_at' => Carbon::now()
            ];
            $customer = $this->updateOrCreateStripeCustomer($data);
            $data['stripe_customer_id'] = $customer->id;
            if (!User::whereEmail($data['email'])->exists()) {
                if (User::withTrashed()->whereEmail($data['email'])->exists()) {
                    $user = User::withTrashed()->whereEmail($data['email'])->first();
                    $data['deleted_at'] = null;
                    $user->update($data);
                } else {
                    User::create($data);
                }
            } else {
                $user = User::whereEmail($data['email'])->first();
                $user->update($data);
            }
            $user->cards()->delete();
        }

        return 'data seeded';
    }

    // update or create stripe customer
    private function updateOrCreateStripeCustomer($customer)
    {
        // Retrieve the customer based on the email from Stripe
        $existingCustomer = $this->stripeClient->customers->all([
            'email' => $customer['email'],
            'limit' => 1,
        ]);

        if ($existingCustomer->count() > 0) {
            // Update the existing customer's information
            $stripeCustomer = $this->stripeClient->customers->update(
                $existingCustomer->data[0]->id,
                [
                    'name' => $customer['first_name'],
                    'description' => $customer['first_name'] . ' is registered as an existing customer.',
                ]
            );
        } else {
            // Create a new customer since no existing customer with the given email was found
            $stripeCustomer = $this->stripeClient->customers->create([
                'name' => $customer['first_name'],
                'email' => $customer['email'],
                'description' => $customer['first_name'] . ' is registered as a new customer.',
            ]);
        }
        return $stripeCustomer;
    }
}
