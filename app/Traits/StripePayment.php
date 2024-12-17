<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CreditCard;
use App\Models\OrderStatus;
use App\Events\OrderStatusEmail;
use Illuminate\Http\JsonResponse;
use App\Enums\Business\Settings\DeliveryType;
use App\Enums\OrderStatus as OrderStatusEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;
trait StripePayment
{
    public function chargePayment(Order $order)
    {
        if ($order->selected_card) {
            try {
                //business owner
                $businessOwner = $order->business->businessOwner;
                $newOrderTotal = (float)number_format((float)$order->total, 2, '.', '');
                $newspaperFee = (float)number_format((float)$order->platform_commission, 2, '.', '');
                //Charge the selected card in order payload 
                $card = CreditCard::find($order->selected_card);
                if ($card) {
                    $payment = $this->makeStripePayment($newOrderTotal, $card->customer_id, $card->payment_method_id, $newspaperFee, $businessOwner->stripe_connect_id, $order, $card);
                    $status = $payment['status'];
                    $message = $payment['message'];
                } else {
                    $status = 'danger';
                    $message = 'Customer has no credit card. Payment Status Changed To Pending.';
                }
                if (!$card->save_card) {
                    $card->delete();
                }
            } catch (\Stripe\Exception\CardException $e) {
                $order->update([
                    'stripe_decline_code' => $e->getDeclineCode(),
                    'stripe_error_code' => $e->getStripeCode(),
                    'stripe_message' => $e->getMessage(),
                ]);
                $stripeErrorCode = $e->getDeclineCode() ?: $e->getStripeCode();
                $status = 'danger';
                $message = "Srtipe Error Code: " . $stripeErrorCode . ". Message: " . $e->getMessage();
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                $order->update(['stripe_message' => $e->getMessage()]);
                $status = 'danger';
                $message = $e->getMessage();
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                $order->update(['stripe_message' => $e->getMessage()]);
                $status = 'danger';
                $message = $e->getMessage();
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $order->update(['stripe_message' => $e->getMessage()]);
                $status = 'danger';
                $message = $e->getMessage();
            } catch (\Stripe\Exception\AuthenticationException $e) {
                $order->update(['stripe_message' => $e->getMessage()]);
                $status = 'danger';
                $message = $e->getMessage();
            } catch (\Stripe\Exception\IdempotencyException $e) {
                $order->update(['stripe_message' => $e->getMessage()]);
                $status = 'danger';
                $message = $e->getMessage();
            } catch (\Stripe\Exception\PermissionException $e) {
                $order->update(['stripe_message' => $e->getMessage()]);
                $status = 'danger';
                $message = $e->getMessage();
            } catch (\Stripe\Exception\RateLimitException $e) {
                $order->update(['stripe_message' => $e->getMessage()]);
                $status = 'danger';
                $message = $e->getMessage();
            } catch (ModelNotFoundException $e) {
                $order->update(['stripe_message' => $e->getMessage()]);
                $status = 'danger';
                $message = $e->getMessage();
            } catch (\Exception $e) {
                $order->update(['stripe_message' => $e->getMessage()]);
                $status = 'danger';
                $message = $e->getMessage();
            }

            return [
                'status' => $status,
                'message' => $message,
            ];
        } else {
            return [
                'status' => 'success',
                'message' => 'Order Placed Successfully',
            ];
        }

    }

