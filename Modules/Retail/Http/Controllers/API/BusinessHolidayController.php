<?php

namespace Modules\Retail\Http\Controllers\API;

use App\Models\Business;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Retail\Entities\BusinessHoliday;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Retail\Http\Requests\BusinessHolidayRequest;
use Carbon\Carbon;


class BusinessHolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($businessUuid)
    {
        $businessHolidays = BusinessHoliday::whereRelation('business', 'uuid', $businessUuid)->get();
            $formattedHolidays = $businessHolidays->map(function ($holiday) {
                return [
                    'id' => $holiday->id,
                    'date' => $holiday->formatted_date,
                    'title' => $holiday->title,
                    'business_id' => $holiday->business_id
                ];
            });
    
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => $formattedHolidays
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(BusinessHolidayRequest $request, $businessUuid)
    {
        try{
            $business=Business::where('uuid',$businessUuid)->first();
            $requestData = array_merge($request->all(), ['business_id' => $business->id]);
            BusinessHoliday::create($requestData);
            $businessHolidays = BusinessHoliday::whereRelation('business', 'uuid', $businessUuid)->get();
            return response()->json([
                'message' => 'Business Holiday Added!',
                'data' => $businessHolidays
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
     * @return Response
     */
    public function show($businessUuid, $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(BusinessHolidayRequest $request, $businessUuid, $id)
    {
        try {
            DB::beginTransaction();
            $businessHoliday = BusinessHoliday::findOrFail($id);
            $businessHoliday->update($request->all());
            $businessHolidays = BusinessHoliday::where('business_id', $businessHoliday->business_id)->get();
            DB::commit();
            return response()->json([
                'message' => 'Business Holiday Updated!',
                'data' => $businessHolidays
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
     * @return Response
     */
    public function destroy($businessUuid, $id)
    {
        try {
            $businessHoliday = BusinessHoliday::findOrFail($id);
            $businessId = $businessHoliday->business_id;
            $businessHoliday->delete();
            $businessHolidays = BusinessHoliday::where('business_id', $businessId)->get();
            
            return response()->json([
                'data' => $businessHolidays,
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
