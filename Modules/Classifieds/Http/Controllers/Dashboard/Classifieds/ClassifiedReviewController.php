<?php

namespace Modules\Classifieds\Http\Controllers\Dashboard\Classifieds;

use Inertia\Inertia;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClassifiedReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {
        try {
            $limit = \config()->get('settings.pagination_limit');
            $product = Product::whereUuid($uuid)->firstOrFail();

            $reviews = Review::with('user')
            ->whereHas('product', function ($query) use ($uuid) {
                $query->where('uuid', $uuid);
            })->where(function ($query) {
                $keyword = request()->keyword;
                $query->whereHas('user', function ($query) use ($keyword) {
                    $query->where('first_name', 'like', '%' . $keyword . '%')
                    ->orWhere('last_name', 'like', '%' . $keyword . '%');
                })
                ->orWhere('comment', 'like', '%' . $keyword . '%')
                ->orWhere('created_at', 'like', '%' . $keyword . '%');
            })->latest()->paginate($limit);

            return Inertia::render('Classifieds::Classifieds/Settings/ClassifiedReviews', [
                'reviews' => $reviews,
                'product' => $product,
                'searchedKeyword' => request()->keyword,
            ]);
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $uuid, $id)
    {
        try {
            Review::when(!auth()->user()->hasRole(['admin', 'newspaper']), function ($query) {
                $query->whereHas('product', function ($subQuery) {
                    $subQuery->where('user_id', auth()->id());
                });
            })->findOrFail($id)->delete();
            flash('Review deleted succesfully', 'success');
            return back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to delete this review', 'danger');
            return back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
    }
}
