<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\StandardTag;
use App\Transformers\CalendarEventTransformer;
use Modules\Events\Entities\CalendarEvent;

class CalendarEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $module = null)
    {
        try {
            $limit = $request->limit ? $request->limit : config()->get('settings.pagination_limit');
            $module = StandardTag::where('id', $module)->orWhere('slug', $module)->first();
            $events = CalendarEvent::with('product')->where('user_id', auth()->user()?->id)
                ->where('module_id', $module?->id)->active()
                ->whereEventDateNotPassed()
                ->when(request()->input('type') === 'week', function ($query) {
                    $query->when(!request()->input('start'), function ($subQuery) {
                        $subQuery->whereBetween('date', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                    }, function ($subQuery) {
                        $subQuery->whereBetween('date', [
                            request()->input('start'),
                            date('Y-m-d 23:59:59', strtotime(request()->input('end'))),
                        ]);
                    });
                })->when(request()->input('type') === 'month', function ($query) {
                    $query->whereBetween('date', [
                        request()->input('start'),
                        date('Y-m-d 23:59:59', strtotime(request()->input('end'))),
                    ]);
                })->when(request()->input('type') === 'list' || request()->input('type') === 'day', function ($query) {
                    $query->whereDate('date', request()->input('start'));
                })
                ->paginate($limit);
            $paginate = apiPagination($events, $limit);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => (new CalendarEventTransformer)->transformCollection($events),
                'meta' => $paginate,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function changeStatus(Request $request, $module = null, $id)
    {
        try {
            $calendarEvent = CalendarEvent::findOrFail($id);
            $calendarEvent->update([
                'status' => $request->input('status')
            ]);
            return response()->json([
                'data' => $calendarEvent,
                'message' => 'Status has been changed.',
                'ststus' => JsonResponse::HTTP_OK,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
