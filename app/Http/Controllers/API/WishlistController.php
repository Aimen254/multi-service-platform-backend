<?php

namespace App\Http\Controllers\API;

use App\Models\Wishlist;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Transformers\WishlistTransformer;
use App\Http\Requests\API\WishListRequest;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        try {
            $module = StandardTag::where('type', 'module')->where('id', request()->module)->orWhere('slug', request()->module)->first();
            $options = [
                'module' => $module?->id,
                'type' => $request->input('type'),
                'withLevelTwoTags' => true,
                'withLevelThreeTags' => true,
                'withDelivery' => $request->input('delivery_type'),
                'withProducts' => $request->input('with_products'),
                'levelOneTag' => $module?->id,
            ];
            $limit = $request->input('limit') ? $request->limit : \config()->get('settings.pagination_limit');

            $keyword = $request->input('keyword') ?? '';
            $order = $request->input('order') ?? 'desc';
            if ($request->input('type') == 'business') {
                $whishlist = Wishlist::where('model_type', 'App\Models\Business')->whereHas('business', function ($query) use ($request, $keyword) {
                    $query->when($request->has('keyword'), function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    });
                    $query->where('status', 'active')->whereHas('standardTags', function ($subQuery) {
                        $subQuery->where('id', request()->module)->orWhere('slug', request()->module);
                    });
                })->with(['model' => function ($query) {
                    $query->withCount(['reviews as reviews_avg' => function ($query) {
                        $query->select(DB::raw('avg(rating)'));
                    }]);
                }])->where('user_id', $request->user()->id)->orderBy('created_at', $order)
                    ->paginate($limit);
            } else if ($request->input('type') == 'product') {

                $whishlist = Wishlist::where('model_type', 'App\Models\Product')->where('user_id', $request->user()->id)
                    ->whereHas('product', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%$keyword%")
                            ->orWhere('description', 'like', "%$keyword%");
                    })
                    ->when(in_array($module->slug, ['retail', 'automotive', 'boats', 'services', 'employment', 'government', 'notices', 'real-estate']), function ($query) use($module) {
                       $query->where(function ($subQuery) use($module) {
                           $subQuery->whereHas('product.business', function ($query) {
                               $query->where('status', 'active')
                               ->whereHas('standardTags', function ($subQuery) {
                                   $subQuery->where('id', request()->module)->orWhere('slug', request()->module);
                                })->when(request()->has('business_uuid') || request()->has('business'), function ($query) {
                                    $query->where('uuid', \request()->business_uuid)
                                    ->orWhere('id', \request()->business);
                                });
                            })->when(request()->input('level_two_tag'), function ($query) {
                                $query->whereHas('product', function ($query) {
                                    $query->whereRelation('standardTags', 'slug', request()->input('level_two_tag'));
                                });
                            }, 
                            function ($subQuery) use($module) {
                                if (in_array($module?->slug, ['automotive', 'boats'])) {
                                    $subQuery->orWhereHas('product', function ($subQuery) {
                                        $subQuery->active()->when(request()->has('business_uuid') || request()->has('business'), function ($query) {
                                            $query->whereHas('business', function ($innerQuery) {
                                                $innerQuery->where('status', 'active');
                                                $innerQuery->where('uuid', \request()->business_uuid)
                                                    ->orWhere('id', \request()->business);
                                            });
                                        })
                                        ->whereHas('standardTags', function ($innerQuery) {
                                            $innerQuery->where('id', request()->module)->orWhere('slug', request()->module);
                                        })->when(request()->input('user_id'), function ($innerQuery) {
                                            $innerQuery->where('user_id', request()->input('user_id'));
                                        });
                                    });
                                }
                            });
                        });


                    }, function ($query) use ($module) {
                        $query->whereHas('product', function ($subQuery) use ($module) {
                            $subQuery->when(in_array($module->slug, ['events']), function ($innerQuery) {
                                $innerQuery->whereEventDateNotPassed();
                            });
                            $subQuery->whereHas('standardTags', function ($innerQuery) {
                                $innerQuery->where('id', request()->module)->orWhere('slug', request()->module);
                            })->when(request()->input('user_id'), function ($innerQuery) {
                                $innerQuery->where('user_id', request()->input('user_id'));
                            });
                        });
                    })->whereHas('product', function ($query) use ($module) {
                        $query->when(in_array($module->slug, ['events']), function ($innerQuery) {
                            $innerQuery->whereEventDateNotPassed();
                        });
                        $query->when(request()->input('user_id'), function($subQuery) {
                            $subQuery->where('user_id', request()->input('user_id'));
                        });
                        $query->withoutHiddenProducts();
                        $query->when(request()->input('listing_type'), function ($subQuery) {
                            $subQuery->where('type', request()->input('listing_type'));
                        })->where('status', 'active');
                    })->when(request()->input('filters'), function ($query) {
                        $query->whereHas('product.vehicle', function ($subQuery) {
                            $filterParams = request()->input('filters');
                            if (!is_array($filterParams)) {
                                $filterParams = (array)json_decode($filterParams);
                            }
                            $subQuery->where('maker_id', $filterParams['maker'])->where('model_id', $filterParams['model'])
                                ->whereBetween('year', [$filterParams['from'], $filterParams['to']]);
                        });
                    })->orderBy('created_at', $order)->paginate($limit);
            } elseif ($request->input('type') == 'user') {
                $whishlist = Wishlist::where('model_type', 'App\Models\User')
                    ->where('user_id', $request->user()->id)
                    ->where('module_id', $module?->id)
                    ->whereHas('favoriteUser', function ($query) use ($keyword, $module) {
                        $query->where(function ($subQuery) use ($keyword, $module) {
                            $subQuery->whereHas('products', function ($innerQuery) use ($keyword) {
                                $innerQuery->where('status', 'active');
                            });
                            if (request()->input('keyword')) {
                                $keywords = explode(' ', request()->input('keyword'));
                                foreach ($keywords as $keyword) {
                                    $subQuery->where(function ($subQuery) use ($keyword, $module) {
                                        $subQuery->where('first_name', 'like', '%' . $keyword . '%')
                                            ->orWhere('last_name', 'like', '%' . $keyword . '%');
                                        $subQuery->orWhereHas('publicProfiles', function ($query) use ($keyword, $module) {
                                            $query->where(
                                                'name',
                                                'like',
                                                '%' . $keyword . '%'
                                            )->where('module_id', $module?->id);
                                        });
                                    });
                                }
                            }
                        });
                    })
                    ->orderBy('created_at',  $order)->get()->reject(function ($record) use ($keyword) {
                        if ($keyword && $record->wherehas('favoriteUser', function ($query) use ($record) {
                            $query->where('id', $record->model_id)->whereHas('publicProfiles', function ($subQuery) {
                                $subQuery->where('module_id', request()->input('module'));
                            });
                        })->exists()) {
                            if ($record->wherehas('favoriteUser', function ($query) use ($record, $keyword) {
                                $query->where('id', $record->model_id)->whereHas('publicProfiles', function ($subQuery) use ($keyword) {
                                    $subQuery->where('name', 'like', '%' . $keyword . '%')->where('module_id', request()->input('module'));
                                });
                            })->exists()) {
                                return false;
                            } else {
                                return true;
                            }
                        } else {
                            return false;
                        }
                    })->paginate($limit);
            } else {
                $module = StandardTag::where('id', $request->module)->orWhere('slug', $request->module)->first();
                $whishlist = Wishlist::where('model_type', 'App\Models\StandardTag')->whereHas('standardTags', function ($query) use ($module) {
                    $query->whereHas('levelTwo', function ($query) use ($module) {
                        $query->where('L1', $module->id);
                    })->orWhereHas('levelThree', function ($query) use ($module) {
                        $query->where('L1', $module->id);
                    });
                })->where('user_id', $request->user()->id)->whereHas('model', function ($query) {
                    $query->where('status', 'active');
                })->orderBy('id', 'DESC')->paginate($limit);
            }

            $paginate = apiPagination($whishlist, $limit);
            $whishlists = (new WishlistTransformer)->transformCollection($whishlist, $options);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $whishlists,
                'meta' => $paginate
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
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
        try {
            $wishList = new WishList();
            $result = [];
            $result = $wishList->addToWishList($request);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'flag' => $result['flag'],
                'message' => $result['message']
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
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
}
