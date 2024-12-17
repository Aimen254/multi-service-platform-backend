<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Product;
use App\Models\Business;
use App\Models\StandardTag;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Jobs\UpdateConversations;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\ConversationRequest;
use App\Transformers\ConversationTransformer;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($module)
    {
        $limit = request()->input('limit') ? request()->limit : \config()->get('settings.pagination_limit');
        $module = StandardTag::where('slug', $module)->firstOrFail();
        $conversations = Conversation::with(
            [
                'sender' => function ($query) {
                    $query->where('id', '<>', auth()->user()?->id);
                },
                'receiver' => function ($query) {
                    $query->where('id', '<>', auth()->user()?->id);
                },
                'product'
            ]
        )->where('module_id', $module?->id)->where(function ($query) {
            $query->where('sender_id', auth()->user()?->id)->orWhere('reciever_id', auth()->user()?->id);
        })->orderBy('updated_at', 'desc')->paginate($limit);
        $paginate = apiPagination($conversations, $limit);
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => (new ConversationTransformer)->transformCollection($conversations),
            'meta' => $paginate,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConversationRequest $request, $module)
    {
        try {
            $business = Business::whereRelation('products', 'id', $request->product_id)->first();
            if($business) {
                $reciever_id = $business->owner_id;
            } else {
                $reciever_id = Product::where('id', $request->product_id)->first()?->user_id;
            }

            if($reciever_id == auth()->user()?->id) {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => [
                        'reciver_error' => ['You cannot send message to yourself.']
                    ]
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $module = StandardTag::where('slug', $module)->firstOrFail();

            // check if module chat permission
            if(!$module?->can_chat || ($business && !$business?->can_chat)) { 
                return response()->json([
                    'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => [
                        'chat_permission' => ['Chat feature is disabled']
                    ]
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $product = Product::where('id', request()->input('product_id'))->with(['mainImage'])->first();

            $data = $request->validated();
            $data['module_id'] = $module?->id;
            $conversation = Conversation::updateOrCreate([
                'sender_id' => $data['sender_id'],
                'reciever_id' => $reciever_id,
                'module_id' => $data['module_id'],
                'product_id' => $request->input('product_id')
            ], [
                'message' => $data['message']
            ]);
            
            // get sender
            $sender = auth()->user();
            
            // get reciever
            $reciever = User::where('id', $conversation?->reciever_id)->first();

            // call chat api to start conversation
            $response = Http::post(env('CHAT_API_URL') . '/conversation', [
                'sender_id' => $conversation?->sender_id,
                'sender_username' => $sender->first_name. ' ' .$sender->last_name,
                'sender_avatar' => getImage($sender->avatar, 'avatar', $sender->is_external),
                'receiver_id' => $conversation?->reciever_id,
                'receiver_username' => $reciever?->first_name. ' ' .$reciever?->last_name,
                'receiver_avatar' => getImage($reciever?->avatar, 'avatar'),
                'product_id' => $product?->id,
                'product_uuid' => $product?->uuid,
                'product_name' => $product?->name,
                'product_media' => $product?->mainImage
                ? getImage($product?->mainImage?->path, 'image', $product?->mainImage?->is_external)
                : getImage(NULL, 'image'),
                'module_id' => $conversation?->module_id,
                'module_slug' => $module?->slug,
                'message' => $conversation->message,
                'room_id' => $conversation->id,
            ]);

            // data that to be send in socket
            $data = [
                'sender_id' => $conversation?->sender_id,
                'sender_username' => $sender->first_name. ' ' .$sender->last_name,
                'sender_avatar' => getImage($sender->avatar, 'avatar', $sender->is_external),
                'receiver_id' => $conversation?->reciever_id,
                'receiver_username' => $reciever?->first_name. ' ' .$reciever?->last_name,
                'receiver_avatar' => getImage($reciever?->avatar, 'avatar'),
                'product_id' => $product?->id,
                'product_uuid' => $product?->uuid,
                'product_name' => $product?->name,
                'product_media' => $product?->mainImage
                ? getImage($product?->mainImage?->path, 'image', $product?->mainImage?->is_external)
                : getImage(NULL, 'image'),
                'module_id' => $conversation?->module_id,
                'module_slug' => $module?->slug,
                'message' => $conversation->message,
                'room_id' => $conversation->id,
            ];
            if ($response->successful()) {
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'message' => 'Message Send successfully.',
                    'data' => $data,
                ], JsonResponse::HTTP_OK);
            } else {
                $errorBody = $response->json();
                return response()->json([
                    'message' => $errorBody['message'],
                    'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ConversationRequest $request, $module, $id)
    {
        try {
            $conversation = Conversation::find($id);
            $conversation->update([
                'message' => $request->message
            ]);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Conversation Updated successfully.',
                'data' => $conversation,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($module, $id)
    {
        try {
            $conversation = Conversation::find($id);
            $conversation->delete(); // Soft delete the conversation
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Conversation soft deleted successfully.',
                'data' => $conversation,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // verfiy auth user token 
    public function VerifyToken()
    {
        try {
            $user = auth('sanctum')->user();
            if ($user) {
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'message' => 'Authenticated'
                ], JsonResponse::HTTP_OK);
            } else {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNAUTHORIZED,
                    'message' => 'Unauthenticated'
                ], JsonResponse::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
