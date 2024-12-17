<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\PublicProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Rules\MatchCurrentPassword;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Traits\CustomerStreetAddress;
use App\Transformers\UserTransformer;
use App\Transformers\AddressTransformer;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\API\ChangePasswordRequest;
use App\Http\Requests\API\ProfileAddressRequest;
use App\Http\Requests\API\PaymentSettingsRequest;
use App\Http\Requests\API\ProfileSettingsRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\StandardTag;
use App\Transformers\PublicProfileTransformer;

class UserProfileController extends Controller
{

    public function viewProfile()
    {
        try {
            $options = [
                'withDetails' => true
            ];
            $user = (new UserTransformer)->transform(auth()->user(), $options);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $user,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUserProfile(Request $request)
    {
        try {
            $options = [
                'withDetails' => (bool) ($request->with_details === 'false' ? false : true),
                'levelOneTag' => $request->module_id,
                'withBusiness' => (bool) ($request->with_business === 'false' ? false : true),
            ];
            $publicProfile = null;
            $user = null;
            $module = StandardTag::where('id', $request->module_id)->first();

            if ($module->slug === 'posts') {
                $publicProfile = PublicProfile::where('id', $request->user_id)->with(['followers' => function($subquery) {
                        $subquery->where('public_profiles.id', request()->input('profile_id'));
                    }])->first();
                $publicProfile = (new PublicProfileTransformer)->transform($publicProfile, $options);
            } else {
                $user = User::with('business')
                    ->withCount(['products' => function ($query) {
                        $query->whereRelation('standardTags', 'id', request()->input('module_id'));
                    }])
                    ->findOrFail($request->user_id);
                $user = (new UserTransformer)->transform($user, $options);
            }


            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' =>    $publicProfile ? $publicProfile :  $user,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => "User not found"
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $data = $request->all();
            if (auth()->check()) {
                $user = auth()->user();
                $user->update(['password' => Hash::make($data['new_password'])]);
            } else {
                $user = User::where('email_otp', $request->otp)->first();
                if ($user) {
                    $user->update([
                        'password' => Hash::make($data['new_password']),
                        'email_otp' => NULL,
                    ]);
                } else {
                    return response()->json([
                        'success' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => 'Otp Code not matched'
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
            return response()->json([
                'message' => 'Password changed successfully',
                'status' => JsonResponse::HTTP_OK,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function settings(ProfileSettingsRequest $request)
    {
        try {
            $user = auth()->user();
            if ($request->avatar != null) {
                deleteFile($user->avatar);
            }
            if ($request->cover_img != null) {
                deleteFile($user->cover_img);
            }
            $user->update($request->except('email'));
            $user = (new UserTransformer)->transform($user);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'User profile settings updated successfully.',
                'data' => $user,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllAddresses()
    {
        try {
            $limit = request()?->limit ?? \config()->get('settings.pagination_limit');
            $user = auth('sanctum')->user();
            $addresses = $user->addresses();
            if (request()->input('activeAddress')) {
                $addresses = $addresses->where('status', 'active');
            }
            $addresses = $addresses->paginate($limit);
            $paginate = apiPagination($addresses);
            $addresses = (new AddressTransformer)->transformCollection($addresses);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $addresses,
                'meta' => $paginate
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function editAddress($id)
    {
        try {
            $address = Address::findOrFail($id);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $address
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => "No address found"
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function removeAddress($id)
    {
        try {
            $user = auth('sanctum')->user();
            $address = $user->addresses()->findOrFail($id);
            if ($address->is_default) {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => "Can not delete default address.",
                    'messageType' => "default",
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $address->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Address deleted successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => "No address found"
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateAddress(ProfileAddressRequest $request)
    {
        try {
            $message = $request->input('id') ? "Address updated successfully" : "Address created successfully";
            DB::beginTransaction();
            $user = auth('sanctum')->user();
            if ($request->is_default) {
                $user->addresses()->update(['is_default' => 0]);
            }
            $request->merge(['status' => 'active']);
            $result = $user->addresses()->updateOrCreate(['id' => $request->input('id')], $request->validated());
            if ($result?->is_default) {
                $user->addresses()->find($result?->id)->update(['status' => 'active']);
                $user->addresses()->where('id', '!=', $result?->id)->update(['status' => 'inactive']);
            }
            $latestAddress = $user->addresses()->where(['id'=> $request->input('id') , 'status'=>'active'])->first();


            CustomerStreetAddress::streetAddress($latestAddress);
            DB::commit();
            // $address = (new AddressTransformer)->transformCollection($latestAddress);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
                'data' => $latestAddress,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateAddressStatus($id)
    {
        try {
            $address = Address::findOrFail($id);
            $address->status == 'active'
                ? $address->status = 'inactive'
                : $address->status = 'active';

            $address->update();

            // Address::where('id', '!=', $id)->update(['status' => 'inactive']);


            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => "Status updated successfully",
                'data' => $address
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => "No address found"
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function paymentSettings(PaymentSettingsRequest $request)
    {
        try {
            $user = auth()->user();
            $expiry_date = new Carbon($request->expiry_date);
            $expiry_date = $expiry_date->format('Y-m-d');
            $user->payment()->updateOrCreate([
                'user_id' => $user->id,

            ], [
                'card_number' => $request->card_number,
                'cvc' => $request->cvc,
                'expiry_date' => $expiry_date,
            ]);
            $user = (new UserTransformer)->transform($user);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Payment settings updated successfully.',
                'data' => $user,
            ], JsonResponse::HTTP_OK);
        } catch (\Exceptiono $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', new MatchCurrentPassword()]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = request()->user();
        $trashEmail = 'trash' . $user->id . '@trash.com';
        $user->update(['email' => $trashEmail, 'password' => NULL]);
        $user->delete();

        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Account deleted successfully'
        ], JsonResponse::HTTP_OK);
    }
}
