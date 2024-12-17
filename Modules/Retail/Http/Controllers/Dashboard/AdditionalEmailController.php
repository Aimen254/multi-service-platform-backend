<?php

namespace Modules\Retail\Http\Controllers\Dashboard;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Entities\BusinessAdditionalEmail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;
use Modules\Retail\Http\Requests\AdditionalEmailsRequest;

class AdditionalEmailController extends Controller
{
    protected $businessId;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $business = getBusinessDetails(Route::current()->parameters['business_uuid']);
        $this->businessId = $business ? $business->id : null;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $limit = \config()->get('settings.pagination_limit');
        $businessEmails = BusinessAdditionalEmail::where(function ($query) {
            if (request()->keyword) {
                $keyword = request()->keyword;
                $query->where('personal_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', $keyword);
            }
        })->where('business_id', $this->businessId)->paginate($limit);
        return Inertia::render('Retail::Business/BusinessEmails/Index', [
            'businessEmails' => $businessEmails,
            'searchedKeyword' => request()->keyword
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
     * @param Request $request
     * @return Renderable
     */
    public function store(AdditionalEmailsRequest $request, $moduleId, $businessId)
    {
        try {
            DB::beginTransaction();
            $request->merge([
                'business_id' => $this->businessId,
            ]);
            BusinessAdditionalEmail::create($request->all());
            DB::commit();
            \flash('Business additional email created successfully.', 'success');
            return \redirect()
                ->route('retail.dashboard.business.emails.index', [$moduleId, $businessId]);
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
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
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(AdditionalEmailsRequest $request, $module, $id)
    {
        try {
            DB::beginTransaction();
            $businessEmail = BusinessAdditionalEmail::findOrFail($id);
            $businessEmail->update($request->all());
            flash('Business additional email updated successfully.', 'success');
            DB::commit();
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business additional email.', 'danger');
            DB::rollBack();
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            DB::rollBack();
            return \redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $id, $businessEmailId,Request $request)
    {
        try {
            $businessEmail = BusinessAdditionalEmail::findOrFail($id);
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $businessEmail->delete();
            flash('Business additional email deleted successfully.', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('retail.dashboard.business.emails.index', [$moduleId,$businessEmailId, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business additional email', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }
}
