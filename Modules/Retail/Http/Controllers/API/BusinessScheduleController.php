<?php

namespace Modules\Retail\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Retail\Entities\BusinessSchedule;
use Illuminate\Http\JsonResponse;
use Modules\Retail\Entities\ScheduleTime;
use Modules\Retail\Http\Requests\BusinessScheduleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class BusinessScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($businessUuid)
    {
        $schedule = BusinessSchedule::with('scheduletimes')->whereRelation('business', 'uuid', $businessUuid)->get();
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => $schedule
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(BusinessScheduleRequest $request)
    {
       try {
            $open_at = $this->formatDateTime($request->open_at);
            $close_at = $this->formatDateTime($request->close_at);
            $request['open_at'] = $open_at;
            $request['close_at'] = $close_at;
            ScheduleTime::create(['business_schedule_id' => $request->id, 'open_at' => $request->open_at, 'close_at' => $request->close_at]);
            $schedule = BusinessSchedule::with('scheduletimes')
                    ->findOrfail($request->input('business_schedule_id'));
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Schedule Time Added!',
                'data' => $schedule
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
        

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(BusinessScheduleRequest $request, $businessUuid, $id)
    {
        try {
            DB::beginTransaction();
            $schedule = ScheduleTime::findOrFail($id);
            $open_at = $this->formatDateTime($request->open_at);
            $close_at = $this->formatDateTime($request->close_at);
            $request['open_at'] = $open_at;
            $request['close_at'] = $close_at;
            $schedule->update($request->all());
            $businessSchedule = BusinessSchedule::with('scheduletimes')->findOrFail($schedule->business_schedule_id);
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Schedule Time Updated!',
                'data' => $businessSchedule
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Unable to find this Schedule'
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
            $scheduleTime = ScheduleTime::findOrFail($id);
            $businessScheduleId = $scheduleTime->business_schedule_id;
            $scheduleTime->delete();
            $businessSchedule = BusinessSchedule::with('scheduletimes')->findOrFail($businessScheduleId);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Schedule Time Deleted!',
                'schedule' => $businessSchedule
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Unable to find this Schedule'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function changeStatus($businessUuid, $scheduleId)
    {
        try {
            DB::beginTransaction();
            $schedule = BusinessSchedule::with('scheduletimes')->findOrfail($scheduleId);
            $schedule->statusChanger()->save();
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $schedule,
                'message' => 'Status changed!'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function formatDateTime($time) {
        $date = \DateTime::createFromFormat('h:i A', $time);
        return $date ? $date->format('H:i:s') : null;
    }
}
