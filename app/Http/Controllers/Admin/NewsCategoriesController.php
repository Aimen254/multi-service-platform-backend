<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use App\Http\Requests\NewsRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\NewsCategoryRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class NewsCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $limit = \config()->get('settings.pagination_limit');
        $newsCategories = NewsCategory::orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->where('category_name', 'like', '%' . $keyword . '%');
                }
        })->paginate($limit);
        return Inertia::render('News/NewsCategories/Index', [
            'newsCategoriesList' => $newsCategories,
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
    public function store(NewsCategoryRequest $request)
    {
        try {
            NewsCategory::create($request->all());
            flash('News category created succesfully', 'success');
            return \redirect()->route('dashboard.categories.index');
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
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
    public function update(NewsCategoryRequest $request, $id)
    {
        try {
            $newsCategory = NewsCategory::findOrFail($id);
            $newsCategory->update($request->all());
            flash('News category updated succesfully', 'success');
            return \redirect()->route('dashboard.categories.index');
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this news category', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
             flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
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
            $newsCategory = NewsCategory::findOrFail($id);
            $newCount = $newsCategory->withCount('news')->first();
            if($newCount->news_count > 0)
            {
                flash('Not deleted! Category contain news', 'danger');
                return \redirect()->route('dashboard.categories.index');
            }
            $newsCategory->delete();
            flash('News category deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('dashboard.categories.index', [ 'page' => $previousPage]);
            }
            // return \redirect()->route('dashboard.categories.index');
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this news category', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }
}
