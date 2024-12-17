<?php

namespace App\Http\Controllers\API;

use App\Models\Product;;

use App\Models\Business;;

use App\Models\StandardTag;
use App\Traits\TopSearches;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\TagHierarchy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use DonatelloZa\RakePlus\RakePlus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\StoreSearchRequest;
use App\Transformers\Search\SearchHistoryTransformer;
use App\Transformers\Search\SearchProductTransformer;
use App\Transformers\Search\SearchBusinessTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SearchController extends Controller
{
    protected $isProductsExists = false;
    protected $business = null;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SearchRequest $request)
    {
        $relevantKeyword = RakePlus::create(\request()->keyword)->keywords();
        if (is_numeric(request()->keyword) || preg_match('/^\d+(\.\d+)?$/', request()->keyword)) {
            request()->merge([
                'keyword' => $request->keyword
            ]);
        } else {
            request()->merge([
                'keyword' => collect($relevantKeyword)->implode(' ')
            ]);
        }
        if (auth('sanctum')->user()) {
            TopSearches::saveSearch(auth('sanctum')->user());
        }
        $field = 'L1';
        request()->merge([
            'L1' => request()->input('L1') ? StandardTag::where('id', request()->input('L1'))->orWhere('slug', request()->input('L1'))->first()->id : null,
            'L2' => request()->input('L2') ? StandardTag::where('id', request()->input('L2'))->orWhere('slug', request()->input('L2'))->first()->id : null,
            'L3' => request()->input('L3') ? StandardTag::where('id', request()->input('L3'))->orWhere('slug', request()->input('L3'))->first()->id : null
        ]);

        $exact_match = StandardTag::where('name', request()->input('keyword'))
            ->whereHas('tagHierarchies', function ($query) {
                $query->where('level_type', 4);
            })->exists();
        $levelOneTag = StandardTag::where('name', 'like', request()->input('keyword') . '%')->where('type', 'module')->first();
        $partial_match = $exact_match ? false : true;
        $standardTag = StandardTag::where('name', request()->input('keyword'))
            ->where('type', 'product')->first();

        if (request()->input('L1')) {
            $moduleTag = StandardTag::findOrFail(request()->input('L1'));
        } else {
            $moduleTag = StandardTag::where('name', 'like', request()->input('keyword') . '%')->where('type', 'module')->first();
        }

        $tagHierarchies = request()->input('keyword') ?
            TagHierarchy::where(function ($query) use ($levelOneTag, $moduleTag) {
                // if no level is selected
                $query->when(!request()->filled('L1'), function ($subQuery) {
                    $subQuery->whereHas('levelOne', function ($innerQuery) {
                        $innerQuery->searchName(request()->keyword)->filterProducts();
                    });
                });

                // if only level 1 is selected
                $query->when(!request()->filled('L2'), function ($subQuery) use ($levelOneTag) {
                    $subQuery->orWhereHas('levelTwo', function ($innerQuery) {
                        $innerQuery->searchName(request()->keyword)->filterProducts();
                    })->when(request()->filled('L1'), function ($innerQuery) {
                        $innerQuery->where('L1', request()->L1);
                    })->when($levelOneTag, function ($query) use ($levelOneTag) {
                        $query->where('L1', $levelOneTag->id);
                    });
                });

                // if only level 1 and 2 is selected
                $query->when(!request()->filled('L3'), function ($subQuery) use ($levelOneTag, $moduleTag) {
                    $subQuery->orWhereHas('levelThree', function ($innerQuery) use ($moduleTag) {
                        $innerQuery->searchName(request()->keyword)->filterProducts();
                    })->when(request()->filled('L2'), function ($innerQuery) {
                        $innerQuery->where('L1', request()->L1)->where('L2', request()->L2);
                    })->when(request()->filled('L1'), function ($innerQuery) {
                        $innerQuery->where('L1', request()->L1);
                    })->when($levelOneTag, function ($query) use ($levelOneTag) {
                        $query->where('L1', $levelOneTag->id);
                    });
                });
            })
            ->orWhereHas('standardTags', function ($query) use ($moduleTag) {
                $query->searchName(request()->keyword)->filterProducts()
                    ->when(request()->filled('L1'), function ($subQuery) {
                        $subQuery->whereHas('tagHierarchies', function ($innerQuery) {
                            $innerQuery->where('L1', request()->L1);
                        });
                    });
            })
            ->when(\request()->filled('L3'), function ($query) {
                $query->where('L1', \request()->L1)->where('L2', \request()->L2)
                    ->where('L3', \request()->L3);
            })
            ->whereHas('levelOne', function ($query) {
                $query->filterProducts();
            })
            ->get()->unique($field) : [];
        $tag_hierarchies = $this->getHierarchy($tagHierarchies, $field, $partial_match, $moduleTag?->slug);

        $match_type = count($tag_hierarchies) == 0 && !$standardTag
            ? 'relevant_match' : ($exact_match ? 'exact_match' : 'partial_match');

        $businesses_matched = !$exact_match && $match_type != 'relevant_match' && !(\request()->filled('L2') || \request()->filled('L3')) && \request()->input('L1')
            ? $this->getRelevantStores($request->keyword, request()->L1) : [];

        if ($match_type == 'relevant_match') {
            if (count($relevantKeyword) > 0) {
                $levelFours = StandardTag::whereHas('tagHierarchies', function ($query) {
                    $query->where("level_type", 4);
                })->where(function ($query) use ($relevantKeyword) {
                    foreach ($relevantKeyword as $keyword) {
                        $query->orWhere('name', 'like', $keyword . '%');
                    }
                })->get(['id', 'name']);
                $levelFourIds = Arr::pluck($levelFours, 'id');
                $levelFourNames = Arr::pluck($levelFours, 'name');
                $namesArray = [];
                foreach ($levelFourNames as $key => $levelFourName) {
                    $names = [Str::singular($levelFourName), Str::plural($levelFourName)];
                    $namesArray = \array_merge($namesArray, $names);
                }
                $relevantKeyword = array_values(array_unique(\array_merge(
                    RakePlus::create(\request()->keyword)->keywords(),
                    $namesArray
                )));

                // updating the search keywords
                $standardTags = StandardTag::whereHas('tags_', function ($query) use ($relevantKeyword) {
                    $query->whereIn('slug', $relevantKeyword);
                })
                    ->where('priority', '!=', 4)
                    ->pluck('name')->toArray();
                $mergedKyewords = \array_unique(\array_map('strtolower', array_merge($relevantKeyword, $standardTags)));

                // convert to string
                $searchString = collect($mergedKyewords)->implode(' ');

                // convert string to array then check remove the duplicate words from it and again convert it to string
                request()->merge([
                    'keyword' => \collect(array_unique(explode(" ", $searchString)))->implode(' ')
                ]);

                $tagHierarchies = TagHierarchy::when(request()->filled('L1'), function ($query) {
                    $query->where('L1', request()->input('L1'));
                })->whereHas('standardTags.productTags', function ($query) {
                    $query->active()->matchOrphanTags(request()->keyword);
                })->get()->unique($field);

                // check this level four exist in these hierarchy
                $existLeveFour = TagHierarchy::when(request()->filled('L1'), function ($query) {
                    $query->where('L1', request()->input('L1'));
                })->whereHas('standardTags.productTags', function ($query) {
                    $query->active()->matchOrphanTags(request()->keyword);
                })->whereHas('standardTags', function ($query) use ($levelFourIds) {
                    $query->whereIn('id', $levelFourIds);
                })->exists();


                $tag_hierarchies = $this->getHierarchy($tagHierarchies, $field, \false, ['relevantMatch' => \true, 'levelFourIds' => $levelFourIds, 'existLevelFour' => $existLeveFour]);
            }
        }

        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => [
                'tag_hierarchies' => $tag_hierarchies,
                'match_type' => $match_type,
                'businesses_matched' => $businesses_matched,
                'isPrdouctsExists' => $this->isProductsExists,
                'searched_keyword' => $relevantKeyword
            ],
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Get listing of tags according to hierarchy.
     *
     * @param $results, $field, $options, $stringMatched
     * @return array
     */
    private function getHierarchy($results, $field, $partial_match = false, $moduleTag = null, $options = [], $stringMatched = false)
    {
        $currentLevel = NULL;
        $data = [];
        $options['currentField'] = $field;
        $options['stringMatched'] = $stringMatched;
        $options['levelMatchedOn'] = NULL;
        $options['exactMatchId'] = NULL;
        $options['partialMatch'] = $partial_match;
        $options['relevantMatch'] = isset($options['relevantMatch'])
            ? $options['relevantMatch'] : \false;
        $options['moduleTag'] = $moduleTag;
        if (!$partial_match && StandardTag::whereHas('tagHierarchies')->whereName(\request()->keyword)->exists()) {
            $options['exactMatchId'] = StandardTag::whereHas('tagHierarchies')
                ->whereName(\request()->keyword)->first()->id;
        }

        foreach ($results as $key => $result) {
            switch ($field) {
                case 'L3':
                    $level = $result->levelThree;
                    $currentLevel = filter_var($field, FILTER_SANITIZE_NUMBER_INT);
                    $options['L1'] = request()->filled('L1') ? request()->L1 : $options['L1'];
                    $options['L2'] = request()->filled('L2') ? request()->L2 : $options['L2'];
                    $options['L3'] = request()->filled('L3') ? request()->L3 : $level->id;
                    $options['nextField'] = 'L4';
                    break;
                case 'L2':
                    $level = $result->levelTwo;
                    $currentLevel = filter_var($field, FILTER_SANITIZE_NUMBER_INT);
                    $options['L1'] = request()->filled('L1') ? request()->L1 : $options['L1'];
                    $options['L2'] = request()->filled('L2') ? request()->L2 : $level?->id;
                    Log::info(json_encode($level));
                    $options['nextField'] = 'L3';
                    break;
                default:
                    $level = $result->levelOne;
                    $currentLevel = filter_var($field, FILTER_SANITIZE_NUMBER_INT);
                    $options['L1'] = request()->filled('L1') ? request()->L1 : $level->id;
                    $options['nextField'] = 'L2';
                    break;
            }
            if ($stringMatched) {
                $childrensData = $this->hierarchyQuery($options);
            } else if (Str::contains(Str::lower($level->name), Str::lower(request()->keyword))) {
                switch (\request()) {
                    case \request()->filled('L3'):
                        $stringMatched = $currentLevel == 3 || $currentLevel == 2 ? \false : \true;
                        break;
                    case \request()->filled('L2'):
                        $stringMatched = $currentLevel == 2 ? \false : \true;
                        break;
                    case \request()->filled('L1'):
                        $stringMatched = $currentLevel == 1 ? \false : \true;
                        break;

                    default:
                        $stringMatched = true;
                        break;
                }

                $options['levelMatchedOn'] = (int)$currentLevel;
                $options['stringMatched'] = $stringMatched;
                $childrensData = $this->hierarchyQuery($options);
            } else {
                $childrensData = $this->hierarchyQuery($options);
            }

            $data[] = [
                'id' => $level?->id,
                'name' => $level?->name,
                'slug' => $level?->slug,
                'childrens' => isset($childrensData) && count($childrensData) > 0
                    ? $this->getHierarchy($childrensData, $options['nextField'], $partial_match, $options['moduleTag'], $options, $stringMatched)
                    : $this->multiTagsTransformer($result, $options),
            ];
            if (!empty($data) && isset($data[count($data) - 1]['childrens'])) {
                $lastIndex = $data[count($data) - 1];
            }
            if (isset($lastIndex['childrens']) && count($lastIndex['childrens']) <= 0) {
                $data = collect($data);
                $data = $data->reject(function ($record) {
                    return count($record['childrens']) > 0 ? \false : \true;
                });
            } else {
                if ($currentLevel == 3 && $options['relevantMatch']) {
                    $lastIndex = collect($data)->last();
                    if (isset($lastIndex['childrens']) && count($lastIndex['childrens']) > 0) {
                        $data = collect($data);
                        $data = $data->map(function ($record) {
                            $record['avg_relevancy'] = array_sum(
                                array_column($record['childrens']->toArray(), 'avg_relevancy')
                            ) / count($record['childrens']);
                            return $record;
                        });
                    }
                }
            }
        }

        return $data;
    }

    /**
     * filter out listing according to hierarchy.
     *
     * @param $options
     */
    private function hierarchyQuery($options)
    {
        $options = json_decode(\json_encode($options));
        return TagHierarchy::when($options->nextField == 'L2', function ($query) use ($options) {
            $query->where('L1', $options->L1)->whereNotNull($options->nextField)
                ->whereHas('levelTwo', function ($subQuery) use ($options) {
                    $subQuery->whereHas('productTags', function ($subQuery) use ($options) {
                        $subQuery->active();
                        $subQuery->when($this->business, function ($innerQuery) {
                            $innerQuery->where('business_id', $this->business->id);
                        });
                        $subQuery->when($options->relevantMatch, function ($innerQuery) {
                            $innerQuery->matchOrphanTags(request()->keyword);
                        });
                    });
                })->whereHas('levelThree', function ($subQuery) use ($options) {
                    $subQuery->filterProducts();
                })->when(\request()->filled('L2'), function ($subQuery) {
                    $subQuery->where('L2', \request()->L2);
                })->when($options->relevantMatch, function ($subQuery) use ($options) {
                    $subQuery->WhereHas('standardTags', function ($innerQuery) use ($options) {
                        if (count($options->levelFourIds) > 0 && $options->existLevelFour) {
                            $innerQuery->whereIn('id', $options->levelFourIds);
                        }
                    });
                });
            if (!$options->stringMatched && !($options->currentField == 'L1' && $options->levelMatchedOn) && !\request()->filled('L2') && !$options->relevantMatch) {
                $query->whereHas('levelTwo', function ($subQuery) {
                    $subQuery->searchName(request()->keyword);
                })->orWhereHas('levelThree', function ($subQuery) {
                    $subQuery->searchName(request()->keyword);
                })->orWhereHas('standardTags', function ($subQuery) {
                    $subQuery->searchName(request()->keyword);
                });
            }
        })
            ->when($options->nextField == 'L3', function ($query) use ($options) {
                $query->where('L1', $options->L1)->where('L2', $options->L2)
                    ->whereNotNull($options->nextField)
                    ->whereHas('levelThree', function ($subQuery) use ($options) {
                        $subQuery->whereHas('productTags', function ($subQuery) use ($options) {
                            $subQuery->active();
                            $subQuery->when($this->business, function ($innerQuery) {
                                $innerQuery->where('business_id', $this->business->id);
                            });
                            $subQuery->when($options->relevantMatch, function ($innerQuery) {
                                $innerQuery->matchOrphanTags(request()->keyword);
                            });
                        });
                    })->whereHas('standardTags', function ($subQuery) use ($options) {
                        $subQuery->filterProducts()->when($options->relevantMatch, function ($innerQuery) use ($options) {
                            if (count($options->levelFourIds) > 0 && $options->existLevelFour) {
                                $innerQuery->whereIn('id', $options->levelFourIds);
                            }
                        });
                    })->when(\request()->filled('L3'), function ($subQuery) {
                        $subQuery->where('L3', \request()->L3);
                    });
                if (!$options->stringMatched && !($options->currentField == 'L2' && $options->levelMatchedOn) && !\request()->filled('L3') && !$options->relevantMatch) {
                    $query->whereHas('levelThree', function ($subQuery) {
                        $subQuery->searchName(request()->keyword);
                    });
                    $query->orWhereHas('standardTags', function ($subQuery) {
                        $subQuery->searchName(request()->keyword)->filterProducts();
                    });
                }
            })
            ->when($options->nextField == 'L4', function ($query) use ($options) {
                $query->where('L1', $options->L1)->where('L2', $options->L2)->where('L3', $options->L3)
                    ->whereNotNull($options->nextField)
                    ->whereHas('standardTags', function ($subQuery) use ($options) {
                        $subQuery->whereHas('productTags', function ($subQuery) use ($options) {
                            $subQuery->active();
                            $subQuery->when($this->business, function ($innerQuery) {
                                $innerQuery->where('business_id', $this->business->id);
                            });
                            $subQuery->when($options->relevantMatch, function ($innerQuery) {
                                $innerQuery->matchOrphanTags(request()->keyword);
                            });
                        })->when($options->relevantMatch, function ($subQuery) use ($options) {
                            if (count($options->levelFourIds) > 0 && $options->existLevelFour) {
                                $subQuery->whereIn('id', $options->levelFourIds);
                            }
                        });
                    });
                if (!$options->stringMatched && !($options->currentField == 'L3' && $options->levelMatchedOn)  && !$options->relevantMatch) {
                    $query->whereHas('standardTags', function ($subQuery) use ($options) {
                        $subQuery->when($this->business, function ($subQuery) {
                            $subQuery->whereHas('productTags', function ($subQuery) {
                                $subQuery->where('business_id', $this->business->id);
                            });
                        });
                        $subQuery->searchName(request()->keyword)->filterProducts();
                    });
                }
            })->get()->unique($options->nextField)->reject(function ($record) use ($options) {
                if ($options->nextField == 'L2') {
                } else if ($options->nextField == 'L3') {
                    $count = $record->standardTags()
                        ->whereHas('productTags', function ($query) use ($record, $options) {
                            $query->when($this->business, function ($subQuery) {
                                $subQuery->where('business_id', $this->business->id);
                            });
                            $query->whereHas('standardTags', function ($subQuery) use ($record, $options) {
                                if ($options->exactMatchId) {
                                    $subQuery->whereIn('id', [$record->L1, $record->L2, $record->L3, $options->exactMatchId])->select('*', DB::raw('count(*) as total'))
                                        ->having('total', '>=', 4);
                                } else {
                                    $subQuery->whereIn('id', [$record->L1, $record->L2, $record->L3])
                                        ->select('*', DB::raw('count(*) as total'))
                                        ->having('total', '>=', 3);
                                }
                            })->when($options->relevantMatch, function ($innerQuery) {
                                $innerQuery->matchOrphanTags(request()->keyword);
                            });
                        })->count();
                    return !$count > 0 ? \true : \false;
                } else {
                    return \false;
                }
            });
    }

    /* transforming data of childrens of tags.
    *
    * @param $hierarchy, $options
    * @return \Illuminate\Http\Response
    */
    private function multiTagsTransformer($hierarchy, $options)
    {
        $data = isset($options['L3'])
            ? $hierarchy->standardTags()
            ->when(!$options['stringMatched'] && !$options['relevantMatch'], function ($query) {
                $query->searchName(request()->keyword);
            })->whereHas('productTags', function ($query) use ($options) {
                $query->where('status', 'active')
                    ->select('*', DB::raw('count(*) as active_products'))
                    ->having('active_products', '>', 0);
                $query->when($options['relevantMatch'], function ($subQuery) {
                    $subQuery->MatchOrphanTags(request()->keyword);
                });
                $query->when($this->business, function ($subQuery) {
                    $subQuery->where('business_id', $this->business->id);
                });
            })->when($options['relevantMatch'], function ($query) use ($options) {
                if (count($options['levelFourIds']) > 0 && isset($options['existLevelFour']) && $options['existLevelFour'] == true) {
                    $query->whereIn('id', $options['levelFourIds']);
                }
            })->get()->reject(function ($record) use ($options) {
                $count = $record->productTags()->when(isset($options['moduleTag']) && in_array($options['moduleTag'], ['news', 'posts']), function ($query) {
                    $query->where('status', 'active');
                }, function ($subQuery) {
                    $subQuery->active();
                })
                    ->when($options['relevantMatch'], function ($query) {
                        $query->matchOrphanTags(request()->keyword);
                    })->whereHas('standardTags', function ($query) use ($record, $options) {
                        $query->select('standard_tags.*', DB::raw('count(*) as total'))
                            ->whereIn('id', [$options['L1'], $options['L2'], $options['L3'], $record->id])->having('total', '>=', 4);
                    })->count();
                return !$count > 0 ? \true : \false;
            })->map(function ($record) use ($options) {
                $data = [
                    'id' => $record->id,
                    'name' => $record->name,
                    'slug' => $record->slug,
                ];

                if (!$options['stringMatched']) {
                    $options['L4'] = $record->id;

                    if ($options['relevantMatch']) {
                        $products = Product::when($this->business, function ($query) {
                            $query->where('business_id', $this->business->id);
                        })->active()->matchAgainstWithPriority(request()->keyword)
                            ->hierarchyBasedProducts($options)->take(8)->get();
                        $this->isProductsExists = $this->isProductsExists || $products->count() > 0 ? true : false;
                    } else {
                        $products = Product::when($this->business, function ($query) {
                            $query->where('business_id', $this->business->id);
                        })->active()->hierarchyBasedProducts($options)->take(8)->get();
                        $this->isProductsExists = $this->isProductsExists || $products->count() > 0 ? true : false;
                    }
                }
                $data['products'] = !$options['stringMatched']
                    ? (new SearchProductTransformer)->transformCollection($products)
                    : [];

                $data['avg_relevancy'] = $this->calculateAvgSumOfPriority($data['products']);
                return $data;
            })->values()
            : [];
        return $data;
    }

    /**
     * get business according to keyword search (L2 and L3) tags w.r.t selected level one.
     *
     * @param $keyword, $levelOne
     * @return \Illuminate\Http\Response
     */
    private function getRelevantStores($keyword, $levelOne)
    {
        $tag = StandardTag::find($levelOne);
        $standardTag = StandardTag::searchName($keyword)->where('name', 'not like', $tag->name)->where(function ($query) use ($levelOne) {
            $query->whereHas('levelTwo', function ($query) use ($levelOne) {
                $query->where('L1', $levelOne);
            })->orWhereHas('levelThree', function ($query) use ($levelOne) {
                $query->where('L1', $levelOne);
            });
        })->first();
        $businesses = Business::active()
            ->whereHas('standardTags.levelOne', function ($query) use ($levelOne) {
                $query->where('L1', $levelOne);
            })->whereHas('products', function ($subQuery) use ($standardTag) {
                $subQuery->where('status', 'active')->whereHas('standardTags', function ($innerQuery) use ($standardTag) {
                    $innerQuery->where('id', $standardTag?->id);
                });
            })->with('standardTags')->take(6)->get();

        return (new SearchBusinessTransformer)->transformCollection($businesses);
    }

    public function storeSearch(StoreSearchRequest $request)
    {
        $this->business = Business::where('slug', $request->business_slug)->firstOrFail();
        $moduleTag = $this->business->standardTags()->where('type', 'module');
        if ($moduleTag->exists()) {
            $moduleTag = $moduleTag->first();
            $relevantKeyword = RakePlus::create(\request()->keyword)->keywords();
            if (is_numeric(request()->keyword) || preg_match('/^\d+(\.\d+)?$/', request()->keyword)) {
                request()->merge([
                    'keyword' => $request->keyword
                ]);
            } else {
                request()->merge([
                    'keyword' => collect($relevantKeyword)->implode(' ')
                ]);
            }
            if (auth('sanctum')->user()) {
                TopSearches::saveSearch(auth('sanctum')->user(), $moduleTag);
            }
            $field = 'L1';
            request()->merge([
                'L2' => request()->input('L2') ? StandardTag::where('id', request()->input('L2'))->orWhere('slug', request()->input('L2'))->first()->id : null,
                'L3' => request()->input('L3') ? StandardTag::where('id', request()->input('L3'))->orWhere('slug', request()->input('L3'))->first()->id : null
            ]);
            $exact_match = StandardTag::where('name', request()->input('keyword'))
                ->whereHas('tagHierarchies', function ($query) {
                    $query->where('level_type', 4);
                })->exists();

            $partial_match = $exact_match ? false : true;

            $tagHierarchies = request()->input('keyword') ? TagHierarchy::where('L1', $moduleTag->id)->where(function ($query) {
                $query->when(!request()->filled('L2'), function ($subQuery) {
                    $subQuery->whereHas('levelTwo', function ($innerQuery) {
                        $innerQuery->whereHas('productTags', function ($subQuery) {
                            $subQuery->where('business_id', $this->business->id);
                        });
                        $innerQuery->searchName(request()->keyword)->filterProducts();
                    });
                });

                // if only level 2 is selected
                $query->when(!request()->filled('L3'), function ($subQuery) {
                    $subQuery->orWhereHas('levelThree', function ($innerQuery) {
                        $innerQuery->whereHas('productTags', function ($subQuery) {
                            $subQuery->where('business_id', $this->business->id);
                        });
                        $innerQuery->searchName(request()->keyword)->filterProducts();
                    })->when(request()->filled('L2'), function ($innerQuery) {
                        $innerQuery->where('L2', request()->L2);
                    });
                });
            })->orWhereHas('standardTags', function ($query) {
                $query->whereHas('productTags', function ($subQuery) {
                    $subQuery->where('business_id', $this->business->id);
                });
                $query->searchName(request()->keyword)->filterProducts();
            })->when(\request()->filled('L3'), function ($query) {
                $query->where('L2', \request()->L2)
                    ->where('L3', \request()->L3);
            })->get()->unique($field) : [];
            $tag_hierarchies = $this->getHierarchy($tagHierarchies, $field, $partial_match);
            $match_type = count($tag_hierarchies) == 0
                ? 'relevant_match' : ($exact_match ? 'exact_match' : 'partial_match');

            if ($match_type == 'relevant_match') {
                if (count($relevantKeyword) > 0) {
                    $levelFours = StandardTag::whereHas('tagHierarchies', function ($query) {
                        $query->where("level_type", 4);
                    })->where(function ($query) use ($relevantKeyword) {
                        foreach ($relevantKeyword as $keyword) {
                            $query->orWhere('name', 'like', $keyword . '%');
                        }
                    })->get(['id', 'name']);
                    $levelFourIds = Arr::pluck($levelFours, 'id');
                    $levelFourNames = Arr::pluck($levelFours, 'name');
                    $namesArray = [];
                    foreach ($levelFourNames as $key => $levelFourName) {
                        $names = [Str::singular($levelFourName), Str::plural($levelFourName)];
                        $namesArray = \array_merge($namesArray, $names);
                    }
                    $relevantKeyword = array_values(array_unique(\array_merge(
                        RakePlus::create(\request()->keyword)->keywords(),
                        $namesArray
                    )));

                    // updating the search keywords
                    $standardTags = StandardTag::whereHas('tags_', function ($query) use ($relevantKeyword) {
                        $query->whereIn('slug', $relevantKeyword);
                    })
                        ->where('priority', '!=', 4)
                        ->pluck('name')->toArray();
                    $mergedKyewords = \array_unique(\array_map('strtolower', array_merge($relevantKeyword, $standardTags)));

                    // convert to string
                    $searchString = collect($mergedKyewords)->implode(' ');

                    // convert string to array then check remove the duplicate words from it and again convert it to string
                    request()->merge([
                        'keyword' => \collect(array_unique(explode(" ", $searchString)))->implode(' ')
                    ]);

                    $tagHierarchies = TagHierarchy::where('L1', $moduleTag->id)
                        ->whereHas('standardTags.productTags', function ($query) {
                            $query->where('business_id', $this->business->id);
                            $query->active()->matchOrphanTags(request()->keyword);
                        })->get()->unique($field);

                    $existLeveFour = TagHierarchy::where('L1', $moduleTag->id)->whereHas('standardTags.productTags', function ($query) {
                        $query->active()->matchOrphanTags(request()->keyword);
                    })->whereHas('standardTags', function ($query) use ($levelFourIds) {
                        $query->whereIn('id', $levelFourIds);
                    })->exists();
                    $tag_hierarchies = $this->getHierarchy($tagHierarchies, $field, false, null, ['relevantMatch' => \true, 'levelFourIds' => $levelFourIds, 'existLevelFour' => $existLeveFour]);
                }
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => [
                    'tag_hierarchies' => $tag_hierarchies,
                    'match_type' => $match_type,
                    'isPrdouctsExists' => $this->isProductsExists,
                    'searched_keyword' => $relevantKeyword
                ],
            ], JsonResponse::HTTP_OK);
        }
    }

    /**
     * get level 4 average relevancy based on search keyword.
     *
     * @param $level
     * @return \Illuminate\Http\Response
     */
    private function calculateAvgSumOfPriority($products)
    {
        if (count($products) > 0) {
            $relevancySum = array_sum(array_column($products->toArray(), 'relevancy'));
            return $relevancySum / 8;
        }
    }

    public function getSearchHistory(Request $request, $module)
    {
        try {
            $module = StandardTag::where('id', $module)->orWhere('slug', $module)->first();
            $user = $request->user();
            $searchHistory = $user->searchHistory()->where('module_id', $module->id)->latest()->get();
            $searchHistory = (new SearchHistoryTransformer)->transformCollection($searchHistory);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $searchHistory,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function removeSearchHistory(Request $request, $id)
    {
        try {
            $user = $request->user();
            $search = $user->searchHistory()->where('id', $id)->firstOrFail()->delete();;
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Search removed successfully'
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
}
