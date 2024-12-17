<?php

namespace App\Http\Controllers\Admin\Settings;

use Inertia\Inertia;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allLanguages = config()->get('languages.languages');
        $limit = \config()->get('settings.pagination_limit');
        $languages = Language::orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->where('name', 'like', '%' . $keyword . '%')->orWhere('code', $keyword);
                }
            })->paginate($limit);
        return Inertia::render('Settings/Languages/Index', [
            'languagesList' => $languages,
            'searchedKeyword' => request()->keyword,
            'allLanguages' => array_column($allLanguages, 'name')
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
    public function store(LanguageRequest $request)
    {
        $languages = config()->get('languages.languages');
        if (!in_array($request->name, array_column($languages, 'name'))) {
            flash('Selected language is invalid', 'danger');
            return \redirect()->route('dashboard.settings.languages.index');
        }
        $index = array_search($request->name, array_column($languages, 'name'));
        Language::create($request->all() + ['code' => $languages[$index]['code']]);

        flash('Language added successfully', 'success');
        return \redirect()->route('dashboard.settings.languages.index');
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
        try {
            DB::beginTransaction();
            $language = Language::findOrFail($id);

            $defaultLanguage = Language::where('is_default', 1)->firstOrFail();
            if ($defaultLanguage) {
                $defaultLanguage->makeOrRemoveDefault()->save();
            }

            $language->makeOrRemoveDefault()->update([
                'status' => 'active'
            ]);

            DB::commit();
            flash('Marked as default succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this language', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return redirect()->back();
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
            $language = Language::findOrFail($id);
            if ($language->is_default) {
                flash('Can not delete the default language', 'danger');
                return redirect()->back();
            }
            $language->delete();
            flash('Language deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('dashboard.settings.languages.index', ['page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this language', 'danger');
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
            $language = Language::findOrFail($id);
            if ($language->is_default) {
                flash('Can not change the status of default language', 'danger');
                return redirect()->back();
            }
            $language->statusChanger()->save();
            flash('Language status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this language', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
