<?php

namespace Modules\Recipes\Http\Controllers\Dashboard\Recipes;

use Illuminate\Routing\Controller;
use Modules\News\Entities\Comment;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RecipeCommentController extends Controller
{
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