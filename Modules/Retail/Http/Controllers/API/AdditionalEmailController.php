<?php

namespace Modules\Retail\Http\Controllers\Api;

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
use App\Models\Business;


class AdditionalEmailController extends Controller
{
   
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($businessUuid)
    {

        $limit = request()->input('limit') ? request()->input('limit') : config('settings.pagination_limit');
        $business = Business::whereUuid($businessUuid)->firstOrFail();
        $query = BusinessAdditionalEmail::where('business_id', $business->id)
            ->orderBy('id', 'desc');

        if ($keyword = request()->input('keyword')) {
            $query->where(function ($query) use ($keyword) {
                $query->where('personal_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }
        $businessEmails = $query->paginate($limit);
        $businessEmails->getCollection();
        $paginate = apiPagination($businessEmails, $limit);
        return response()->json([
            'businessEmails' => $businessEmails->items(),
            'meta' =>  $paginate
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
    public function store(AdditionalEmailsRequest $request, $uuid)
    {

        try {
            DB::beginTransaction();
            $business = Business::whereUuid($uuid)->firstOrFail();
            $request->merge([
                'business_id' => $business->id,
            ]);
            $businessEmail = BusinessAdditionalEmail::create($request->all());
            DB::commit();
            return response()->json([
                'message' => 'Business internal email created successfully.',
                'businessEmail' => $businessEmail
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'danger'
            ], 500);
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
            $updateData = $request->except('business_id');
            $businessEmail->update($updateData);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Business internal email updated successfully.',
                'businessEmail' => $businessEmail 
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Unable to find this business internal email ',
                'status' => 'error'
            ], 404);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request,  $businessEmailId, $id)
    {
        try {
            $businessEmail = BusinessAdditionalEmail::findOrFail($id);
            $businessEmail->delete();
            return response()->json([
                'message' => 'Business internal email deleted successfully.',
                'status' => 'success'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Unable to find this business internal email',
                'status' => 'danger'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'danger'
            ], 500);
        }
    }
}
