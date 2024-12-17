<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Transformers\CommentTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\News\Entities\Comment;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ? $request->limit : \config()->get('settings.pagination_limit');
        $comments = Comment::whereRelation('product', function ($query) {
            // $query->commentable();
        })->where(function ($query) {
            if (request()->input('keyword')) {
                $query->where('comment', 'like', '%' . request()->input('keyword') . '%');
            }
        })->where('model_id', request()->product_id)->where('model_type', 'App\Models\Product')->with('user')->latest()->paginate($limit);
        $paginate = apiPagination($comments, $limit);
        $comments = (new CommentTransformer)->transformCollection($comments);
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => [
                'comments' => $comments,
            ],
            'meta' => $paginate,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request)
    {
        try {
            DB::beginTransaction();
            $message = '';
            $module = $request->module ? $request->module : '';
            $user = auth()->user();
            $news = Product::where('id', $request->product_id)->firstOrFail();
            $data = array_merge($request->validated(), [
                'user_id' => $user->id
            ]);
            $news->comments()->create($data);
            DB::commit();
            switch($module) {
                case 'obituaries':
                    $message = "Memory Added Successfully";
                    break;
                default:
                    $message = "Comment Added Successfully";
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' =>  $message,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(CommentRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $message = '';
            $module = $request->module ? $request->module : '';
            $comment = Comment::findOrFail($id);
            $user = auth()->user();
            $data = array_merge($request->validated(), [
                'user_id' => $user->id
            ]);
            $comment->update($data);
            DB::commit();
            switch($module) {
                case 'obituaries':
                    $message = "Memory updated Successfully";
                    break;
                default:
                    $message = "Comment updated Successfully";
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
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
    public function destroy(Request $request, $id)
    {
        try {
            $message = '';
            $module = $request->module ? $request->module : '';
            Comment::findOrFail($id)->delete();
            switch($module) {
                case 'obituaries':
                    $message = "Memory deleted Successfully";
                    break;
                default:
                    $message = "Comment deleted Successfully";
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' =>  $message,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
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
