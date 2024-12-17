<?php

namespace Modules\Retail\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use App\Models\Business;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class ColorController extends Controller
{
    protected $business;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->business = Business::whereUuid(Route::current()->parameters['business_uuid'])
            ->firstOrFail();
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $businessId)
    {
        $limit = \config()->get('settings.pagination_limit');
        $colors = Color::where(function ($query) {
            if (request()->keyword) {
                $keyword = request()->keyword;
                $query->where('title', 'like', '%' . $keyword . '%');
            }
        })->where('business_id', $this->business->id)->orderBy('id', 'desc')->paginate($limit);

        return Inertia::render('Retail::Products/Colors/Index', [
            'business' => $this->business,
            'colorsList' => $colors,
            'searchedKeyword' => request()->keyword,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('retail::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $moduleId, $businessUuid)
    {
        $request->validate([
            'title' => [
                'required',
                Rule::unique('colors')->where(function ($query) {
                    return $query->where('business_id', $this->business->id);
                })
            ]
        ]);

        $this->business->colors()->create($request->all());

        \flash('Color created successfully.', 'success');
        return \redirect()->back();
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('retail::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('retail::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $moduleId, $businessUuid, $id)
    {
        $request->validate([
            'title' => [
                'required',
                Rule::unique('colors')->where(function ($query) use ($id) {
                    return $query->where('business_id', $this->business->id)
                        ->whereNotIn('id', [$id]);
                })
            ]
        ]);

        try {
            $colors = Color::findOrFail($id);
            $colors->update($request->all());

            \flash('Color updated successfully.', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this color', 'danger');
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
    public function destroy($moduleId, $businessUuid, $id,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $color = Color::findOrFail($id);
            $color->delete();

            flash('Color deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('retail.dashboard.business.colors.index', [$moduleId,$businessUuid, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this color', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
