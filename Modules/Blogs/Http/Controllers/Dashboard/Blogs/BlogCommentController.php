<?php

namespace Modules\Blogs\Http\Controllers\Dashboard\Blogs;

use App\Models\Product;
use Illuminate\Routing\Controller;
use Modules\News\Entities\Comment;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogCommentController extends Controller
{
    /**
     * Show the specified resource.
     *
     * @param int $moduleId
     * @param mixed $uuid
     * @param int $id
     * @return Renderable
     */
    public function show($moduleId, $uuid, $id)
    {
        try
        {
            $product = Product::where('uuid',$uuid)->firstOrFail();
            $comment = Comment::with('user')->where(function ($query) use ($product, $id) {
                $query->where('model_id', $product->id);
                $query->where('id', $id);
            })->firstOrFail();

            return inertia('Blogs::Comments/Show', [
                'commentList' => $comment
            ]);
        }
        catch (ModelNotFoundException $e) {
            flash('Unable to find the comment', 'danger');
            return \back();
        }
        catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $moduleId
     * @param mixed $uuid
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $uuid, $id)
    {
        try {
            if (auth()->user()->hasRole(['admin', 'newspaper', 'business_owner'])) {
                $comment = Comment::when(auth()->user()->hasRole('business_owner'), function ($query) {
                    $query->whereHas('product', function ($subQuery) {
                        $subQuery->where('user_id', auth()->user()->id);
                    });
                })->findOrFail($id)->delete();
            }
            flash('Comment deleted succesfully', 'success');
            return \back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to delete this comment', 'danger');
            return \back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        }
    }
}
