<?php

namespace Modules\Retail\Http\Controllers\API;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Stripe\StripeClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Retail\Http\Requests\StripeBankRequest;
use Nwidart\Modules\Exceptions\ModuleNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class StripeController extends Controller
{

    protected StripeClient $stripeClient;
    public function __construct(StripeClient $stripeClient = null)
    {
        $this->stripeClient = $stripeClient;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($businessUuid)
    {
        try {
             // if token exist
            if(request()->input('token')) {
                $stripeToken = DB::table('stripe_state_tokens')->where('token', request()->input('token'))->first();
                if($stripeToken) {
                    $businessOwner = User::find($stripeToken->user_id);
                    $businessOwner->update([
                        'completed_stripe_onboarding' => true
                    ]);
                }
            }
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
            $data =  $this->getStripeAccountInformation($business);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $data,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $businessUuid)
    {
        try {
            $businessOwner = Business::where('uuid', $businessUuid)->first()->businessOwner;
            if ($businessOwner && !$businessOwner->completed_stripe_onboarding) { 
                DB::beginTransaction();
                $token = Str::random();
                DB::table('stripe_state_tokens')->insert([
                    'created_at' => now(),
                    'updated_at' => now(),
                    'user_id' => $businessOwner->id,
                    'token' => $token
                ]);

                if (is_null($businessOwner->stripe_connect_id)) {
                    // Create account
                    $account = $this->stripeClient->accounts->create([
                        'country' => 'US',
                        'type'    => 'custom',
                        'email'   => $businessOwner->email,
                        'capabilities[transfers][requested]' => true,
                    ]);

                    $businessOwner->update(['stripe_connect_id' => $account->id]);
                    $businessOwner->fresh();
                }
            
                $onboardLink = $this->stripeClient->accountLinks->create([
                    'account'     => $businessOwner->stripe_connect_id,
                    'refresh_url' => env('VUE_APP_URL').'/dashboard/retail/store/'.$businessUuid.'/detail/create/strip/connect',
                    'return_url' => env('VUE_APP_URL').'/dashboard/retail/store/'.$businessUuid.'/detail/create/strip/connect/'.$token,
                    'type'        => 'account_onboarding',
                    'collect'     => 'eventually_due'
                ]);
                DB::commit();
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'url' => $onboardLink->url
                ], JsonResponse::HTTP_OK);
            } 
            $loginLink = $this->stripeClient->accounts->createLoginLink($businessOwner->stripe_connect_id);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'url' => $loginLink->url
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(StripeBankRequest $request, $businessUuid)
    {
        try {
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
            $res =  $this->stripeClient->accounts->createExternalAccount(
                $business->businessOwner->stripe_connect_id,
                ['external_account' =>
                [
                    'object' => 'bank_account',
                    'account_holder_name' => $request->input('account_holder_name'),
                    'account_number' => $request->input('account_number'),
                    'country' => $request->input('country'),
                    'routing_number' => $request->input('routing_number'),
                    'currency' => 'usd',
                ]]
            );
            $business->businessOwner->update([
                'stripe_bank_id' => $res->id
            ]);
            $data =  $this->getStripeAccountInformation($business);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $data,
                'message' => 'Bank Account saved successfully.'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function getList($businessUuid, Request $request)
    {
        try {
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
               $paginationPointer = $request->input('next_id') ? 'starting_after' : 'ending_before';
            $pointerValue = $request->input('next_id') ?: $request->input('previous_id');
            $data = null;
            switch ($request->input('request_type')) {
                case 'transactions':
                    $stripeTransactions = $this->stripeClient->charges->all(
                        [
                            'limit' => request()->input('charge_limit'),
                            $paginationPointer => $pointerValue,
                        ],
                        ['stripe_account' => $business->businessOwner->stripe_connect_id]
                    );
                    $data = $stripeTransactions;
                    break;
                case 'payouts':
                    $stripePayouts = $this->stripeClient->payouts->all(
                        [
                            'limit' => request()->input('payout_limit'),
                            $paginationPointer => $pointerValue,
                        ],
                        ['stripe_account' => $business->businessOwner->stripe_connect_id]
                    );
                    $data = $stripePayouts;
                    break;
                case 'refunds':
                    $stripeRefunds = $this->stripeClient->refunds->all(
                        [
                            'limit' => 20, 
                            $paginationPointer => $pointerValue,
                        ],
                        ['stripe_account' => $business->businessOwner->stripe_connect_id]
                    );
                    $data = $stripeRefunds;
                    break;
                default:
                    return response()->json([
                        'status' => JsonResponse::HTTP_BAD_REQUEST,
                        'message' => 'Invalid request type.'
                    ], JsonResponse::HTTP_BAD_REQUEST);
            }
            // Return the response with the data
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $data
            ], JsonResponse::HTTP_OK);
    
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Business not found.'
            ]);

        }
    }

    private function getStripeAccountInformation($business) {
        $bankAccount = null;
        $stripeAccount = null;
        $stripeBalance = null;
        $stripeChargesAll = null;
        $stripePayouts = null;

        if ($business->businessOwner->stripe_bank_id) {
            $bankAccount =  $this->stripeClient->accounts->retrieveExternalAccount(
                $business->businessOwner->stripe_connect_id,
                $business->businessOwner->stripe_bank_id,
                []
            );
        }

        if($business->businessOwner->stripe_connect_id) {
            // Stripe Connected account info get
            $stripeAccount = $this->stripeClient->accounts->retrieve(
                $business->businessOwner->stripe_connect_id,
                []
            );

            // Stripe balance get
            $stripeBalance = $this->stripeClient->balance->retrieve(
                [],
                ['stripe_account' => $business->businessOwner->stripe_connect_id]
            );

            // Stripe charges get
            $stripeChargesAll = $this->stripeClient->charges->all(
                ['limit' => request()->input('charge_limit')],
                ['stripe_account' => $business->businessOwner->stripe_connect_id]
            );

            $stripePayouts = $this->stripeClient->payouts->all(
                ['limit' => request()->input('payout_limit')],
                ['stripe_account' => $business->businessOwner->stripe_connect_id]
            );
        }

       return [
            'business' => $business,
            'bankAccount' => $bankAccount,
            'stripeConnected' => $business->businessOwner->completed_stripe_onboarding ? true : false,
            'stripeBalance' => $stripeBalance,
            'stripeChargesAll' => $stripeChargesAll,
            'stripeChargesRefunded' => null,
            'stripeAccount' => $stripeAccount,
            'stripePayouts' => $stripePayouts
       ];
    }
    // set payout interval
    public function payOut($businessUuid) {
        try {
            if (request()->input('schedule') == 'daily') {
                $schedule = ['settings' => ['payouts' => ['schedule' => ['interval' => 'daily']]]];
            } else if (request()->input('schedule') == 'weekly') {
                $schedule = ['settings' => ['payouts' => ['schedule' => ['interval' => 'weekly', 'weekly_anchor' => 'monday']]]];
            } else {
                $schedule = ['settings' => ['payouts' => ['schedule' => ['interval' => 'monthly', 'monthly_anchor' => 1]]]];
            }
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
            $this->stripeClient->accounts->update(
                $business->businessOwner->stripe_connect_id,
                $schedule
            );
            
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => request()->input('schedule'),
                'message' => 'Payout Settings Updated.'
            ], JsonResponse::HTTP_OK);
        } catch (ModuleNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
}
