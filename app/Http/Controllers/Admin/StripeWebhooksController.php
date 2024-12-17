<?php

namespace App\Http\Controllers\Admin;

use Stripe\Refund;
use App\Models\User;
use App\Models\Order;
use App\Models\Setting;
use Stripe\StripeClient;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Events\OrderStatusEmail;
use App\Events\SubscriptionEmails;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Enums\Business\Settings\DeliveryType;
use App\Enums\OrderStatus as OrderStatusEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StripeWebhooksController extends Controller
{
    protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function paymentIntentSucceeded(Request $request)
    {
        // //getting stripe webhook secret from settings table
        $clientWebhookSecret = Setting::where('key', 'sandbox')->first()->value == 'no' ?  Setting::where('key', 'stripe_webhook_secret_production')->first() : Setting::where('key', 'stripe_webhook_secret_sandbox')->first();
        $endpoint_secret = $clientWebhookSecret->value;

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        //Getting Pending order status from DB
        $orderStatusPending = OrderStatus::where('status', 'pending')->first();
        //Getting refunded order status from DB
        $orderStatusRefunded = OrderStatus::where('status', 'refunded')->first();
        //Gettings cancelled order status from DB
        $orderStatusCancelled = OrderStatus::where('status', 'cancelled')->first();
        //Gettings accepted order status from DB
        $orderStatusAccepted = OrderStatus::where('status', 'accepted')->first();
        //Gettings partially refunded order status from DB
        $orderStatusPartiallyRefunded = OrderStatus::where('status', 'partially_refunded')->first();
        //Gettings partially refunded order status from DB
        $orderStatusRefundFailed = OrderStatus::where('status', 'refund_failed')->first();
        //Gettings rejected order status from DB
        $orderStatusRejected = OrderStatus::where('status', 'rejected')->first();

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $customerId = $event->data->object->customer;
                $customer = User::where('stripe_customer_id', $customerId)->firstOrFail();
                $paymentIntent = $event->data->object;
                $orderId = $paymentIntent->metadata['order_id'];
                if ($orderId) {
                    $order = Order::with('shippingAddress', 'orderTracking', 'coupon', 'orderStatus', 'items')->find($orderId);
                    $businessOwner = $order->business->businessOwner->load('addresses', 'deliveryZone');
                    $order->update([
                        'captured' => 1,
                        'stripe_decline_code' => null,
                        'stripe_error_code' => null,
                        'stripe_message' => null,
                        'order_status_id' => $orderStatusAccepted->id,
                    ]);
                    event(new OrderStatusEmail($order, (string)OrderStatusEnum::Accepted, $businessOwner));
                }
                break;

            case 'payment_intent.payment_failed':
                $customerId = $event->data->object->customer;
                $customer = User::where('stripe_customer_id', $customerId)->firstOrFail();
                $paymentIntent = $event->data->object;
                $orderId = $paymentIntent->metadata['order_id'];
                if ($paymentIntent->last_payment_error) {
                    $order = Order::with('shippingAddress', 'orderTracking', 'coupon', 'orderStatus', 'items')->find($orderId);
                    $businessOwner = $order->business->businessOwner->load('addresses', 'deliveryZone');
                    $order->update([
                        'charged' => 0,
                        'order_status_id' => $orderStatusPending->id,
                        'stripe_decline_code' => $paymentIntent->last_payment_error->decline_code,
                        'stripe_error_code' => $paymentIntent->last_payment_error->code,
                        'stripe_message' => $paymentIntent->last_payment_error->message,
                    ]);
                    //sending mail to customer.
                    event(new OrderStatusEmail($order, 'order_failed', $businessOwner));
                }
                break;

            case 'charge.refunded':
                $charge = $event->data->object;
                $orderId = $charge->metadata['order_id'];
                $refunds = $this->stripeClient->refunds->all([
                    'charge' => $charge->id,
                ]);
                if (!empty($refunds->data) &&  $refunds->data[0]->metadata->partial_refund && $refunds->data[0]->metadata->partial_refund == 'true') {
                    try {
                        $orderItemId = $refunds->data[0]->metadata->order_item_id;
                        $orderItem = OrderItem::findOrFail($orderItemId);
                        $order = $orderItem->order;
                        if ($refunds->data[0]->metadata->all_items_refunded == 'true') {
                            $status = $orderStatusRefunded->id;
                            $orderStatusEnum = (string)OrderStatusEnum::Refunded;
                            $orderRefunded = 1;
                            $proportionalPlatformFee = $order->platform_commission;
                            $proportionalDeliveryFee = $order->delivery_fee;
                        } else {
                            $status = $refunds->data[0]->metadata->previous_status;
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
                        $businessOwner = $order->business->businessOwner->load('addresses', 'deliveryZone');
                        event(new OrderStatusEmail($order, $orderStatusEnum, $businessOwner));
                    } catch (ModelNotFoundException $e) {
                        Log::info($e);
                    } catch (\Exception $e) {
                        Log::info($e);
                    }
                } else {
                    try {
                        $order = Order::with('shippingAddress', 'orderTracking', 'coupon', 'orderStatus', 'items')->findOrFail($orderId);
                        $OrderRefundedAmount = ($charge->amount_refunded) / 100;
                        $order->update([
                            'refunded' => 1,
                            'amount_refunded' => $OrderRefundedAmount,
                            'order_status_id' => $orderStatusRefunded->id,
                        ]);
                        //sending mail to customer.
                        $businessOwner = $order->business->businessOwner->load('addresses', 'deliveryZone');
                        event(new OrderStatusEmail($order, (string)OrderStatusEnum::Refunded, $businessOwner));
                    } catch (ModelNotFoundException $e) {
                        Log::info($e);
                    } catch (\Exception $e) {
                        Log::info($e);
                    }
                }
                break;

            case 'payment_intent.amount_capturable_updated':
                $paymentIntent = $event->data->object;
                $orderId = $paymentIntent->metadata['order_id'];
                $order = Order::with('shippingAddress', 'orderTracking', 'coupon', 'orderStatus', 'items')->find($orderId);
                $businessOwner = $order->business->businessOwner->load('addresses', 'deliveryZone');
                $order->update([
                    'charged' => 1,
                    'stripe_decline_code' => null,
                    'stripe_error_code' => null,
                    'stripe_message' => null,
                ]);
                //sending mail to customer.
                event(new OrderStatusEmail($order, 'order_charged', $businessOwner));
                break;

            case 'payment_intent.canceled':
                $customerId = $event->data->object->customer;
                $customer = User::where('stripe_customer_id', $customerId)->firstOrFail();
                $paymentIntent = $event->data->object;
                $orderId = $paymentIntent->metadata['order_id'];
                $order = Order::with('shippingAddress', 'orderTracking', 'coupon', 'orderStatus', 'items')->find($orderId);
                if ($order->rejection_reason) {
                    $status = $orderStatusRejected->id;
                    $orderStatusEnum = (string)OrderStatusEnum::Rejected;
                } else {
                    $status = $orderStatusCancelled->id;
                    $orderStatusEnum = (string)OrderStatusEnum::Cancelled;
                }
                $businessOwner = $order->business->businessOwner->load('addresses', 'deliveryZone');
                $order->update([
                    'order_status_id' => $status,
                ]);
                //sending mail to customer.
                event(new OrderStatusEmail($order, $orderStatusEnum, $businessOwner));
                break;

            case 'charge.refund.updated':
                $refund = $event->data->object;
                $orderId = $refund->metadata['order_id'];

                if ($refund->status == 'failed') {
                    if (!empty($refund->refunds->data) &&  $refund->refunds->data[0]->metadata->partial_refund && $refund->refunds->data[0]->metadata->partial_refund == 'true') {
                        try {
                            $orderItemId = $refund->refunds->data[0]->metadata->order_item_id;
                            $orderItem = OrderItem::findOrFail($orderItemId);
                            $orderItem->update([
                                'refunded' => 0,
                            ]);
                            $order = $orderItem->order;
                            $order->update([
                                'amount_refunded' => null,
                                'order_status_id' => $orderStatusRefundFailed->id,
                                'refunded_delivery_fee' => null,
                                'refunded_platform_fee' => null,
                            ]);
                            //sending mail to customer.
                            $businessOwner = $order->business->businessOwner->load('addresses', 'deliveryZone');
                            event(new OrderStatusEmail($order, (string)OrderStatusEnum::Refund_Failed, $businessOwner));
                        } catch (ModelNotFoundException $e) {
                            Log::info($e);
                        } catch (\Exception $e) {
                            Log::info($e);
                        }
                    } else {
                        try {
                            $order = Order::with('shippingAddress', 'orderTracking', 'coupon', 'orderStatus', 'items')->findOrFail($orderId);
                            $order->update([
                                'refunded' => 0,
                                'amount_refunded' => null,
                                'order_status_id' => $orderStatusRefundFailed->id,
                            ]);
                            //sending mail to customer.
                            $businessOwner = $order->business->businessOwner->load('addresses', 'deliveryZone');
                            event(new OrderStatusEmail($order, (string)OrderStatusEnum::Refund_Failed, $businessOwner));
                        } catch (ModelNotFoundException $e) {
                            Log::info($e);
                        } catch (\Exception $e) {
                            Log::info($e);
                        }
                    }
                }
                break;

            case 'invoice.paid':
                $invoicePaid = $event->data->object;
                $subscription = $this->stripeClient->subscriptions->retrieve(
                    $invoicePaid->subscription,
                    []
                );
                $productId = $subscription->plan->product;
                $product = $this->stripeClient->products->retrieve(
                    $productId,
                    []
                );
                $invoicePaid->product = $product;
                $invoicePaid->subscription = $subscription;
                $invoicePaid->hookType = 'invoice_paid';
                event(new SubscriptionEmails($invoicePaid));
                break;

            case 'invoice.payment_failed':
                $invoiceFailed = $event->data->object;
                $subscription = $this->stripeClient->subscriptions->retrieve(
                    $invoiceFailed->subscription,
                    []
                );
                $productId = $subscription->plan->product;
                $product = $this->stripeClient->products->retrieve(
                    $productId,
                    []
                );
                $paymentIntent = $this->stripeClient->paymentIntents->retrieve(
                    $invoiceFailed->payment_intent,
                    []
                );
                $invoiceFailed->product = $product;
                $invoiceFailed->paymentIntent = $paymentIntent;
                $invoiceFailed->subscription = $subscription;
                $invoiceFailed->hookType = 'invoice_payment_failed';
                event(new SubscriptionEmails($invoiceFailed));
                break;

            case 'invoice.upcoming':
                $invoiceUpcoming = $event->data->object;
                $subscription = $this->stripeClient->subscriptions->retrieve(
                    $invoiceUpcoming->subscription,
                    []
                );
                $productId = $subscription->plan->product;
                $product = $this->stripeClient->products->retrieve(
                    $productId,
                    []
                );
                $invoiceUpcoming->product = $product;
                $invoiceUpcoming->subscription = $subscription;
                $invoiceUpcoming->hookType = 'invoice_upcoming';
                event(new SubscriptionEmails($invoiceUpcoming));
                break;

            case 'customer.subscription.updated':
                $subscription = $event->data->object;
                $upcomingInvoice = $this->stripeClient->invoices->upcoming([
                    'customer' => $subscription->customer,
                    'subscription' => $subscription->id,
                ]);
                $productId = $subscription->plan->product;
                $product = $this->stripeClient->products->retrieve(
                    $productId,
                    []
                );
                $upcomingInvoice->product = $product;
                $upcomingInvoice->subscription = $subscription;
                $upcomingInvoice->hookType = 'customer_subscription_updated';
                event(new SubscriptionEmails($upcomingInvoice));
                break;

            case 'customer.subscription.deleted':
                $subCancelled = $event->data->object;
                $module = $subCancelled->metadata->module;
                $user = User::where('stripe_customer_id', $subCancelled->customer)->first();
                switch ($user->user_type) {
                    case 'business_owner':
                        $businesses = $user->businesses()->active()->with('standardTags', function ($query) {
                            $query->whereType('module');
                        })->whereHas('standardTags', function ($query) use ($module) {
                            $query->whereSlug($module);
                        })->get();
                        $businesses->each(function ($business, $key) {
                            $business->status = 'inactive';
                            $business->saveQuietly();
                        });
                        $subscription = $this->stripeClient->subscriptions->retrieve(
                            $subCancelled->id,
                            []
                        );
                        $productId = $subscription->plan->product;
                        $product = $this->stripeClient->products->retrieve(
                            $productId,
                            []
                        );
                        $subCancelled->product = $product;
                        $subCancelled->subscription = $subscription;
                        $subCancelled->hookType = 'subscription_cancelled';
                        event(new SubscriptionEmails($subCancelled));
                        break;
                }
                break;

                // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
                break;
        }
        return;
    }
}
