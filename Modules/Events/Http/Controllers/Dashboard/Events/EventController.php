<?php

namespace Modules\Events\Http\Controllers\Dashboard\Events;

use Exception;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Product;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Rules\PriceValidation;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\News\Entities\Comment;
use Illuminate\Support\Facades\Log;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductPriorityManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Support\Renderable;
use Modules\Events\Http\Requests\EventRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventController extends Controller
{

    use StripeSubscription;

    public function __construct(protected StripeClient $stripeClient)
    {
        $this->middleware('can:view_products')->only('view');
        $this->middleware('can:edit_products')->only('edit');
        $this->middleware('can:add_products')->only('create');
        $this->middleware('can:delete_products')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

     public function index($moduleId = null)
    {
        $requestForm = request()->form ?? [];
        $orderBy = $requestForm['orderBy'] ?? 'desc';
        $barMinValue = $requestForm['barMinValue'] ?? null;
        $barMaxValue = $requestForm['barMaxValue'] ?? null;
        $limit = config('settings.pagination_limit');

        $userId = auth()->id();
        $isAdmin = auth()?->user()?->hasRole('admin');

        $baseQuery = Product::query()
            ->whereHas('standardTags', function ($subQuery) use ($moduleId) {
                $subQuery->where('id', $moduleId);
            });

        // Max and Min Price calculation using pluck
        $maxPrice = $baseQuery->when(!$isAdmin, function ($query) use($userId) {
            $query->where('user_id', $userId);
        })->pluck('max_price')->max();

        $minPrice = $baseQuery->when(!$isAdmin, function ($query) use($userId) {
            $query->where('user_id', $userId);
        })->pluck('price')->min();

        // Main Query for Events
        $eventsQuery = $baseQuery->with(['events', 'user', 'mainImage', 'comments'])
            ->when(!$isAdmin, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->when(request()->keyword, function ($query) {
                $keyword = request()->keyword;
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->when(isset($requestForm['status']), function ($query) use ($requestForm) {
                $query->where('status', $requestForm['status']);
            })
            ->when(isset($requestForm['barMinValue']) && isset($requestForm['barMaxValue']), function ($query) use ($barMinValue, $barMaxValue) {
                $query->whereBetween('price', [$barMinValue, $barMaxValue]);
            })
            ->when(isset($requestForm['tag']), function ($query) use ($requestForm) {
                $query->whereHas('standardTags', function ($subQuery) use ($requestForm) {
                    $subQuery->where('id', $requestForm['tag']);
                });
            });

        // Pagination and ordering
        $events = $eventsQuery->orderBy('id', $orderBy)
            ->paginate($limit);

        return inertia('Events::Index', [
            'eventList' => $events,
            'searchedKeyword' => request()->keyword,
            'maxPrice' => $maxPrice ?? 1000,
            'minPrice' => $minPrice ?? 0,
            'barValueMin' => $barMinValue,
            'barValueMax' => $barMaxValue,
            'orderBy' => $orderBy,
            'status' => $requestForm['status'] ?? null,
            'tag' => $requestForm['tag'] ?? null,
        ]);
    }


    // public function index($moduleId = null)
    // {
    //     $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
    //     $barMinValue = request()->form && isset(request()->form['barMinValue']) ? request()->form['barMinValue'] : null;
    //     $barMaxValue = request()->form && isset(request()->form['barMaxValue']) ? request()->form['barMaxValue'] : null;
    //     $maxPrice = Product::when(auth()?->user()?->hasRole('admin'), function ($query) use ($moduleId) {
    //         $query->whereHas('standardTags', function ($subQuery) use ($moduleId) {
    //             $subQuery->where('id', $moduleId);
    //         });
    //     }, function ($query) use ($moduleId) {
    //         $query->where('user_id', auth()->id())->whereHas('standardTags', function ($subQuery) use ($moduleId) {
    //             $subQuery->where('id', $moduleId);
    //         });
    //     })->pluck('max_price')->max();

    //     $minPrice = Product::when(auth()->user()?->hasRole('admin'), function ($query) use ($moduleId) {
    //         $query->whereHas('standardTags', function ($subQuery) use ($moduleId) {
    //             $subQuery->where('id', $moduleId);
    //         });
    //     }, function ($query) use ($moduleId) {
    //         $query->where('user_id', auth()->id())->whereHas('standardTags', function ($subQuery) use ($moduleId) {
    //             $subQuery->where('id', $moduleId);
    //         });
    //     })->pluck('price')->min();

    //     $limit = \config()->get('settings.pagination_limit');
    //     $events = Product::with(['events', 'user'])->when(auth()?->user()?->hasRole('admin'), function ($query) use ($moduleId, $barMinValue, $barMaxValue) {
    //         $query->moduleBasedProducts($moduleId)->where('user_id', '!=', null)->with(['mainImage'])->where(function ($query) use ($barMinValue, $barMaxValue) {
    //             if (request()->keyword) {
    //                 $keyword = request()->keyword;
    //                 $query->where('name', 'like', '%' . $keyword . '%');
    //             }
    //             if (request()->form && isset(request()->form['status'])) {
    //                 $query->where('status', request()->form['status']);
    //             }
    //             if (request()->form && isset(request()->form['barMinValue']) && isset(request()->form['barMaxValue'])) {
    //                 $query->whereBetween('price', [$barMinValue, $barMaxValue]);
    //             }
    //             if (request()->form && isset(request()->form['tag'])) {
    //                 $query->whereHas('standardTags', function ($subQuery) {
    //                     $subQuery->where('id', request()->form['tag']);
    //                 });
    //             }
    //         });
    //     }, function ($query) use ($moduleId, $barMinValue, $barMaxValue) {
    //         $query->where('user_id', auth()->id())->moduleBasedProducts($moduleId)->with(['mainImage'])->where(function ($query) use ($barMinValue, $barMaxValue) {
    //             if (request()->keyword) {
    //                 $keyword = request()->keyword;
    //                 $query->where('name', 'like', '%' . $keyword . '%');
    //             }
    //             if (request()->form && isset(request()->form['status'])) {
    //                 $query->where('status', request()->form['status']);
    //             }
    //             if (request()->form && isset(request()->form['barMinValue']) && isset(request()->form['barMaxValue'])) {
    //                 $query->whereBetween('price', [$barMinValue, $barMaxValue]);
    //             }
    //             if (request()->form && isset(request()->form['tag'])) {
    //                 $query->whereHas('standardTags', function ($subQuery) {
    //                     $subQuery->where('id', request()->form['tag']);
    //                 });
    //             }
    //         });
    //     })
    //         ->withCount('comments')
    //         ->orderBy('id', $orderBy)
    //         ->paginate($limit);

    //     return inertia('Events::Index', [
    //         'eventList' => $events,
    //         'searchedKeyword' => request()->keyword,
    //         'maxPrice' => $maxPrice ? $maxPrice : 1000,
    //         'minPrice' => $minPrice ? $minPrice : 0,
    //         'barValueMin' => $barMinValue,
    //         'barValueMax' => $barMaxValue,
    //         'orderBy' => request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc',
    //         'status' => request()->form && isset(request()->form['status']) ? request()->form['status'] : null,
    //         'tag' => request()->form && isset(request()->form['tag']) ? request()->form['tag'] : null,
    //     ]);
    // }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($moduleId)
    {
        $mediaSizes = \config()->get('events.media.events');
        $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
            $query->where('L1', $moduleId);
        })->select(['id', 'name as text', 'slug'])->get();

        return Inertia::render('Events::Create', ['mediaSizes' => $mediaSizes, 'levelTwoTags' => $levelTwoTags]);
    }

    /**
     * Store a newly created resource in storage.
     * @param EventRequest $request
     * @return Renderable
     */
    public function store(EventRequest $request, $moduleId)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('events');
            $product = Product::create(array_merge($request->all(), [
                'user_id' => auth()->id(),
            ]));
            $product->events()->create([
                'event_date' => $request->event_date,
                'event_location' => $request->event_location,
                'performer' => $request->performer,
                'away_team' => $request->away_team,
                'event_ticket' => $request->ticket_url,
            ]);

            \flash('events created successfully.', 'success');
            DB::commit();
            return \redirect()
                ->route('events.dashboard.events.index', $moduleId);
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

    public function show($moduleId, $uuid)
    {
        try {
            $limit = \config()->get('settings.pagination_limit');
            $product = Product::with(['secondaryImages', 'mainImage', 'user', 'events'])->where('uuid', $uuid)->firstOrFail();
            $comments = Comment::with('user')
                ->whereHas('product', function ($query) use ($uuid) {
                    $query->where('uuid', $uuid);
                })->where(function ($query) {
                    $keyword = request()->keyword;
                    $query->whereHas('user', function ($query) use ($keyword) {
                        $query->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere('comment', 'like', '%' . $keyword . '%')
                        ->orWhere('created_at', 'like', '%' . $keyword . '%');
                })->latest()->paginate($limit);


            return inertia('Events::Show', [
                'product' => $product,
                'commentList' => $comments,
                'searchedKeyword' => request()->keyword,
            ]);
        } catch (ModelNotFoundException $e) {
            return \back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($moduleId, $uuid)
    {
        try {
            $events = Product::with('events')->where('uuid', $uuid)->firstOrfail();
            $productLevelTwoTag = $events->standardTags()->whereHas('levelTwo', function ($query) use ($moduleId) {
                $query->where('L1', $moduleId);
            })->first();
            $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
                $query->where('L1', $moduleId);
            })->select(['id', 'name as text', 'slug'])->get();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Event', 'danger');
            return back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return inertia('Events::Edit', [
            'event' => $events,
            'levelTwoTags' => $levelTwoTags,
            'productLevelTwoTag' => $productLevelTwoTag
        ]);
    }



    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(EventRequest $request, $moduleId, $uuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('events');
            $product = Product::whereUuid($uuid)->firstOrFail();

            $product->update(array_merge($request->all(), [
                'user_id' => auth()->id(),
            ]));

            $product->events()->update([
                'event_date' => $request->event_date,
                'event_location' => $request->event_location,
                'performer' => $request->performer,
                'away_team' => $request->away_team,
                'event_ticket' => $request->ticket_url,
            ]);
            \flash('Event updated successfully.', 'success');
            DB::commit();
            return \back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Blog', 'danger');
            DB::rollBack();
            return \back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \back();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($moduleId, $uuid,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $event = Product::where('uuid', $uuid)->firstOrfail();
            $event->delete();
            flash('Event deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('events.dashboard.events.index', [$moduleId, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Event', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }


    /**
     * get of specified resource from storage.
     *
     * @param int $moduleId
     * @return \Illuminate\Http\Response
     */
    public function getTags(Request $request, $moduleId, $tagId, $level)
    {
        try {
            $nextLevelTags = [];
            $productLevelThreeTag = null;
            $productLevelFourTag = null;
            if ($level == 2) {
                if (request()->event) {
                    $product = Product::findOrFail(request()->event);
                    $productLevelThreeTag = $product->standardTags()->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                        $query->where('L1', $moduleId)->where('L2', $tagId);
                    })->select(['id', 'name as text', 'slug'])->first();
                } else {
                    $nextLevelTags = StandardTag::where('name', 'like', '%' . request()->keyword . '%')->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                        $query->where('L1', $moduleId)->where('L2', $tagId);
                    })->select(['id', 'name as text', 'slug'])->paginate(50);
                }
            } else {
                if (request()->event) {
                    $product = Product::findOrFail(request()->event);
                    $productLevelFourTag = StandardTag::whereHas('productTags', function ($query) use ($moduleId, $tagId) {
                        $query->where('product_id', request()->event)->whereHas('standardTags', function ($subQuery) use ($moduleId, $tagId) {
                            $subQuery->whereIn('id', [$moduleId, $tagId, request()->levelTwoTag])->select('*', DB::raw('count(*) as total'))
                                ->having('total', '>=', 3);
                        });
                    })->whereHas('tagHierarchies', function ($query) use ($moduleId, $tagId) {
                        $query->where('L1', $moduleId)->where('L2', request()->levelTwoTag)->where('L3', $tagId);
                        $query->where('level_type', 4);
                    })->select(['id', 'name as text', 'slug'])->first();
                } else {
                    $nextLevelTags = StandardTag::where('name', 'like', '%' . request()->keyword . '%')->whereHas('tagHierarchies', function ($query) use ($moduleId, $tagId) {
                        $query->where('L1', $moduleId)
                            ->where(function ($query) {
                                $query->where('L2', request()->levelTwoTag);
                            })->where(function ($query) use ($tagId) {
                                $query->where('L3',  $tagId);
                            })->where('level_type', 4);
                    })->select(['id', 'name as text', 'slug'])->paginate(50);
                }
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'tags' => $nextLevelTags,
                'productLevelThreeTag' => $productLevelThreeTag,
                'productLevelFourTag' => $productLevelFourTag
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['danger' => $e->getMessage()]);
        }
    }

    /**
     * change status of specified resource from storage.
     *
     * @param  int $uuid, $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($moduleId, $uuid)
    {
        try {
            ModuleSessionManager::setModule('blogs');
            $product = Product::where('uuid', $uuid)->firstOrfail();
            $previousProductStatus = $product->status;
            if ($previousProductStatus == 'inactive' && !$product->mainImage()->exists()) {
                flash('Primary image not found. Cannot proceed with status activation.', 'danger');
                return redirect()->back();
            }
            if ($previousProductStatus == 'inactive') {
                //checking subscription permissions starts
                if (!auth()?->user()?->hasRole('admin') && !auth()?->user()?->hasRole('newspaper')) {
                    $subscriptionPermission = $this->checkGrantedProducts($moduleId, auth()->id());
                    if (!$subscriptionPermission) {
                        flash('You can not change status for this blog due to subscription limitations.', 'danger');
                        return \redirect()->back();
                    }
                }
                //checking subscription permissions ends
            }
            $check = ProductTagsLevelManager::checkProductTagsLevel($product);
            if (!$check) {
                flash('Tag error not resolved', 'danger');
            } else {
                if ($previousProductStatus == 'inactive') {
                    $product->status = 'active';
                } else {
                    $product->status = 'inactive';
                }
                $product->statusChanger();
                $product->saveQuietly();
                $product->refresh(); 
                ProductPriorityManager::updatePriorityBasedOnStatus($product);
                flash('Blog status changed succesfully', 'success');
            }
            return back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Blog', 'danger');
            return back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
    }

    public function searchEventsTags($moduleId)
    {
        try {
            $standardTags = StandardTag::where('type', '!=', 'module')->where('name', 'like', '%' . request()->keyword . '%')->select(['id', 'name as text', 'slug'])
                ->whereHas('productTags', function ($query) use ($moduleId) {
                    $query->whereRelation('standardTags', 'id', $moduleId)->when(!(auth()?->user()?->hasRole('admin')), function ($subQuery) {
                        $subQuery->where('user_id', auth()->id());
                    });
                })->get();

            return response()->json(
                [
                    'status' => JsonResponse::HTTP_OK,
                    'tags' => $standardTags,
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
