<?php

namespace Modules\Retail\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Entities\BusinessHoliday;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Retail\Http\Requests\BusinessHolidayRequest;

class BusinessHolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('retail::index');
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
    public function store(BusinessHolidayRequest $request)
    {
        try{
            BusinessHoliday::create($request->all());
            $businessHolidays = BusinessHoliday::where('business_id', $request->business_id)->get();
            return response()->json([
                'message' => 'Business Holiday Added!',
                'businessHolidays' => $businessHolidays
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($moduleId, $id)
    {
        $businessHolidays = BusinessHoliday::where('business_id', $id)->get();
        return response()->json([
            'businessHolidays' => $businessHolidays,
        ]);
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
    public function update(BusinessHolidayRequest $request, $moduleId,  $id)
    {
        try {
            DB::beginTransaction();
            $businessHoliday = BusinessHoliday::findOrFail($id);
            $businessHoliday->update($request->all());
            $businessHolidays = BusinessHoliday::where('business_id', $businessHoliday->business_id)->get();
            DB::commit();
            return response()->json([
                'message' => 'Business Holiday Updated!',
                'businessHolidays' => $businessHolidays
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Unable to find this Business Holiday'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $id)
    {
        try {
            $businessHoliday = BusinessHoliday::findOrFail($id);
            $businessId = $businessHoliday->business_id;
            $businessHoliday->delete();
            $businessHolidays = BusinessHoliday::where('business_id', $businessId)->get();
            
            return response()->json([
                'businessHolidays' => $businessHolidays,
                'message' => 'Business Holiday Deleted!'
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Unable to find this Business Holiday'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
