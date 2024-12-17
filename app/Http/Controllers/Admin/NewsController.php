<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use Inertia\Inertia;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use App\Http\Requests\NewsRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = \config()->get('settings.pagination_limit');
        $news = News::with('newsCategory')->orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('slug', $keyword);
                }
        })->paginate($limit);
        return Inertia::render('News/Index', [
            'newsList' => $news,
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
        $mediaSizes = \config()->get('image.media.news');
        $newsCategories = NewsCategory::select(['id', DB::raw('category_name as text')])->get(); 
        return Inertia::render('News/Create', [
            'mediaSizes' => $mediaSizes,
            'newsCategories' => $newsCategories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewsRequest $request)
    {
        try {
            $news = News::create($request->all());
            flash('News created succesfully', 'success');
            return \redirect()->route('dashboard.news.index');
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
        try {
            $mediaSizes = \config()->get('image.media.news');
            $newsCategories = NewsCategory::select(['id', DB::raw('category_name as text')])->get(); 
            $news = News::findOrFail($id);
            return Inertia::render('News/Edit', [
                'news' => $news,
                'mediaSizes' => $mediaSizes,
                'newsCategories' => $newsCategories
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this news', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NewsRequest $request, $id)
    {
        try {
            $news = News::findOrFail($id);
            if(request()->hasFile('image')) {
                deleteFile($news->image);
            }
            $news->update($request->all());
            flash('News updated succesfully', 'success');
            return \redirect()->route('dashboard.news.index');
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this news', 'danger');
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
            $news = News::findOrFail($id);
            \deleteFile($news->image);
            $news->delete();
            flash('News deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('dashboard.news.index', ['page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this news', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
             flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
        
    }
}
