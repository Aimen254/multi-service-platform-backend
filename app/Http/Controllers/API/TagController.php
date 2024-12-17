<?php

namespace App\Http\Controllers\API;

use App\Models\Tag;
use App\Models\Product;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\ModuleSessionManager;
use Illuminate\Support\Facades\Auth;
use App\Transformers\UserTransformer;
use App\Traits\ProductTagsLevelManager;
use App\Transformers\StandardTagTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TagController extends Controller
{
    public function levelOneTags()
    {
        try {
            $limit = request()->input('limit')
                ? request()->limit : \config()->get('settings.pagination_limit');
            $user = Auth::user();
            if ($user->hasRole('government_staff')) {
                $business = $user->business;
                $moduleTags = $business->standardTags()->where('type', 'module')->paginate($limit);

                $paginate = apiPagination($moduleTags, $limit);

                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'data' => (new StandardTagTransformer)->transformCollection($moduleTags),
                    'meta' => $paginate,
                ], JsonResponse::HTTP_OK);
            } else {
                $moduleTags = StandardTag::active()
                    ->whereType('module')
                    ->where(function ($query) {
                        $query->whereHas('businesses', function ($query) {
                            $query->where('status', 'active');
                        })->orWhereNotIn('id', function ($query) {
                            $query->select('standard_tag_id')
                                ->from('business_standard_tag');
                        });
                    })
                    ->orderBy('name')
                    ->paginate($limit);

                $paginate = apiPagination($moduleTags, $limit);

                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'data' => (new StandardTagTransformer)->transformCollection($moduleTags),
                    'meta' => $paginate,
                ], JsonResponse::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function levelTwoTags($levelOne)
    {
        try {
            $uniqueUsers = [];
            $limit = request()->input('limit')
                ? request()->limit : \config()->get('settings.pagination_limit');
            $productLimit = request()->input('productLimit') ? request()->productLimit : 11;
            $levelOneTag = StandardTag::where('id', $levelOne)->orWhere('slug', $levelOne)->firstOrFail();
            $levelOne = $levelOneTag ? $levelOneTag->id : null;
            $getLevelTags = request()->input('getLevelTags') ? true : false;

            // get rgions of these level two tags
            $regionsLevelTwoTags = ['residential', 'rental'];
            $levelTwoTags = StandardTag::when(request()->input('keyword'), function($query) {
                $query->where('name', 'like', '%' . request()->input('keyword') . '%');
            })->when(!request()->input('inventory'), function ($query) use ($levelOneTag) {
                $validSlugs = ['retail', 'boats', 'employment', 'services', 'government', 'notices', 'real-estate'];
                if (in_array($levelOneTag->slug, $validSlugs)) {
                    $query->whereHas('productTags', function ($subQuery) {
                        $subQuery->whereHas('business', function ($subSubQuery) {
                            $subSubQuery->active();
                        });
                    })->active();
                }
            })->active()
            ->when(request()->input('type') != 'body_style' && request()->input('type') != 'make', function ($query) {
                $query->where('name', 'like', '%' . request()->input('type') . '%');
            })->when(request()->input('business_id'), function ($query) {
                $query->whereRelation('businesses', 'id', request()->input('business_id'));
            })->when((request()->input('user_id') && in_array($levelOneTag->slug, ['taskers'])), function ($query) {
                $query->whereRelation('users', 'id', request()->input('user_id'));
            })->when(filter_var(request()->input('regions'), FILTER_VALIDATE_BOOLEAN), function ($query) use ($regionsLevelTwoTags) {
                $query->whereIn('slug', $regionsLevelTwoTags);
            })->when(!request()->input('inventory'), function ($query) use ($levelOneTag) {
                $query->when(in_array($levelOneTag->slug, ['retail', 'automotive', 'boats', 'employment', 'services', 'government', 'notices', 'real-estate']), function ($query) use($levelOneTag) {
                    $query->where(function ($query) use($levelOneTag) {
                        $query->whereRelation('businesses', 'status', 'active')->when(!in_array($levelOneTag->slug, [ 'boats']), function($subQuery) {
                            // $subQuery->whereHas('productTags', function($subQuery) {
                            //     $subQuery->whereRelation('business', 'status', 'active');
                            // });
                        }, function($subQuery) {
                            $subQuery->orWhereHas('productTags', function ($query) {
                                $query->whereHas('user', function ($innerQuery) {
                                    $innerQuery->where('status', 'active');
                                });
                            });
                        });
                    });
                });

                // check tags have users and status is active
                $query->when(in_array($levelOneTag->slug, ['taskers']), function ($query) {
                    $query->where(function ($query) {
                        $query->whereRelation('users', 'status', 'active');
                    });
                });
                $query->when(in_array($levelOneTag->slug, ['events', 'posts']), function ($query) use ($levelOneTag) {
                    $query->whereHas('productTags', function ($subQuery) use ($levelOneTag) {
                        // check event date if event module
                        $subQuery->when(in_array($levelOneTag->slug, ['events']), function ($innerQuery) {
                            $innerQuery->whereEventDateNotPassed();
                        });

                        // check business if business id or slug
                        $subQuery->when(request()->input('business'), function ($innerQuery) {
                            $innerQuery->where('status', 'active');
                            $innerQuery->whereHas('business', function($query) {
                                $query->where('status', 'active')->where(function($subQuery) {
                                    $subQuery->where('id', request()->input('business'))->orWhere('slug', request()->input('business'));
                                });
                            });
                        });

                        // check if public profile
                        $subQuery->when(in_array($levelOneTag->slug, ['posts']), function($innerQuery) {
                            $innerQuery->whereHas('publicProfile', function($query) {
                                $query->when(request()->input('public_profile_id'), function($subQuery) {
                                    $subQuery->where('id', request()->input('public_profile_id'));
                                }, function($query) {
                                    $query->where('is_public', true)->orWhereHas('followers', function ($q) {
                                        $q->where('public_profile_follower.follower_public_profile_id', request()->input('profile_id'))
                                          ->where('public_profile_follower.status', 'accepted');
                                    });
                                    
                                });
                            });
                        });

                        // check user if user id in request
                        $subQuery->when(request()->input('user_id'), function ($innerQuery) {
                            $innerQuery->where('status', 'active')->where('user_id', request()->input('user_id'))->whereHas('user', function ($query) {
                                $query->where('status', 'active');
                            });
                        }, function ($innerQuery) {
                            $innerQuery->where('status', 'active')->when(request()->input('today_products'), function ($innerQuery) {
                                $innerQuery->whereDate('created_at', today());
                            });
                            $innerQuery->where('status', 'active')->when(request()->input('latest'), function ($innerQuery) {
                                $innerQuery->latest();
                            });
                        });
                    });
                });
            })
            ->when(\request()->input('business') || request()->input('withProducts'), function ($query) use ($levelOneTag, $productLimit) {
                $query->with(['productTags' => function ($query) use ($levelOneTag, $productLimit) {
                    $query->when(request()->input('favoriteProducts'), function ($subQuery) {
                        $subQuery->with(['wishList' => function ($query) {
                            $user = auth('sanctum')->user();
                            $query->where('user_id', $user?->id);
                        }]);
                    });

                    $query->when(in_array($levelOneTag->slug, ['events']), function ($innerQuery) {
                        $innerQuery->whereEventDateNotPassed();
                    });

                    $query->withCount('comments')->withoutHiddenProducts()->whereRelation('standardTags', 'id', $levelOneTag->id)->when(request()->input('today_products'), function ($innerQuery) {
                        $innerQuery->whereDate('created_at', today());
                    });
                    $query->when(in_array($levelOneTag->slug, ['retail', 'automotive', 'boats', 'services', 'employment', 'government', 'notices', 'real-estate']), function ($query) use ($levelOneTag) {
                        $query->where(function ($subQuery) use ($levelOneTag) {
                            $subQuery->whereHas('business', function ($subQuery) {
                                $subQuery->active();
                                $subQuery->when(request()->input('business'), function ($innerQuery) {
                                    $innerQuery->where('slug', request()->input('business'))
                                        ->orWhere('id', request()->input('business'));
                                })->active();
                            })->when(in_array($levelOneTag->slug, ['automotive', 'boats']), function ($query) {
                                $query->when(!request()->input('business'), function($query) {
                                    $query->orWhereHas('user', function ($subQuery) {
                                        $subQuery->where('status', 'active');
                                        $subQuery->when(request()->input('user_id'), function ($innerQuery) {
                                            $innerQuery->where('user_id', request()->input('user_id'));
                                        });
                                    });
                                });
                            });
                        });
                    });

                    $query->when(request()->input('user_id'), function ($subQuery) {
                        $subQuery->where('status', 'active')->where('user_id', request()->input('user_id'))->whereHas('user', function ($query) {
                            $query->where('status', 'active');
                        });
                    });
                    $query->when(request()->input('header_filter'), function ($subQuery) {
                        switch (\request()->header_filter) {
                            case 'delivery':
                                $subQuery->where('is_deliverable', '!=', 0)
                                    ->whereRelation('business.deliveryZone', 'delivery_type', '!=', 0);
                                break;
                            case 'new':
                            case 'used':
                                $subQuery->whereRelation('vehicle', 'type', request()->input('header_filter'));
                                break;
                        }
                    })->latest();
                    $query->when(request()->input('favoriteProducts'), function ($query) {
                        // check if tag heve products in wishlist
                        $user = auth('sanctum')->user();
                        $query->whereRelation('wishList', 'user_id', $user?->id);
                    });
                    $query->when(in_array($levelOneTag->slug, ['retail', 'automotive', 'boats', 'employment', 'services', 'government', 'notices', 'real-estate']), function ($innerQuery) {
                        $innerQuery->with('vehicle')->active();
                    }, function ($innerQuery) {
                        $innerQuery->with('user')->where('status', 'active');
                    });
                    $query->limit($productLimit);
                }]);
            })
                ->whereHas('levelTwo', function ($query) use ($levelOne) {
                    $query->where('L1', $levelOne)->where('level_type', 4)->when(!request()->input('inventory'), function ($query) use ($levelOne) {
                        $query->when(request()->input('user_id'), function ($query) use ($levelOne) {
                            $query->whereHas('standardTags.productTags', function ($subQuery) use ($levelOne) {
                                $subQuery->withoutHiddenProducts();

                                $subQuery->when(request()->input('user_id'), function ($innerQuery) {
                                    $innerQuery->where('status', 'active')->where('user_id', request()->input('user_id'))->whereHas('user', function ($query) {
                                        $query->where('status', 'active');
                                    });
                                });
                                $subQuery->when(request()->input('public_profile_id'), function ($innerQuery) {
                                    $innerQuery->where('status', 'active')->where('public_profile_id', request()->input('public_profile_id'))->whereHas('publicProfile', function ($query) {
                                        $query->where('is_public', true);
                                    });
                                });


                                $subQuery->when(request()->has('header_filter') && \request()->header_filter, function ($subQuery) {
                                    $deliveryFlag = request()->input('header_filter') && (request()->input('header_filter')) == 'delivery' ?  true : false;
                                    if ($deliveryFlag) {
                                        $subQuery->where('is_deliverable', '!=', 0)
                                            ->whereRelation('business.deliveryZone', 'delivery_type', '!=', 0);
                                    }
                                    if (request()->input('header_filter') == 'new' || request()->input('header_filter') == 'used') {
                                        $subQuery->whereRelation('vehicle', 'type', request()->input('header_filter'));
                                    }
                                });
                            });
                        });
                    });
                })
                ->when(request()->input('type') == 'make', function ($query) {
                    $query->where(function ($subQuery) {
                        $subQuery->whereIn('name', \config()->get('automotive.makes'))->where(function ($innerQuery) {
                            $innerQuery->where('name', 'LIKE', '%' . request()->input('keyword') . '%');
                        });
                    });
                })->when(request()->input('type') == 'body_style', function ($query) {
                    $query->where(function ($subQuery) {
                        $subQuery->whereIn('name', \config()->get('automotive.body_styles'))->where(function ($innerQuery) {
                            $innerQuery->where('name', 'LIKE', '%' . request()->input('keyword') . '%');
                        });
                    });
                })->when(request()->input('favoriteProducts'), function ($query) {
                    // check if tag heve products in wishlist
                    $user = auth('sanctum')->user();
                    $query->whereRelation('productTags.wishList', 'user_id', $user?->id);
                })->when(request()->input('review_flag'), function ($query) {
                    // $query->whereHas('vehicleMakeReview');
                });
            $options = [
                'withChildrens' =>  request()->input('withChildrens') ? request()->input('withChildrens') : false,
                'levelOne' => $levelOneTag,
            ];
            $filteredTags = $levelTwoTags->orderBy('name')->get()->reject(function ($tag) use ($levelOne, $levelOneTag) {
                $threeTags = collect();
                if (in_array($levelOneTag->slug, ['retail', 'automotive', 'boats', 'employment', 'services', 'government', 'notices', 'real-estate']) ) {
                    $threeTags = StandardTag::when(!request()->input('inventory'), function ($query) use ($levelOneTag) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->whereRelation('businesses', 'status', 'active')
                                ->orWhereHas('productTags', function ($query) {
                                    $query->whereHas('user', function ($innerQuery) {
                                        $innerQuery->where('status', 'active');
                                    });
                                });
                        });
                        if(in_array($levelOneTag->slug, ['retail', 'boats', 'employment', 'services', 'government', 'notices', 'real-estate'])){
                            $query->whereHas('productTags', function ($query)  {
                                $query->withoutHiddenProducts();
                                $query->where('status', 'active');
                                $query->when(\request()->input('business'), function ($query) {
                                    $query->whereHas('business', function ($subQuery) {
                                        $subQuery->where('slug', request()->input('business'))
                                            ->orWhere('id', request()->input('business'));
                                    });
                                });
                            });
                        }
                    })->whereHas('levelThree', function ($query) use ($tag, $levelOne) {
                        $query->where('L1', $levelOne)->where('L2', $tag->id);
                    })->active()->get();
                } elseif (in_array($levelOneTag->slug, ['taskers'])) { // check tags have users and status is active
                    $threeTags = StandardTag::when(!request()->input('inventory'), function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->whereRelation('users', 'status', 'active');
                        });
                        $query->whereHas('productTags', function ($query) {
                            $query->withoutHiddenProducts();
                            $query->where('status', 'active');
                        });
                    })->whereHas('levelThree', function ($query) use ($tag, $levelOne) {
                        $query->where('L1', $levelOne)->where('L2', $tag->id);
                    })->active()->get();
                } else {
                    $threeTags = StandardTag::when(!request()->input('inventory'), function ($query) use ($levelOne, $tag, $levelOneTag) {
                        $query->whereHas('productTags', function ($query) use ($levelOne, $tag, $levelOneTag) {
                            $query->when(in_array($levelOneTag->slug, ['events']), function ($innerQuery) {
                                $innerQuery->whereEventDateNotPassed();
                            });
                            $query->withoutHiddenProducts();
                            $query->whereRelation('standardTags', 'id', $levelOne)->whereRelation('standardTags', 'id', $tag->id)->where('status', 'active');
                        });
                    })->whereHas('levelThree', function ($query) use ($tag, $levelOne) {
                        $query->where('L1', $levelOne)->where('L2', $tag->id);
                    })->active()->get();
                }
                if ($threeTags->count() == 0) {

                    return true;
                }
            })->values();

            // get single unique users against their multiple products
            if (filter_var(request()->input('unique_users'), FILTER_VALIDATE_BOOLEAN)) {
                $filteredTags->each(function ($record) use (&$uniqueUsers) {
                    $productTags = $record->productTags;
                    foreach ($productTags as $productTag) {
                        $user = $productTag->user;

                        $isUserExists = collect($uniqueUsers)->contains('id', $user?->id);
                        if (!$isUserExists && $user) {
                            $uniqueUsers[] = $user;
                        }
                    }
                });
            }

            if (request()->input('pagination')) {
                $levelTwoTags = $filteredTags->paginate($limit);
                $paginate = apiPagination($levelTwoTags, $limit);
                $data = (new StandardTagTransformer)->transformCollection($levelTwoTags->values(), $options);
            } else {
                $data = (new StandardTagTransformer)->transformCollection($filteredTags, $options);
            }


            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $data,
                'authors' => (new UserTransformer)->transformCollection(collect($uniqueUsers), ['withAddress' => true, 'levelOneTag' => $levelOne, 'withBusiness' => filter_var(request()->input('withBusiness'), FILTER_VALIDATE_BOOLEAN), 'getLevelTags' => $getLevelTags]),
                'meta' => request()->input('pagination') ? $paginate : null,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function levelThreeTags($levelOne, $levelTwo = null)
    {
        try {
            $uniqueUsers = [];
            $limit = request()->input('limit')
                ? request()->limit : \config()->get('settings.pagination_limit');
            $levelOneTag = StandardTag::where('id', $levelOne)->orWhere('slug', $levelOne)
                ->firstOrFail();
            $productLimit = request()->input('productLimit') ? request()->productLimit : 11;
            $getLevelTags = request()->input('getLevelTags') ? true : false;
            $levelOne = $levelOneTag ? $levelOneTag->id : null;

            $levelTwoTag = StandardTag::where('id', $levelTwo)->orWhere('slug', $levelTwo)
                ->firstOrFail();
            $levelThreeTags = StandardTag::when(request()->input('keyword'), function($query) {
                $query->where('name', 'like', '%' . request()->input('keyword') . '%');
            })->active()->when(request()->input('business_id'), function ($query) {
                $query->whereRelation('businesses', 'id', request()->input('business_id'));
            })->when((request()->input('user_id') && in_array($levelOneTag->slug, ['taskers'])), function ($query) {
                $query->whereRelation('users', 'id', request()->input('user_id'));
            })->when(!request()->input('inventory'), function ($query) use ($levelOneTag, $levelTwoTag) {
                $query->when($levelOneTag->slug == 'automotive', function ($query) use ($levelOneTag, $levelTwoTag){
                    $query->when(request()->input('ifProducts'), function ($query) use ($levelOneTag, $levelTwoTag) {
                        $query->whereHas('productTags', function ($subQuery) use ($levelOneTag, $levelTwoTag) {
                            $subQuery->active();
                        });
                    }, function ($query) {
                        $query->whereRelation('businesses', 'status', 'active');
                    });
                }, function ($query) use ($levelOneTag, $levelTwoTag) {
                    $query->whereHas('productTags', function ($subQuery) use ($levelOneTag, $levelTwoTag) {
                        $subQuery->when(in_array($levelOneTag->slug, ['events']), function ($innerQuery) {
                            $innerQuery->whereEventDateNotPassed();
                        });

                        // check if public profile
                        $subQuery->when(in_array($levelOneTag->slug, ['posts']), function($innerQuery) {
                            $innerQuery->whereHas('publicProfile', function($query) {
                                $query->when(request()->input('public_profile_id'), function($subQuery) {
                                    $subQuery->where('id', request()->input('public_profile_id'));
                                }, function($query) {
                                    $query->where('is_public', true)->orWhereHas('followers', function ($q) {
                                        $q->where('public_profile_follower.follower_public_profile_id', request()->input('profile_id'))
                                          ->where('public_profile_follower.status', 'accepted');
                                    });
                                    
                                });
                            });
                        });

                        $subQuery->withoutHiddenProducts();
                        $subQuery->where('status', 'active');
                        $subQuery->when(request()->input('user_id'), function ($innerQuery) {
                            $innerQuery->where('status', 'active')->where('user_id', request()->input('user_id'))->whereHas('user', function ($query) {
                                $query->where('status', 'active');
                            });
                        });
                        $subQuery->whereRelation('standardTags', 'id', $levelOneTag->id)->whereRelation('standardTags', 'id', $levelTwoTag->id);
                        $subQuery->when(request()->has('header_filter') && \request()->header_filter, function ($subQuery) {
                            $deliveryFlag = request()->input('header_filter') && (request()->input('header_filter')) == 'delivery' ?  true : false;
                            if ($deliveryFlag) {
                                $subQuery->where('is_deliverable', '!=', 0)
                                    ->whereRelation('business.deliveryZone', 'delivery_type', '!=', 0);
                            }
                            if (request()->input('header_filter') == 'new' || request()->input('header_filter') == 'used') {
                                $subQuery->whereRelation('vehicle', 'type', request()->input('header_filter'));
                            }
                        });
                        $subQuery->when(\request()->has('business'), function ($innerQuery) {
                            $business = Business::where('slug', request()->input('business'))->orWhere('id', request()->input('business'))->first();
                            $innerQuery->where('business_id', $business->id);
                        });
                        $subQuery->when(in_array($levelOneTag->slug, ['retail', 'automotive', 'boats', 'employment', 'services', 'government', 'notices', 'real-estate']), function ($innerQuery) use($levelOneTag) {
                            $innerQuery->when(!in_array($levelOneTag->slug, ['automotive', 'boats']), function($subQuery) {
                                $subQuery->whereRelation('business', 'status', 'active');
                            }, function($subQuery) {
                                $subQuery->active();
                            });
                        }, function ($innerQuery) {
                            $innerQuery->where('status', 'active');
                        });
                    });
                });
            })->whereHas('levelThree', function ($query) use ($levelOne, $levelTwo) {
                $query->where(function ($query) use ($levelOne, $levelTwo) {
                    $query->where('L1', $levelOne)->where('L2', $levelTwo)->where('level_type', 4)
                        ->orWhereHas('levelTwo', function ($subQuery) use ($levelTwo, $levelOne) {
                            $subQuery->where('L1', $levelOne)->where('slug', $levelTwo);
                        });
                })->when(request()->input('type') == 'make', function ($query) {
                    $query->where(function ($subQuery) {
                        $subQuery->whereIn('name', \config()->get('boats.makes'));
                    });
                });
            })->when(request()->input('favoriteProducts'), function ($query) {
                // $query->whereHas('productTags.wishList');
                $query->where(function ($subQuery) {
                    $subQuery->withoutHiddenProducts();
                    $subQuery->whereHas('wishList');
                });
            })->when(request()->input('review_flag'), function ($query) {
                $query->with('productTags.vehicle');
            })->when((!request()->input('type')) && !request()->input('inventory') || \request()->has('business'), function ($query) use ($levelOneTag, $levelTwoTag, $productLimit) {
                $query->with(['productTags' => function ($query) use ($levelOneTag, $levelTwoTag, $productLimit) {
                    $query->when(in_array($levelOneTag->slug, ['events']), function ($innerQuery) {
                        $innerQuery->whereEventDateNotPassed();
                    });
                    $query->withCount('comments')
                    ->withoutHiddenProducts();
                    $query->where('status', 'active');
                    // if ($levelOneTag->slug !== 'notices') {
                    //     $query->whereNotNull('user_id');
                    // }
                    $query->when(request()->input('user_id'), function ($innerQuery) {
                        $innerQuery->where('user_id', request()->input('user_id'))->whereHas('user', function ($query) {
                            $query->where('status', 'active');
                        });
                    });
                    $query->when(request()->input('public_profile_id'), function ($innerQuery) {
                        $innerQuery->where('public_profile_id', request()->input('public_profile_id'))->whereHas('user', function ($query) {
                            $query->where('status', 'active');
                        });
                    });
                    $query->whereHas('standardTags', function ($subQuery) use ($levelOneTag, $levelTwoTag) {
                        $subQuery->where(['slug' => $levelOneTag->slug, 'slug' => $levelTwoTag->slug]);
                    });
                    $query->when(\request()->has('business'), function ($subQuery) {
                        $subQuery->whereHas('business', function ($subQuery) {
                            $subQuery->where('slug', request()->input('business'))
                                ->orWhere('id', request()->input('business'));
                        });
                    });
                    // $query->when(request()->has('header_filter') && \request()->header_filter, function ($subQuery) {
                    //     $deliveryFlag = request()->input('header_filter') && (request()->input('header_filter')) == 'delivery' ?  true : false;
                    //     if ($deliveryFlag) {
                    //         $subQuery->where('is_deliverable', '!=', 0)
                    //             ->whereRelation('business.deliveryZone', 'delivery_type', '!=', 0);
                    //     }
                    //     if (request()->input('header_filter') == 'new' || request()->input('header_filter') == 'used') {
                    //         $subQuery->whereRelation('vehicle', 'type', request()->input('header_filter'));
                    //     }
                    // })->latest();
                    $query->when(in_array($levelOneTag->slug, ['retail', 'automotive', 'boats', 'employment', 'services', 'government', 'notices', 'real-estate']), function ($innerQuery) {
                        $innerQuery->with('vehicle')
                        ->active();
                    }, function ($innerQuery) {
                        $innerQuery->with('user')->where('status', 'active');
                    });
                    $query->take($productLimit);
                }]);
            });

            $tags = $levelThreeTags->orderBy('name')->paginate($limit);
            $paginate = apiPagination($tags, $limit);

            // get single unique users against their multiple products
            if (filter_var(request()->input('unique_users'), FILTER_VALIDATE_BOOLEAN)) {
                $tags->each(function ($record) use (&$uniqueUsers) {
                    $productTags = $record->productTags;
                    foreach ($productTags as $productTag) {
                        $user = $productTag->user;
                        if ($user) {
                            $isUserExists = collect($uniqueUsers)->contains('id', $user?->id);
                            if (!$isUserExists) {
                                $uniqueUsers[] = $user;
                            }
                        }
                    }
                });
            }

            $options = [
                'level-four-count' => request()->input('level-four-count') ? true : false,
                'levelOneTag' => $levelOneTag,
                'levelTwoTag' => $levelTwoTag->id
            ];
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => (new StandardTagTransformer)->transformCollection($tags, $options),
                'authors' => (new UserTransformer)->transformCollection(collect($uniqueUsers), ['withAddress' => true, 'levelOneTag' => $levelOne, 'withBusiness' => filter_var(request()->input('withBusiness'), FILTER_VALIDATE_BOOLEAN), 'getLevelTags' => $getLevelTags]),
                'meta' => $paginate
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function levelFourTags($levelOne, $levelTwo, $levelThree)
    {
        try {
            $uniqueUsers = [];
            $limit = request()->input('limit')
                ? request()->limit : \config()->get('settings.pagination_limit');
            $productLimit = request()->input('productLimit') ? request()->productLimit : 11;
            $getLevelTags = request()->input('getLevelTags') ? true : false;
            $levelOneTag = StandardTag::where('id', $levelOne)->orWhere('slug', $levelOne)->firstOrFail();
            $levelOne = $levelOneTag ? $levelOneTag->id : null;
            $levelTwo = StandardTag::when(ctype_digit($levelTwo), function ($query) use ($levelTwo) {
                $query->where('id', $levelTwo);
            }, function ($query) use ($levelTwo) {
                $query->where('slug', $levelTwo);
            })->first();
            $levelTwo = $levelTwo ? $levelTwo->id : null;
            $levelThree = StandardTag::when(ctype_digit($levelThree), function ($query) use ($levelThree) {
                $query->where('id', $levelThree);
            }, function ($query) use ($levelThree) {
                $query->where('slug', $levelThree);
            })->first();
            $levelThree = $levelThree ? $levelThree->id : null;
            $levelFourTags = StandardTag::when(!request()->input('inventory'), function ($query) use ($levelOne, $levelTwo, $levelThree, $levelOneTag) {
                $query->whereHas('productTags', function ($query) use ($levelOne, $levelTwo, $levelThree, $levelOneTag) {
                    $query->when(in_array($levelOneTag->slug, ['events']), function ($innerQuery) {
                        $innerQuery->whereEventDateNotPassed();
                    });
                    $query->withoutHiddenProducts();
                    $query->when(request()->input('user_id'), function ($innerQuery) {
                        $innerQuery->where('status', 'active')->where('user_id', request()->input('user_id'))->whereHas('user', function ($query) {
                            $query->where('status', 'active');
                        });
                    });

                    // check if public profile
                    $query->when(in_array($levelOneTag->slug, ['posts']), function($innerQuery) {
                        $innerQuery->whereHas('publicProfile', function($query) {
                            $query->when(request()->input('public_profile_id'), function($subQuery) {
                                $subQuery->where('id', request()->input('public_profile_id'));
                            }, function($query) {
                                $query->where('is_public', true)->orWhereHas('followers', function ($q) {
                                    $q->where('public_profile_follower.follower_public_profile_id', request()->input('profile_id'))
                                    ->where('public_profile_follower.status', 'accepted');
                                });
                                
                            });
                        });
                    });

                    $query->when(request()->has('header_filter') && \request()->header_filter, function ($subQuery) {
                        $deliveryFlag = request()->input('header_filter') && (request()->input('header_filter')) == 'delivery' ?  true : false;
                        if ($deliveryFlag) {
                            $subQuery->where('is_deliverable', '!=', 0)
                                ->whereRelation('business.deliveryZone', 'delivery_type', '!=', 0);
                        }
                        if (request()->input('header_filter') == 'new' || request()->input('header_filter') == 'used') {
                            $subQuery->whereRelation('vehicle', 'type', request()->input('header_filter'));
                        }
                    });
                    $query->when(in_array($levelOneTag->slug, ['retail', 'automotive', 'boats', 'employment', 'services', 'government', 'real-estate']), function ($innerQuery) {
                        $innerQuery->active();
                    }, function ($innerQuery) {
                        $innerQuery->where('status', 'active');
                    });
                    $query->whereHas('standardTags', function ($subQuery) use ($levelOne, $levelTwo, $levelThree) {
                        $subQuery->whereIn('id', [$levelOne, $levelTwo, $levelThree])
                            ->select('*', DB::raw('count(*) as total'))
                            ->having('total', '>=', 3);
                    })->when(request()->input('business'), function ($query) {
                        $query->whereHas('business', function ($query) {
                            $query->where('slug', request()->input('business'))
                                ->orWhere('id', request()->input('business'));
                        });
                    });
                });
            })->whereHas('tagHierarchies', function ($query) use ($levelOne, $levelTwo, $levelThree) {
                $query->where('level_type', 4)->where('L1', $levelOne)->where('L2', $levelTwo)->where('L3', $levelThree);
            })->when(request()->input('favoriteProducts'), function ($query) {
                // $query->whereHas('productTags.wishList');
                $query->where(function ($subQuery) {
                    $subQuery->withoutHiddenProducts();
                    $subQuery->whereHas('wishList');
                });
            });
            if (request()->input('withProducts')) {
                $levelFourTags->with(['productTags' => function ($query) use ($levelOne, $levelTwo, $levelThree, $levelOneTag, $productLimit) {
                    $query->when(in_array($levelOneTag->slug, ['events']), function ($innerQuery) {
                        $innerQuery->whereEventDateNotPassed();
                    });

                    

                    $query->withCount('comments')->withoutHiddenProducts();
                    $query->when(request()->input('user_id'), function ($innerQuery) {
                        $innerQuery->where('status', 'active')->where('user_id', request()->input('user_id'))->whereHas('user', function ($query) {
                            $query->where('status', 'active');
                        });
                    });
                  


                    $query->whereHas('standardTags', function ($subQuery) use ($levelOne, $levelTwo, $levelThree) {
                        $subQuery->whereIn('id', [$levelOne, $levelTwo, $levelThree])
                            ->select('*', DB::raw('count(*) as total'))
                            ->having('total', '>=', 3);
                    });
                    if (request()->input('business')) {
                        $query->whereHas('business', function ($subQuery) {
                            $subQuery->where('slug', request()->input('business'))
                                ->orWhere('id', request()->input('business'));
                        });
                    }
                    $query->when(request()->has('header_filter') && \request()->header_filter, function ($subQuery) {
                        $deliveryFlag = request()->input('header_filter') && (request()->input('header_filter')) == 'delivery' ?  true : false;
                        if ($deliveryFlag) {
                            $subQuery->where('is_deliverable', '!=', 0)
                                ->whereRelation('business.deliveryZone', 'delivery_type', '!=', 0);
                        }
                        if (request()->input('header_filter') == 'new' || request()->input('header_filter') == 'used') {
                            $subQuery->whereRelation('vehicle', 'type', request()->input('header_filter'));
                        }
                    })->latest();
                    $query->when(in_array($levelOneTag->slug, ['retail', 'automotive', 'boats', 'employment', 'services', 'government', 'real-estate']), function ($innerQuery) {
                        $innerQuery->with('vehicle')->active();
                    }, function ($innerQuery) {
                        $innerQuery->with('user')->where('status', 'active');
                    });
                    $query->limit($productLimit);
                }]);
            }
            $levelFourTags = $levelFourTags->orderBy('name')->get();
            if ($levelOneTag && $levelOneTag->slug == 'automotive' && count($levelFourTags) > 0 && !request()->input('withProducts') && !request()->input('product')) {
                $newtag = new StandardTag();
                $newtag->name = "All";
                $newtag->id = StandardTag::count() + 1;
                $newtag->slug = 'all';
                $newtag->type = 'product';
                $levelFourTags->prepend($newtag);
            }


            $levelFourTags = $levelFourTags->paginate($limit);

            // get single unique users against their multiple products
            if (filter_var(request()->input('unique_users'), FILTER_VALIDATE_BOOLEAN)) {
                $levelFourTags->each(function ($record) use (&$uniqueUsers) {
                    $productTags = $record->productTags;

                    foreach ($productTags as $productTag) {
                        $user = $productTag->user;
                        $isUserExists = collect($uniqueUsers)->contains('id', $user->id);

                        if (!$isUserExists) {
                            $uniqueUsers[] = $user;
                        }
                    }
                });
            }

            $data = $levelFourTags->map(function ($tag) {
                return (new StandardTagTransformer)->transform($tag);
            })->values();

            $paginate = apiPagination($levelFourTags, $limit);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $data,
                'authors' => (new UserTransformer)->transformCollection(collect($uniqueUsers), ['withAddress' => true, 'levelOneTag' => $levelOne, 'withBusiness' => filter_var(request()->input('withBusiness'), FILTER_VALIDATE_BOOLEAN), 'getLevelTags' => $getLevelTags]),
                'meta' => $paginate
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getProductTags($uuid)
    {
        try {
            $limit = request()->input('limit')
                ? request()->limit : \config()->get('settings.pagination_limit');
            $product = Product::whereUuid($uuid)->firstOrFail();

            $orphanTags = $product->tags()->asTag()->where(function ($query) {
                $query->where('is_category', true)->whereHas('standardTags_', function ($query) {
                    $query->where('priority', 1);
                });
            })->get()->reject(function ($record) {
                $tag = $record->whereHas('standardTags_', function ($query) use ($record) {
                    $query->where('priority', 1)->where('slug', $record->slug);
                })->first();
                return $tag ? true : false;
            });

            $extraOrphanTags = $product->tags()->asTag()->whereDoesntHave('standardTags_')->get();
            // Getting extra standard tags
            $extraStandardTags = $product->standardTags()->asTag()->wherePriority(4)->whereHas('tags_')->get();
            // Getting tags
            $extraTags = Arr::collapse([$extraOrphanTags, $extraStandardTags]);

            $ignoredOrphanTags = $product->tags()->asTag()->whereHas('standardTags_', function ($query) {
                $query->where('priority', '<>', 1);
            })->orWhereDoesntHave('standardTags_')->where('product_id', $product->id)->get()->reject(function ($record) {
                $tag = $record->whereHas('standardTags_', function ($query) use ($record) {
                    $query->where('priority', '<>', 1)->where('slug', $record->slug);
                })->first();
                return $tag ? true : false;
            });
            // Getting ingnored tags
            $ignoredTags = $product->ignoredTags()->first();
            $productIgnoredTags = [];
            if ($ignoredTags) {
                $data = json_decode($ignoredTags->tags);
                $productIgnoredTags = array_map(function ($item) {
                    return [
                        "text" => $item,
                    ];
                }, $data);
            }
            // Getting ignored standard tags
            $ignoredStandardTags = $product->standardTags()->asTag()->where('type', '<>', 'module')->where('priority', '<>', 1)->get();

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'allproductTags' => Arr::collapse([$orphanTags]),
                'extraTags' => $extraTags,
                'allTags' => StandardTag::asTag()->active()->where('type', '!=', 'module')->get(),
                'allIgnoredAutocomplete' => Arr::collapse([$ignoredOrphanTags, $ignoredStandardTags]),
                'productIgnoredTags' => $productIgnoredTags,
                'allproductBrandTags' => $product->standardTags()->asTag()->where('type', 'brand')->get(),
                'allBrandTags' => StandardTag::asTag()->active()->where('type', 'brand')->get(),
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function storeStandardTags($news, $standardTags): void
    {
        foreach ($standardTags as $id) {
            $standardTag = StandardTag::find($id);
            $existInBothLevels = $standardTag->whereHas('levelThree')->whereHas('tagHierarchies')->where('id', $standardTag->id)->first();
            if ($existInBothLevels) {
                $productLevelThreeTag = $news->standardTags()->where('id', '<>', $standardTag->id)->whereHas('levelTHree')->first();
                if ($productLevelThreeTag) {
                    $exsistInlevelFour = $standardTag->whereHas('tagHierarchies', function ($query) use ($productLevelThreeTag) {
                        $query->where('L3', $productLevelThreeTag->id);
                    })->where('id', $standardTag->id)->first();
                    if ($exsistInlevelFour) {
                        $news->standardTags()->syncWithOutDetaching($standardTag->id);
                    }
                }
            } else {
                $news->standardTags()->syncWithOutDetaching($standardTag->id);
            }
        }
    }

    public function assignTags($moduleId, $uuid)
    {
        try {
            $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first()->slug;
            ModuleSessionManager::setModule($module);
            $news = Product::whereUuid($uuid)->firstOrFail();
            DB::beginTransaction();
            $tags = $this->handleTags($moduleId, request()->input('tags'), $news);
            $categoryTags = collect(json_decode(request()->input('categoryTags')))->pluck('id')->toArray();
            $ignoredTagIds = $this->productIgnoredTags(request()->ignoredTags, $news);
            $productCategoryTags = $news->tags()->asTag()->where(function ($query) {
                $query->where('is_category', true)->whereHas('standardTags_', function ($query) {
                    $query->where('priority', 1);
                });
            })->get()->reject(function ($record) {
                $tag = $record->whereHas('standardTags_', function ($query) use ($record) {
                    $query->where('priority', 1)->where('slug', $record->slug);
                })->first();
                return $tag ? true : false;
            })->pluck('id')->toArray();

            $removeCategoryTags = array_diff($productCategoryTags, $categoryTags);
            if (request()->input('removeOrphans')) {
                $tags['orphanTags'] = array_diff($tags['orphanTags'], json_decode(request()->input('removeOrphans')));
            }

            $orphanTags = Arr::collapse([$tags['orphanTags'], $categoryTags]);
            $orphanTags = array_diff($orphanTags, $removeCategoryTags);
            $news->tags()->sync($orphanTags);
            $standardTagIds =  $news->standardTags()->whereNotIn('type', ['module', 'attribute'])->wherePriority(4)->pluck('id')->toArray();
            $brandTags = collect(json_decode(request()->input('brandTags')))->pluck('id')->toArray();
            $standardTags = Arr::collapse([$tags['standardTags'], $brandTags]);

            if (request()->input('removeStandardTags')) {
                $standardTags = array_diff($standardTags, json_decode(request()->input('removeStandardTags')));
                $news->standardTags()->detach(json_decode(request()->input('removeStandardTags')));
            }

            $removeTags = array_diff($standardTagIds, $standardTags);
            $news->standardTags()->detach($removeTags);

            $this->storeStandardTags($news, $standardTags);

            $previousbrandTags = $news->standardTags()->where('type', 'brand')->pluck('id')->toArray();
            $removeBrandTags = array_diff($previousbrandTags, $brandTags);

            $news->standardTags()->detach($removeBrandTags);
            $news->tags()->detach($ignoredTagIds['ignoredOrphanTagIds']);
            $news->standardTags()->detach($ignoredTagIds['ignoredStandardTagIds']);
            ProductTagsLevelManager::checkProductTagsLevel($news);
            ProductTagsLevelManager::priorityOneTags($news, $removeCategoryTags, 'product_tag');
            ProductTagsLevelManager::priorityFour($news, $tags['orphanTags']);
            ProductTagsLevelManager::priorityTwoTags($news);
            ProductTagsLevelManager::priorityThree($news, null, false, $brandTags, null, count($removeBrandTags) > 0 ? $removeBrandTags : null);
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Tags updated successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Return array of 2 item one for extra tags
     * which are orphan tags and one for standard tags
     */
    public function handleTags($moduleId, $extraTags, $product)
    {
        $newTags = collect(json_decode($extraTags))->pluck('text')->toArray();
        $orphanTags = [];
        $tags = [];
        foreach ($newTags as $newTag) {
            $standardTag = StandardTag::where('slug', orphanTagSlug($newTag))->first();
            // checking if tag is in orphan tags
            if ($standardTag) {
                // tag is in standard tag grab its id for product_standard_tag sync
                \array_push($tags, $standardTag->id);
                //Creating orphan tags
                if ($standardTag->type != 'module' && $standardTag->type != 'industry') {
                    $new = Tag::updateOrCreate([
                        'slug' => Str::slug($newTag)
                    ], [
                        'name' => $newTag,
                        'global_tag_id' => $moduleId,
                        'type' => $standardTag ? $standardTag->type : null,
                        'attribute_id' => $standardTag ? $standardTag->attribute_id : null,
                        'priority' => $standardTag ? $standardTag->priority : null,
                    ]);
                    \array_push($orphanTags, $new->id);
                    $new->standardTags_()->syncWithOutDetaching($standardTag->id);
                }
            } else {

                $tag = Tag::updateOrCreate(['slug' => orphanTagSlug($newTag), 'name' => $newTag], [
                    'priority' => 4,
                ]);
                //if tag is mapped get its mapped id
                if ($tag) {
                    $standardTags = $tag->standardTags_()->whereHas('productTags', function ($query) use ($product) {
                        $query->where('id', $product->id);
                    })->pluck('id');
                    $tags = Arr::collapse([$tags, $standardTags]);
                    \array_push($orphanTags, $tag->id);
                }
            }
        }
        $productTags = $product->standardTags()->pluck('id')->toArray();
        $tags = Arr::collapse([$tags, $productTags]);
        $tags = array_unique($tags);
        $orphan = $product->tags()->pluck('id')->toArray();
        $orphanTags = Arr::collapse([$orphanTags, $orphan]);
        $orphanTags = array_unique($orphanTags);
        return [
            'orphanTags' => $orphanTags,
            'standardTags' => Arr::collapse([$tags])
        ];
    }

    // get and store ignored tags of news
    public function productIgnoredTags($tags, $product)
    {
        $tags = collect(json_decode($tags))->pluck('text')->toArray();
        $product->ignoredTags()->updateOrCreate(
            ['product_id' => $product->id],
            ['tags' => json_encode($tags)]
        );
        $standardTagIds = $product->standardTags()->where('priority', '<>', 1)->whereIn('name', $tags)->pluck('id')->toArray();
        $orphanTagIds = $product->tags()->where('priority', '<>', 1)->whereIn('name', $tags)->pluck('id')->toArray();
        return [
            'ignoredOrphanTagIds' => $orphanTagIds,
            'ignoredStandardTagIds' => $standardTagIds
        ];
    }
}
