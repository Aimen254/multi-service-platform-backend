<?php

namespace Modules\Services\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Review;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $businessUuid)
    {
        $limit = \config()->get('settings.pagination_limit');
        $business = Business::with(['banner', 'logo', 'thumbnail'])->findOrFail(getBusinessDetails($businessUuid)->id);
        $reviews = $business->reviews()->whereHas('user', function ($query) {
            if (request()->input('keyword')) {
                $keyword = request()->keyword;
                $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', ["%{$keyword}%"]);
            }
        })->with('user')->orderBy('id', 'desc')->paginate($limit);
        return Inertia::render('Services::Business/Review/Index', [
            'business' => $business,
            'reviews' => $reviews,
            'title' => 'Reviews',
            'searchedKeyword' => request()->keyword,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('services::create');
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
        return view('services::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('services::edit');
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
    public function destroy($moduleId, $id,$businessId,Request $request)
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
                return Redirect::route('ervices.dashboard.service-provider.reviews.index', [$moduleId,$businessId, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Review', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function changeStatus($moduleId, $id)
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
