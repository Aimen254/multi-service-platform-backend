<?php

namespace Modules\Retail\Http\Controllers\Dashboard;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Business;
use Stripe\StripeClient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Http\Requests\StripeBankRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StripeController extends Controller
{
    protected StripeClient $stripeClient;
    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $businessUuid)
    {
        $business = Business::where('uuid', $businessUuid)->firstOrFail();
        $bankAccount = null;
        if ($business->businessOwner->stripe_bank_id) {
            $bankAccount =  $this->stripeClient->accounts->retrieveExternalAccount(
                $business->businessOwner->stripe_connect_id,
                $business->businessOwner->stripe_bank_id,
                []
            );
        }
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
            ['limit' => 20],
            ['stripe_account' => $business->businessOwner->stripe_connect_id]
        );
        // Stripe Refunds get
        // $stripeRefunds = $this->stripeClient->refunds->all(
        //     ['limit' => 10],
        //     ['stripe_account' => $business->businessOwner->stripe_connect_id]
        // );
        // Stripe payouts get
        $stripePayouts = $this->stripeClient->payouts->all(
            ['limit' => 20],
            ['stripe_account' => $business->businessOwner->stripe_connect_id]
        );
        return Inertia::render('Retail::Business/Stripe/Index', [
            'business' => $business,
            'bankAccount' => $bankAccount,
            'stripeConnected' => $business->businessOwner->completed_stripe_onboarding ? true : false,
            'stripeBalance' => $stripeBalance,
            'stripeChargesAll' => $stripeChargesAll,
            'stripeChargesRefunded' => null,
            'stripeAccount' => $stripeAccount,
            'stripePayouts' => $stripePayouts
        ]);
    }

    public function redirectToStripe($moduleId, $businessUuid)
    {
        $businessOwner = Business::where('uuid', $businessUuid)->firstOrFail()->businessOwner;

        if (!$businessOwner->completed_stripe_onboarding) {
            DB::beginTransaction();
            $token = Str::random();

            DB::table('stripe_state_tokens')->insert([
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => $businessOwner->id,
                'token' => $token
            ]);

            try {
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
                    'refresh_url' => route('retail.dashboard.business.redirect.stripe', ['moduleId' => $moduleId, 'business_uuid' => $businessUuid]),
                    'return_url'  => route('retail.dashboard.business.save.stripe', ['moduleId' => $moduleId, 'business_uuid' => $businessUuid, 'token' => $token]),
                    'type'        => 'account_onboarding',
                    'collect'     => 'eventually_due'
                ]);
                DB::commit();
                return redirect($onboardLink->url);
            } catch (\Exception $exception) {
                DB::rollBack();
                flash($exception->getMessage(), 'danger');
                return back()->withErrors(['message' => $exception->getMessage()]);
            }
        }
        try {
            $loginLink = $this->stripeClient->accounts->createLoginLink($businessOwner->stripe_connect_id);
            return redirect($loginLink->url);
        } catch (\Exception $exception) {

            flash($exception->getMessage(), 'danger');
            return back()->withErrors(['message' => $exception->getMessage()]);
        }
    }

    public function saveStripeAccount($moduleId,  $businessUuid, $token)
    {
        $stripeToken = DB::table('stripe_state_tokens')->where('token', $token)->first();

        if (is_null($stripeToken)) {
            abort(404);
        }

        $businessOwner = User::find($stripeToken->user_id);

        $businessOwner->update([
            'completed_stripe_onboarding' => true
        ]);

        return redirect()->route('retail.dashboard.business.stripe.index', ['moduleId' => $moduleId, 'business_uuid' => $businessUuid]);
    }

    public function saveStripeBankAccount(StripeBankRequest $request, $moduleId,  $businessUuid)
    {
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
        return redirect()->route('retail.dashboard.business.stripe.index', ['moduleId' => $moduleId, 'business_uuid' => $businessUuid]);
    }

    public function payout($moduleId, $businessUuid, Request $request)
    {
        try {
            if ($request->input('schedule') == 'daily') {
                $schedule = ['settings' => ['payouts' => ['schedule' => ['interval' => 'daily']]]];
            } else if ($request->input('schedule') == 'weekly') {
                $schedule = ['settings' => ['payouts' => ['schedule' => ['interval' => 'weekly', 'weekly_anchor' => 'monday']]]];
            } else {
                $schedule = ['settings' => ['payouts' => ['schedule' => ['interval' => 'monthly', 'monthly_anchor' => 1]]]];
            }
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
            $this->stripeClient->accounts->update(
                $business->businessOwner->stripe_connect_id,
                $schedule
            );
            flash('Payout Settings Updated', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $exception) {
            flash($exception->getMessage(), 'danger');
            return redirect()->back();
        } catch (\Exception $exception) {
            flash($exception->getMessage(), 'danger');
            return back()->withErrors(['message' => $exception->getMessage()]);
        }
    }

    public function getList($moduleId, $businessUuid, Request $request)
    {
        try {
            $business = Business::where('uuid', $businessUuid)->firstOrFail();
            $pointerFortransaction = $request->input('next_id') ? 'starting_after' : 'ending_before';
            switch ($request->input('request_type')) {
                case 'transactions':
                    // Stripe transactions for pagination
                    $stripeTransactions = $this->stripeClient->charges->all(
                        [
                            'limit' => 20,
                            $pointerFortransaction => $request->input('next_id') ?: $request->input('previous_id'),
                        ],
                        ['stripe_account' => $business->businessOwner->stripe_connect_id]
                    );
                    $data = $stripeTransactions;
                    break;
                case 'payouts':
                    // Stripe payouts for pagination
                    $stripePayouts = $this->stripeClient->payouts->all(
                        [
                            'limit' => 20,
                            $pointerFortransaction => $request->input('next_id') ?: $request->input('previous_id'),
                        ],
                        ['stripe_account' => $business->businessOwner->stripe_connect_id]
                    );
                    $data = $stripePayouts;
                    break;
                    // case 'refunds':
                    //     // Stripe refunds for pagination
                    //     $stripeRefunds = $this->stripeClient->refunds->all(
                    //         [
                    //             'limit' => 100, 
                    //             $pointerFortransaction => $request->input('next_id') ? : $request->input('previous_id'),
                    //         ],
                    //         ['stripe_account' => $business->businessOwner->stripe_connect_id]
                    //     );
                    //     $data = $stripeRefunds;
                    //     break;
            }
            return \response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $data
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return \response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return \response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
