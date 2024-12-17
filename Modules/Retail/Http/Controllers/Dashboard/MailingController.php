<?php

namespace Modules\Retail\Http\Controllers\Dashboard;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Retail\Entities\Mailing;
use App\Models\Business;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Http\Requests\MailingRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class MailingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $businessUuid)
    {
        $limit = \config()->get('settings.pagination_limit');
        $business = Business::whereUuid($businessUuid)->first();
        $mailings = Mailing::where('business_id', $business->id)->orderBy('id', 'desc')
            ->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->whereRaw('title like ?', ["%{$keyword}%"])
                        ->orWhere('price', $keyword)
                        ->orWhere('minimum_amount', $keyword);
                }
            })->paginate($limit);
        return Inertia::render('Retail::Business/Mailings/Index', [
            'business' => $business,
            'mailingList' => $mailings,
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
     * @param Request $request
     * @return Renderable
     */
    public function store(MailingRequest $request)
    {
        try {
            $mailExists = Mailing::where('title', $request->input('title'))->where('business_id', $request->input('business_id'))->onlyTrashed()->first();
            if ($mailExists) {
                $mailExists->restore();
            }
            Mailing::updateOrCreate(
                ['title' => $request->input('title'), 'business_id' => $request->input('business_id')],
                ['minimum_amount' => $request->input('minimum_amount'), 'price' => $request->input('price'), 'status' => 'active']
            );
            \flash('Business Mail setting added successfully.', 'success');
            return \redirect()->back();
        } catch (\Exception $e) {
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
    public function update(MailingRequest $request, $moduleId, $businessID, $id)
    {
        try {
            $mailing = Mailing::findOrFail($id);
            $mailing->update($request->all());
            flash('Business mail setting updated successfully', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this business mail setting', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            return $e->getMessage();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $businessId, $id,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $mailing = Mailing::findOrFail($id);
            $mailing->delete();
            flash('Business mail setting deleted successfully!', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('retail.dashboard.business.mailings.index', [$moduleId,$businessId, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business mail setting', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function changeStatus($moduleId, $businessId, $id)
    {
        try {
            $mailing = Mailing::findOrFail($id);
            $mailing->statusChanger()->save();
            flash('Business mail setting status changed succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business mail setting', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
