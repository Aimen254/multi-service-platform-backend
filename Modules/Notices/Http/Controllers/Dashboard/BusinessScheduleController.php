<?php

namespace Modules\Notices\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Retail\Entities\ScheduleTime;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Entities\BusinessSchedule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Retail\Http\Requests\BusinessScheduleRequest;

class BusinessScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $businessUuid)
    {
        $business = Business::with(['businessschedules'])->where('uuid', $businessUuid)->first();
        return Inertia::render('Notices::Business/BusinessSchedule/Index', [
            'business' => $business,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('notices::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(BusinessScheduleRequest $request)
    {
        try {
            $schedule = ScheduleTime::create(['business_schedule_id' => $request->id, 'open_at' => $request->open_at, 'close_at' => $request->close_at]);
            $schedule = BusinessSchedule::with('scheduletimes')
                ->findOrfail($schedule->business_schedule_id);
            return response()->json([
                'message' => 'Schedule Time Added!',
                'schedule' => $schedule
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
     * @return Renderable
     */
    public function show($id)
    {
        return view('notices::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('notices::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(BusinessScheduleRequest $request, $moduleId, $id)
    {
        try {
            DB::beginTransaction();
            $schedule = ScheduleTime::findOrFail($id);
            $schedule->update($request->all());
            $businessSchedule = BusinessSchedule::with('scheduletimes')->findOrFail($schedule->business_schedule_id);
            DB::commit();
            return response()->json([
                'message' => 'Schedule Time Updated!',
                'schedule' => $businessSchedule
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
     * @return Renderable
     */
    public function destroy($moduleId, $id)
    {
        try {
            $scheduleTime = ScheduleTime::findOrFail($id);
            $businessScheduleId = $scheduleTime->business_schedule_id;
            $scheduleTime->delete();
            $businessSchedule = BusinessSchedule::with('scheduletimes')->findOrFail($businessScheduleId);
            return response()->json([
                'message' => 'Schedule Time Deleted!',
                'schedule' => $businessSchedule
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Unable to find this Schedule'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus($moduleId, $businessUuid, $scheduleId)
    {
        try {
            DB::beginTransaction();
            $Schedule = BusinessSchedule::with('scheduletimes')->findOrfail($scheduleId);
            $Schedule->statusChanger()->save();
            DB::commit();
            return response()->json([
                'schedule' => $Schedule,
                'message' => 'Status changed!'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getSchedule($moduleId, $businessId, $scheduleId)
    {
        $schedule = BusinessSchedule::with('scheduletimes')->where('business_id', $businessId)
            ->findOrfail($scheduleId);
        return response()->json([
            'schedule' => $schedule,
        ]);
    }
}
