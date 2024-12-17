<?php

namespace Modules\Events\Http\Controllers\API;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use App\Enums\EventBookingStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Events\Entities\EventBooking;
use Illuminate\Contracts\Support\Renderable;
use App\Transformers\EventBookingTransformer;
use Modules\Events\Http\Requests\BuyTicketRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function config;

class EventBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->limit ? $request->limit : config()->get('settings.pagination_limit');
            $bookings = EventBooking::with('product.events', 'user')->when(request()->input('type') === 'week', function ($query) {
                $query->when(!request()->input('start'), function ($subQuery) {
                    $subQuery->whereHas('product.events', function ($innerQyery) {
                        $innerQyery->whereBetween('event_date', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                    });
                }, function ($subQuery) {
                    $subQuery->whereHas('product.events', function ($innerQyery) {
                        $innerQyery->whereBetween('event_date', [
                            request()->input('start'),
                            request()->input('end'),
                        ]);
                    });
                });
            })->when(request()->input('type') === 'month', function ($query) {
                $query->whereHas('product.events', function ($eventQuery) {
                    $eventQuery->whereBetween('event_date', [
                        request()->input('start'),
                        request()->input('end'),
                    ]);
                });
            })->when(request()->input('type') === 'list' || request()->input('type') === 'day', function ($query) {
                $query->whereHas('product.events', function ($eventQuery) {
                    $eventQuery->whereDate('event_date', request()->input('start'));
                });
            })->when(request()->input('keyword'), function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->whereHas('product', function ($eventQuery) use ($keyword) {
                        $eventQuery->where('name', 'like', "%$keyword%");
                    })->orWhereHas('product.events', function ($eventQuery) use ($keyword) {
                        $eventQuery->where('performer', 'like', "%$keyword%")
                            ->orWhere('event_location', 'like', "%$keyword%");
                    });
                });
            })->active()->where('user_id', auth()->user()?->id)
                ->orderBy('id', 'desc')->paginate($limit);
            $paginate = apiPagination($bookings, $limit);
            $bookings = (new EventBookingTransformer)->transformCollection($bookings);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $bookings,
                'meta' => $paginate,
            ], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(BuyTicketRequest $request)
    {
        try {
            $user = auth()->user();
            $eventPrice = Product::where('id', $request->product_id)->first()?->max_price;
            $bookingEvent = EventBooking::create([
                'product_id' => $request->product_id,
                'user_id' => $user?->id,
                'ticket_price' => $eventPrice,
                'status' => EventBookingStatus::Going
            ]);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Event booked successfully.',
                'data' => $bookingEvent,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        try {
            $booking = EventBooking::findOrFail($id);
            $booking->update([
                'status' => $request->status
            ]);
            return response()->json([
                'data' => $booking,
                'message' => 'Status has been changed.',
                'ststus' => JsonResponse::HTTP_OK,
            ], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
