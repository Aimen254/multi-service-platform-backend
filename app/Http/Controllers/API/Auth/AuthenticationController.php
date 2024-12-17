<?php

namespace App\Http\Controllers\API\Auth;

use Carbon\Carbon;
use App\Models\User;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Transformers\UserTransformer;
use App\Http\Requests\API\LoginRequest;
use App\Events\SendEmailVerificationEvent;
use App\Http\Requests\API\RegistrationRequest;
use App\Events\ForgotPasswordEmailNotification;
use App\Http\Requests\API\ForgotPasswordRequest;

class AuthenticationController extends Controller
{
    protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function register(RegistrationRequest $request)
    {
        try {
            DB::beginTransaction();
            $stripeCustomerId = $this->getStripeCustomerId($request); //creating stripe customerId
            if ($request->is_social) {
                $request->merge([
                    'is_external' => $request->input('avatar') ? true : false,
                    'stripe_customer_id' => $stripeCustomerId,
                ]);
                $user = User::updateOrCreate($request->only([
                    'email',
                    'first_name',
                    'last_name',
                    'is_social',
                    'is_external',
                    'avatar',
                    'stripe_customer_id'
                ]));

                $token = $user->createToken('access_token')->plainTextToken;
            } else {
                $email_otp = mt_rand(100000, 999999);
                $request->merge([
                    'password' => Hash::make($request->password),
                    'user_type' => 'customer',
                    'email_otp' => $email_otp,
                    'stripe_customer_id' => $stripeCustomerId,
                ]);
                $user = User::create($request->all());
                if ($user) {
                    event(new SendEmailVerificationEvent($user, $email_otp));
                }
                $address_data = $request->addresses;
                if ($address_data) {
                    foreach ($address_data as $key => $value) {
                        if ($value['name'] != null && $value['address'] != null) {
                            $user->addresses()->create($value);
                        }
                    }
                }
            }

            $user->devices()->updateOrcreate(['device_token' => $request->header('Device-Token')], [
                'device_type' => $request->header('Device-Type'),
                'device_name' => $request->header('Device-Name'),
                'language' => $request->header('Accept-Language'),
                'send_notification' => $request->header('Send-Notification'),
                'provider' => $request->input('provider') ? $request->input('provider') : NULL,
                'provider_id' => $request->input('provider_id')
                    ? $request->input('provider_id') : NULL,
            ]);
            $user = (new UserTransformer)->transform($user, isset($token) ? ['access_token' => $token] : null);
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $user,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $request->merge(['status' => 'active']);
            if (Auth::attempt($request->all())) {
                request()->user()->devices()->updateOrCreate([
                    'user_id' => request()->user()->id,
                    'device_token' => $request->header('Device-Token')
                ], [
                    'device_type' => $request->header('Device-Type'),
                    'device_name' => $request->header('Device-Name'),
                    'language' => $request->header('Accept-Language'),
                    'send_notifications' => (int) $request->header('Send-Notifications')
                ]);
                $token = request()->user()->createToken('access_token')->plainTextToken;
                $user = (new UserTransformer)->transform(request()->user(), [
                    'access_token' => $token
                ]);
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'data' => $user,
                ], JsonResponse::HTTP_OK);
            }
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Invalid credentials',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            if (request()->filled('device_token')) {
                $deviceToken = $user->devices()->where('device_token', request()->device_token)->first();
                if ($deviceToken) {
                    $deviceToken->delete();
                }
            }
            auth()->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'logout successfully',
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $otp = mt_rand(100000, 999999);
            $user = User::whereEmail($request->email)->first();
            $user->update([
                'email_otp' => $otp
            ]);
            event(new ForgotPasswordEmailNotification($user, $otp));
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Email sent successfully',
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', Rule::exists('users')->where(function ($query) {
                return $query->whereEmail(request()->email)->whereUserType('customer');
            })],
            'otp' => ['required'],
            'type' => ['required', 'in:registration,login,forgot_password']
        ]);

        try {
            $user = User::whereEmail($request->email)->where('email_otp', $request->otp)->first();
            if ($user) {
                if ($request->type == 'registration') {
                    $user->update([
                        'email_otp' => null,
                        'email_verified_at' => Carbon::now()
                    ]);
                    $token = $user->createToken('access_token')->plainTextToken;

                    $user = (new UserTransformer)->transform($user, [
                        'access_token' => $token
                    ]);
                    return response()->json([
                        'status' => JsonResponse::HTTP_OK,
                        'data' => $user,
                    ], JsonResponse::HTTP_OK);
                } else if ($request->type == 'forgot_password') {
                    $user->update([
                        'email_verified_at' => Carbon::now()
                    ]);
                    return response()->json([
                        'status' => JsonResponse::HTTP_OK,
                        'message' => 'Otp verified'
                    ], JsonResponse::HTTP_OK);
                } else {
                    $user->update([
                        'email_otp' => null,
                        'email_verified_at' => Carbon::now()
                    ]);
                    $user = (new UserTransformer)->transform($user);
                    return response()->json([
                        'status' => JsonResponse::HTTP_OK,
                        'data' => $user,
                    ], JsonResponse::HTTP_OK);
                }
            } else {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Invalid otp.'
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function accountVerification(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', Rule::exists('users')->where(function ($query) {
                return $query->whereEmail(request()->email)->whereUserType('customer');
            })],
            'otp' => ['required']
        ]);

        try {
            $user = User::whereEmail($request->email)->where('email_otp', $request->otp)->first();
            if ($user) {
                $user->update([
                    'email_otp' => null,
                    'email_verified_at' => Carbon::now()
                ]);
                $token = $user->createToken('access_token')->plainTextToken;

                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'access_token' => $token,
                ], JsonResponse::HTTP_OK);
            } else {
                return response()->json([
                    'status' => JsonResponse::UN,
                    'message' => 'Invalid otp.'
                ], JsonResponse::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getStripeCustomerId($request)
    {
        $customer = $this->stripeClient->customers->create([
            'name' => $request->first_name,
            'email' => $request->email,
            'description' => $request->first_name . ' is resgistered as a new customer.',
        ]);
        return $customer->id;
    }

    public function resendEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email', Rule::exists('users')->where(function ($query) {
                    return $query->whereEmail(request()->email)->whereUserType('customer');
                })],
                'type' => ['required']
            ]);
            $user = User::whereEmail(request()->email)->whereUserType('customer')->first();
            $email_otp = mt_rand(100000, 999999);
            $user->update([
                'email_otp' => $email_otp
            ]);
            if ($user) {
                if ($request->input('type') == 'forgot_password') {
                    event(new ForgotPasswordEmailNotification($user, $email_otp));
                } else {
                    event(new SendEmailVerificationEvent($user, $email_otp));
                }
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Email sent',
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
