<?php

namespace Modules\Government\Http\Controllers\Dashboard\Post;

use Inertia\Inertia;
use App\Models\Review;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostReviewController extends Controller
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

            $reviews = $product->reviews()
            ->where(function ($query) {
                $keyword = request()->input('keyword');
                if ($keyword) {
                    $query->where('comment', 'like', '%' . $keyword . '%')
                          ->orWhereHas('user', function ($query) use ($keyword) {
                              $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"]);
                          });
                }
            })
            ->with('user')
            ->orderBy('id', 'desc')
            ->paginate($limit);

            return Inertia::render('Government::Post/Reviews/PostReviews', [
                'reviews' => $reviews,
                'product' => $product,
                'searchedKeyword' => request()->keyword,
            ]);
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('government::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('government::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('government::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $uuid, $id)
    {
        try {
            if (auth()->user()->hasRole(['admin', 'newspaper'])) {
                $review = Review::when(auth()->user()->hasRole('newspaper'), function ($query) {
                    $query->whereHas('product.business', function ($subQuery) {
                        $subQuery->where('owner_id', auth()->user()->id);
                    });
                })->findOrFail($id)->delete();
            }
            flash('Review deleted succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to delete this review', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
