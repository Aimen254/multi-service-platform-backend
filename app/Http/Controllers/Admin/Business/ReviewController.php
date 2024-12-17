<?php

namespace App\Http\Controllers\Admin\Business;

use Inertia\Inertia;
use App\Models\Review;
use App\Models\Business;;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($businessUuid)
    {

        $limit = \config()->get('settings.pagination_limit');
        $business = Business::with(['banner', 'logo', 'thumbnail'])->findOrFail(getBusinessDetails($businessUuid)->id);
        $reviews = $business->reviews()->whereHas('user', function ($query) {
            if (request()->input('keyword')) {
                $keyword = request()->keyword;
                $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"]);
            }
        })->with('user')->orderBy('id', 'desc')->paginate($limit);
        return Inertia::render('Business/Review/Index', [
            'business' => $business,
            'reviews' => $reviews,
            'title' => 'Reviews',
            'searchedKeyword' => request()->keyword,
        ]);
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
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $review = Review::findOrfail($id);
            $review->delete();
            flash('Review Deleted!', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('dashboard.business.reviews.index', [ 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Review', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
    /**
     * change the specified resource status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($id)
    {
        try {
            $review = Review::findOrfail($id);
            $review->statusChanger()->save();
            flash('Review status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Review', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
