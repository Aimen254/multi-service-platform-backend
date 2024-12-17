<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Traits\StripePayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\API\UserRequest;
use App\Models\StandardTag;
use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    use StripePayment;
    protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $order_by = $request->filled('order') ? $request->input('order') : 'desc';
            $order_by_col = $request->filled('order_by_col') ? $request->input('order_by_col') : 'id';
            $limit = request()?->limit ?? \config()->get('settings.pagination_limit');
            $keyword = request()?->keyword;
            $role = request()->user_type;
            $module = StandardTag::where('id', request()->input('module'))->orWhere('slug', request()->input('module'))->first();
            $currentWeek = Carbon::now()->startOfWeek();
            $options = [
                'withProducts' => request()?->with_products,
                'levelOneTag' => $module?->id
            ];

            $users = null;

            if (isset($role)) {
                $users = User::whereHas('business', function ($query) {
                    $query->when(request()->input('business_id'), function ($subQuery) {
                        $subQuery->where('id', request()->input('business_id'));
                    });
                })->with('business')->whereUserType($role)->orderBy('id', 'desc')
                    ->where(function ($query) use ($keyword) {
                        if ($keyword) {
                            $query->where('first_name', 'like', '%' . $keyword . '%')
                                ->orWhere('last_name', 'like', '%' . $keyword . '%')
                                ->orWhere('email', $keyword);
                        }
                    })->orderBy($order_by_col, $order_by);
            } else {
                $users = User::whereHas('products', function ($query) use ($module) {
                    $query->whereRelation('standardTags', 'id', $module->id);
                })->with('products')->withCount(['products' => function ($query) use ($module) {
                    $query->whereRelation('standardTags', 'id', $module->id);
                }])->where(function ($query) use ($keyword, $request, $module) {
                    $query->when($keyword, function ($subQuery) use ($keyword, $module) {
                        $subQuery->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%');
                        $subQuery->orWhereHas('publicProfiles', function ($query) use ($keyword, $module) {
                            $query->where('name', 'like', '%' . $keyword . '%')->where('module_id', $module->id);
                        });
                    })
                        ->when(request()->input('favorite'), function ($subQuery) use ($request, $module) {
                            $subQuery->favoriteUsers($request, $module);
                        })
                        ->when(request()->input('category'), function ($subQuery) {
                            $subQuery->whereHas('products', function ($subQuery) {
                                $subQuery->whereRelation('standardTags', 'id', request()->input('category'));
                            });
                        });
                })->withCount(['products as weekly_published' => function ($query) use ($currentWeek) {
                    $query->whereBetween('created_at', [
                        $currentWeek->toDateTimeString(), // Convert to datetime string
                        $currentWeek->endOfWeek()->toDateTimeString()
                    ]);
                }])->get()->reject(function ($record) use ($keyword, $module) {
                    if ($keyword && $record->whereHas('publicProfiles', function ($query) use ($keyword, $record, $module) {
                        $query->where('module_id', $module->id)->where('user_id', $record->id);
                    })->exists()) {
                        if ($record->whereHas('publicProfiles', function ($query) use ($keyword, $record, $module) {
                            $query->where('name', 'like', '%' . $keyword . '%')->where('module_id', $module->id)->where('user_id', $record->id);
                        })->exists()) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        return false;
                    }
                });
            }

            $users = $users->paginate($limit);
            $paginate = apiPagination($users, $limit);
            $users = (new UserTransformer)->transformCollection($users, $options);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $users,
                'meta' => $paginate
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
     * @param AgentRequest $request
     * @return Renderable
     */
    public function store(UserRequest $request)
    {
        try {
            DB::beginTransaction();
            $stripeCustomerId = $this->getStripeCustomerId($request); //creating stripe customerId
            $validated = array_merge($request->validated(), [
                'password' => Hash::make($request->password),
                'stripe_customer_id' => $stripeCustomerId,
            ]);

            $user = User::create($validated);

            $address_data = $request->addresses;
            if (isset($address_data)) {
                foreach ($address_data as $key => $value) {
                    if ($value['name'] != null && $value['address'] != null) {
                        $user->addresses()->create($value);
                    }
                }
            }

            DB::commit();
            $role = ucfirst(str_replace('_', ' ', $request->user_type));
            $message = "{$role} created successfully";
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function edit(Request $request, string $id)
    {
        $role = $request->user_type;
        $options = ['levelOneTag' => true, 'withBusiness' => true];
        try {
            $user = User::where(['id' => $id, 'user_type' => $role])->firstOrFail();

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => (new UserTransformer)->transform($user, $options),
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $message = "Unable to find this {$role}";
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $message,
            ], JsonResponse::HTTP_NOT_FOUND);
            return \back();
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return \back();
        }
    }

    public function update(UserRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = User::whereUserType($request->user_type)->findOrFail($id);
            if (request()->hasFile('avatar')) {
                $avatar = config()->get('image.media.avatar');
                $width = $avatar['width'];
                $height = $avatar['height'];
                $userAvatar = request()->file('avatar');
                $extension = $userAvatar->extension();
                $user->avatar = saveResizeImage($userAvatar, "avatars", $width, $height, $extension);
            }
            if (request()->hasFile('cover_image')) {
                deleteFile($user->cover_image);
                $coverImg = config()->get('image.media.banner');
                $width = $coverImg['width'];
                $height = $coverImg['height'];
                $coverFile = request()->file('cover_image');
                $extension = $coverFile->extension();
                $user->cover_img = saveResizeImage($coverFile, "cover", $width, $height, $extension);
            }
            $validated = array_merge($request->validated(), [
                'password' => $request->input('password')
                    ? Hash::make($request->password) : $user->password,
                'avatar' => $user->avatar,
                'cover_img' => $user->cover_img,
            ]);
            $user->update($validated);
            // creating agent address
            $address_data = $request->addresses;
            $ids = array();
            if (isset($address_data)) {
                foreach ($address_data as $value) {
                    if (array_key_exists('id', $value)) {
                        $ids[] = $value['id'];
                        if ($value['name'] != null && $value['address'] != null) {
                            $user->addresses()->where('id', $value['id'])->update([
                                'name' => $value['name'],
                                'latitude' => $value['latitude'],
                                'longitude' => $value['longitude'],
                                'address' => $value['address'],
                                'note' => $value['note'],
                                'is_default' => $value['is_default']
                            ]);
                        }
                    } else {
                        if ($value['name'] != null && $value['address'] != null) {
                            $address = $user->addresses()->create($value);
                            $ids[] = $address->id;
                        }
                    }
                }
            }

            $user->addresses()->whereNotIn('id', $ids)->where('user_id',  $user->id)->delete();

            DB::commit();
            $role = ucfirst($request->user_type);
            $message = "{$role} updated successfully";
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            $message = "Unable to find this {$role}";
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $message,
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return \back();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $role =  str_replace('_', ' ', $request->user_type);
        try {
            $user = User::findOrFail($id);
            $user->status = $user->status == 'active' ? 'inactive' : 'active';
            $user->update();

            $message = "{$role} status changed succesfully.";

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
                'data' => $user
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $message = "Unable to find this {$role}";
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $message,
            ], JsonResponse::HTTP_NOT_FOUND);
            return \back();
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return \back();
        }
    }

    public function destroy(Request $request, string $id)
    {
        $role = ucfirst($request->user_type);
        try {
            $user = User::findOrFail($id);
            $user->forceDelete();

            $message = "{$role} deleted successfully.";
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $message = "Unable to find this {$role}";
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $message,
            ], JsonResponse::HTTP_NOT_FOUND);
            return \back();
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return \back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function getProductsAgainstUser($userId)
    {
        try {
            $options = [
                'withDetails' => true,
                'withSecondaryImages' => true,
            ];

            $limit = request()?->limit ?? \config()->get('settings.pagination_limit');
            $products = Product::where('user_id', $userId)->paginate($limit);
            // transform products and paginate
            $paginate = apiPagination($products, $limit);
            $products = (new ProductTransformer)->transformCollection($products, $options);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $products,
                'meta' => $paginate
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