    public function cancelPayment(Order $order)
    {
        try {
            $paymentCancelled=null;
            $previousOrderStatusId = $order->order_status_id;
            //Gettings processing order status from DB
            $orderStatusProcessing = OrderStatus::where('status', 'Processing')->first();
            if($order->payment_intent_id){

            
                $paymentCancelled = $this->stripeClient->paymentIntents->cancel($order->payment_intent_id, []);
            }
            if ( $order->payment_intent_id && $paymentCancelled->status == "canceled") {
                $order->update([
                    'order_status_id' => $orderStatusProcessing->id,
                    'stripe_message' => $paymentCancelled->status,
                ]);
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'message' => 'Order Status Changed Successfully.',
                ], JsonResponse::HTTP_OK);
            } else {
                $order->update([
                    'order_status_id' => $orderStatusProcessing->id,
                    'stripe_message' => $paymentCancelled->status,
                ]);
                return response()->json([
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                    'message' => $paymentCancelled->status
                ], JsonResponse::HTTP_NOT_FOUND);
            }
        } catch (\Stripe\Exception\CardException $e) {
            $order->update([
                'order_status_id' => $orderStatusProcessing->id,
                'stripe_message' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            $order->update([
                'order_status_id' => $orderStatusProcessing->id,
                'stripe_message' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function capturePayment(Order $order)
    {
        try {
            //Gettings processing order status from DB
            $orderStatusProcessing = OrderStatus::where('status', 'Processing')->first();
            //Business Owner
            $businessOwner = $order->business->businessOwner;
            $newOrderTotal = (float)number_format((float)$order->total, 2, '.', '');
            //retrieving and capturing order charge.
            $paymentIntent = $this->stripeClient->paymentIntents->retrieve($order->payment_intent_id);
            $paymentIntent->capture(['amount_to_capture' => $newOrderTotal * 100]);
            if ($paymentIntent->status == "succeeded") {
                //updating order table
                $order->update([
                    'order_status_id' => $orderStatusProcessing->id,
                    'payment_intent_id' => $paymentIntent->id
                ]);
                $status = 'success';
                $message = 'Order Status Changed Successfully.';
            } else {
                $status = 'danger';
                $message = 'Payment capture unseccessfull';
            }
        } catch (\Stripe\Exception\CardException $e) {
            $order->update([
                'order_status_id' => $orderStatusProcessing->id,
                'stripe_decline_code' => $e->getDeclineCode(),
                'stripe_error_code' => $e->getStripeCode(),
                'stripe_message' => $e->getMessage(),
            ]);
            $stripeErrorCode = $e->getDeclineCode() ?: $e->getStripeCode();
            $status = 'danger';
            $message = "Srtipe Error Code: " . $stripeErrorCode . ". Message: " . $e->getMessage();
            event(new OrderStatusEmail($order, 'order_failed', $businessOwner));
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $order->update(['order_status_id' => $orderStatusProcessing->id, 'stripe_message' => $e->getMessage()]);
            $status = 'danger';
            $message = $e->getMessage();
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            $order->update(['order_status_id' => $orderStatusProcessing->id, 'stripe_message' => $e->getMessage()]);
            $status = 'danger';
            $message = $e->getMessage();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $order->update(['order_status_id' => $orderStatusProcessing->id, 'stripe_message' => $e->getMessage()]);
            $status = 'danger';
            $message = $e->getMessage();
        } catch (\Stripe\Exception\AuthenticationException $e) {
            $order->update(['order_status_id' => $orderStatusProcessing->id, 'stripe_message' => $e->getMessage()]);
            $status = 'danger';
            $message = $e->getMessage();
        } catch (\Stripe\Exception\IdempotencyException $e) {
            $order->update(['order_status_id' => $orderStatusProcessing->id, 'stripe_message' => $e->getMessage()]);
            $status = 'danger';
            $message = $e->getMessage();
        } catch (\Stripe\Exception\PermissionException $e) {
            $order->update(['order_status_id' => $orderStatusProcessing->id, 'stripe_message' => $e->getMessage()]);
            $status = 'danger';
            $message = $e->getMessage();
        } catch (\Stripe\Exception\RateLimitException $e) {
            $order->update(['order_status_id' => $orderStatusProcessing->id, 'stripe_message' => $e->getMessage()]);
            $status = 'danger';
            $message = $e->getMessage();
        } catch (ModelNotFoundException $e) {
            $order->update(['order_status_id' => $orderStatusProcessing->id, 'stripe_message' => $e->getMessage()]);
            $status = 'danger';
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $order->update(['order_status_id' => $orderStatusProcessing->id, 'stripe_message' => $e->getMessage()]);
            $status = 'danger';
            $message = $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
        ];
    }

    public function refundPayment(Order $order)
    {
        try {
            //retrieving payment intent
            $paymentIntent = $this->stripeClient->paymentIntents->retrieve(
                $order->payment_intent_id,
                [
                    'expand' => ['latest_charge'],
                ]
            );
            $amountCaptured = $paymentIntent->latest_charge ? $paymentIntent->latest_charge->amount_captured : $paymentIntent->charges->data[0]->amount_captured;
            $amountRefunded = $paymentIntent->latest_charge ? $paymentIntent->latest_charge->amount_refunded : $paymentIntent->charges->data[0]->amount_refunded;
            //get previous order status
            $previousStatusId = $order->order_status_id;
            //Gettings processing order status from DB
            $orderStatusProcessing = OrderStatus::where('status', 'Processing')->first();
            //Business Owner
            $businessOwner = $order->business->businessOwner;
            //actual amount + total tax is the amount to refund.
            // $amountToRefund = (float)number_format((float)$order->total, 2, '.', '');
            $amountToRefund = $amountCaptured - $amountRefunded;
            //stripe refund api
            $refund = $this->stripeClient->refunds->create([
                'payment_intent' => $order->payment_intent_id,
                'amount' => $amountToRefund,
                // 'payment_intent' => 'pm_card_pendingRefund',
                'reverse_transfer' => true,
                'refund_application_fee' => true,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_other_id' => $order->order_id,
                    'partial_refund' => false,
                ]
            ]);
            if ($refund->status == "succeeded") {
                //updating order table
                $order->update([
                    'order_status_id' => $orderStatusProcessing->id
                ]);
                $status = 'success';
                $message = 'Order Status Changed Successfully.';
            } else {
                $order->update([
                    'refunded' => 0,
                    'order_status_id' => $previousStatusId,
                    'stripe_message' => $refund->status,
                ]);
                //sending mail to customer.
                event(new OrderStatusEmail($order, (string)OrderStatusEnum::Refund_Failed, $businessOwner));
                $status = 'danger';
                $message = 'Payment Refund Failed.';
            }
        } catch (\Stripe\Exception\CardException $e) {
            $order->update([
                'refunded' => 0,
                'order_status_id' => $previousStatusId,
                'stripe_message' => $e->getMessage(),
            ]);
            //sending mail to customer.
            event(new OrderStatusEmail($order, (string)OrderStatusEnum::Refund_Failed, $businessOwner));
            $status = 'danger';
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $order->update([
                'refunded' => 0,
                'order_status_id' => $previousStatusId,
                'stripe_message' => $e->getMessage(),
            ]);
            //sending mail to customer.
            event(new OrderStatusEmail($order, (string)OrderStatusEnum::Refund_Failed, $businessOwner));
            $status = 'danger';
            $message = $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
        ];
    }

    public function rejectPayment(Order $order, $request)
    {
        try {
            //Gettings processing order status from DB
            $orderStatusProcessing = OrderStatus::where('status', 'Processing')->first();
            $paymentCancelled = $this->stripeClient->paymentIntents->cancel($order->payment_intent_id, []);
            if ($paymentCancelled->status == "canceled") {
                $order->update([
                    'order_status_id' => $orderStatusProcessing->id,
                    'stripe_message' => $paymentCancelled->status,
                    'rejection_reason' => $request->input('message'),
                ]);
                $status = 'success';
                $message = 'Order Status Changed Successfully.';
                //sending rejection email to user
            } else {
                $order->update([
                    'order_status_id' => $orderStatusProcessing->id,
                    'stripe_message' => $paymentCancelled->status,
                ]);
                $status = 'danger';
                $message = 'Order Cancelled Failed.';
            }
        } catch (\Stripe\Exception\CardException $e) {
            $order->update([
                'order_status_id' => $orderStatusProcessing->id,
                'stripe_message' => $e->getMessage(),
            ]);
            $status = 'danger';
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $order->update([
                'order_status_id' => $orderStatusProcessing->id,
                'stripe_message' => $e->getMessage(),
            ]);
            $status = 'danger';
            $message = $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
        ];
    }

    public function refundItemPayment(Order $order, OrderItem $orderItem, $totalRefund, $applicationFeeRefund, $allItemsRefunded, $previousStatusId, $orderStatusProcessing, $businessOwner)
    {
        try {
            $refund = $this->stripeClient->refunds->create([
                'payment_intent' => $order->payment_intent_id,
                'amount' => $totalRefund * 100,
                'reverse_transfer' => true,
                'refund_application_fee' => $applicationFeeRefund,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_other_id' => $order->order_id,
                    'order_item_id' => $orderItem->id,
                    'partial_refund' => true,
                    'all_items_refunded' => $allItemsRefunded,
                    'previous_status' => $previousStatusId
                ]
            ]);
            if ($refund->status == "succeeded") {
                //updating order table
                $order->update([
                    'order_status_id' => $orderStatusProcessing->id
                ]);
                $status = 'success';
                $message = 'Item Refunded Successfully.';
            } else {
                $order->update([
                    'order_status_id' => $previousStatusId,
                    'stripe_message' => $refund->status,
                ]);
                //sending mail to customer.
                event(new OrderStatusEmail($order, (string)OrderStatusEnum::Refund_Failed, $businessOwner));
                $status = 'danger';
                $message = 'Item Refund Failed.';
            }
        } catch (\Stripe\Exception\CardException $e) {
            $order->update([
                'order_status_id' => $orderStatusProcessing->id,
                'stripe_message' => $e->getMessage(),
            ]);
            //sending mail to customer.
            event(new OrderStatusEmail($order, (string)OrderStatusEnum::Refund_Failed, $businessOwner));
            $status = 'danger';
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $order->update([
                'order_status_id' => $orderStatusProcessing->id,
                'stripe_message' => $e->getMessage(),
            ]);
            //sending mail to customer.
            event(new OrderStatusEmail($order, (string)OrderStatusEnum::Refund_Failed, $businessOwner));
            $status = 'danger';
            $message = $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
        ];
    }

    private function makeStripePayment($orderTotal, $customer_id, $payment_method_id, $newspaperFee, $stripe_connect_id, $order, $card)
    {
        //Getting Pending order status from DB
        $orderStatusPending = OrderStatus::where('status', 'pending')->first();
        //Check whom will get the delivery fee 
        $deliveryZoneType = $order->business->deliveryZone->delivery_type;
        $deliveryType = DeliveryType::coerce(str_replace(' ', '', ucwords($deliveryZoneType)))->value;
        //Calculating Delivery fee and adding it to charge.
        $deliveryFee = $order->delivery_fee == 0 ? $order->delivery_fee : (float)number_format((float)$order->delivery_fee, 2, '.', '');
        if ($order->order_type != "mail") {
            //updating newspaper fee
            $newspaperFee = $deliveryType == 1 ? $newspaperFee + $deliveryFee : $newspaperFee;
        }
        //updating order 
        $order->update([
            'delivery_owner' => $deliveryZoneType,
        ]);
        //Creating a payment intent
        $payment = $this->stripeClient->paymentIntents->create([
            'amount' => $orderTotal * 100,
            'currency' => 'usd',
            'customer' => $customer_id,
            //'payment_method' => 'pm_card_visa_chargeDeclinedInsufficientFunds',  
            'payment_method' => $payment_method_id,
            'description' => "Charging user's card.",
            'off_session' => true,
            'confirm' => true,
            'capture_method' => 'manual',
            'application_fee_amount' => $newspaperFee * 100,
            'receipt_email' => $order->model->email,
            'error_on_requires_action' => true,
            'payment_method_types' => ['card'],
            'transfer_data' => [
                'destination' => $stripe_connect_id,
            ],
            'metadata' => [
                'order_id' => $order->id,
                'order_other_id' => $order->order_id,
                'delivery_fee' => $order->delivery_fee,
                'platform_fee' => $order->platform_commission
            ],
        ]);

        if ($payment->status == "succeeded") {
            //updating order table
            $order->update([
                'payment_intent_id' => $payment->id,
            ]);
            $status = 'success';
            $message = 'Payment Transfered Successfully.';
        } elseif ($payment->status == "requires_capture") {
            //updating order table
            $order->update([
                'payment_intent_id' => $payment->id,
                'charged' => 1
            ]);
            $status = 'success';
            $message = 'Payment Requires Capture.';
        } else {
            $order->update([
                'payment_intent_id' => $payment->id
            ]);
            $status = 'danger';
            $message = 'Payment unseccessfull';
        }
        return [
            'status' => $status,
            'message' => $message,
        ];
    }

    private function getStripeCustomerId($request)
    {
        if (is_array($request)) {
            $customer = $this->stripeClient->customers->create([
                'name' => $request['first_name'],
                'email' => $request['email'],
                'description' => $request['first_name'] . ' is resgistered as a new customer.',
            ]);
        } else {
            $customer = $this->stripeClient->customers->create([
                'name' => $request->first_name,
                'email' => $request->email,
                'description' => $request->first_name . ' is resgistered as a new customer.',
            ]);
        }
        return $customer->id;
    }

    private function makePaymentMethod()
    {
        $payment = $this->stripeClient->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'token' => 'tok_us',
            ],
        ]);
        return $payment;
    }
}
