<?php

namespace Modules\Classifieds\Http\Controllers\Dashboard\Classifieds;

use App\Models\Product;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\News\Entities\Comment;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Classifieds\Http\Requests\ClassifiedRequest;

class ClassifiedController extends Controller
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
     *
     * @param int $moduleId
     * @return Renderable
     */
    public function index($moduleId = null)
    {
        $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
        $limit = \config()->get('settings.pagination_limit');

        $barMinValue = request()->form && isset(request()->form['barMinValue']) ? request()->form['barMinValue'] : null;
        $barMaxValue = request()->form && isset(request()->form['barMaxValue']) ? request()->form['barMaxValue'] : null;

        $maxPrice = Product::when(auth()?->user()?->hasRole('admin'), function ($query) use ($moduleId) {
            $query->whereHas('standardTags', function ($subQuery) use ($moduleId) {
                $subQuery->where('id', $moduleId);
            });
        }, function ($query) use ($moduleId) {
            $query->where('user_id', auth()->id())->whereHas('standardTags', function ($subQuery) use ($moduleId) {
                $subQuery->where('id', $moduleId);
            });
        })->get()->max('price');

        $minPrice = Product::when(auth()->user()?->hasRole('admin'), function ($query) use ($moduleId) {
            $query->whereHas('standardTags', function ($subQuery) use ($moduleId) {
                $subQuery->where('id', $moduleId);
            });
        }, function ($query) use ($moduleId) {
            $query->where('user_id', auth()->id())->whereHas('standardTags', function ($subQuery) use ($moduleId) {
                $subQuery->where('id', $moduleId);
            });
        })->get()->min('price');

        $classifieds = Product::when(auth()?->user()?->hasRole('admin'), function ($query) use ($moduleId, $barMinValue, $barMaxValue) {
            $query->moduleBasedProducts($moduleId)->where('user_id', '!=', null)->with(['mainImage'])->where(function ($query) use ($barMinValue, $barMaxValue) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
                if (request()->form && isset(request()->form['status'])) {
                    $query->where('status', request()->form['status']);
                }
                if (request()->form && isset(request()->form['barMinValue']) && isset(request()->form['barMaxValue'])) {
                    $query->whereBetween('price', [$barMinValue, $barMaxValue]);
                }
                if (request()->form && isset(request()->form['tag'])) {
                    $query->whereHas('standardTags', function ($subQuery) {
                        $subQuery->where('id', request()->form['tag']);
                    });
                }
            });
        }, function ($query) use ($moduleId, $barMinValue, $barMaxValue) {
            $query->where('user_id', auth()->id())->moduleBasedProducts($moduleId)->with(['mainImage'])->where(function ($query) use ($barMinValue, $barMaxValue) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
                if (request()->form && isset(request()->form['status'])) {
                    $query->where('status', request()->form['status']);
                }
                if (request()->form && isset(request()->form['barMinValue']) && isset(request()->form['barMaxValue'])) {
                    $query->whereBetween('price', [$barMinValue, $barMaxValue]);
                }
                if (request()->form && isset(request()->form['tag'])) {
                    $query->whereHas('standardTags', function ($subQuery) {
                        $subQuery->where('id', request()->form['tag']);
                    });
                }
            });
        })
            ->withCount('comments')
            ->orderBy('id', $orderBy)
            ->paginate($limit);

        return inertia('Classifieds::Classifieds/Index', [
            'classifiedsList' => $classifieds,
            'searchedKeyword' => request()->keyword,
            'maxPrice' => $maxPrice ? $maxPrice : 1000,
            'minPrice' => $minPrice ? $minPrice : 0,
            'barValueMin' => $barMinValue,
            'barValueMax' => $barMaxValue,
            'orderBy' => request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc',
            'status' => request()->form && isset(request()->form['status']) ? request()->form['status'] : null,
            'tag' => request()->form && isset(request()->form['tag']) ? request()->form['tag'] : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($moduleId)
    {
        $mediaSizes = \config()->get('classifieds.media.classified');
        $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
            $query->where('L1', $moduleId);
        })->select(['id', 'name as text', 'slug'])->get();

        return inertia('Classifieds::Classifieds/Create', ['mediaSizes' => $mediaSizes, 'levelTwoTags' => $levelTwoTags]);
    }

    /**
     * Store a newly created resource in storage.
     * @param ClassifiedRequest $request
     * @param int $moduleId
     * @return Renderable
     */
    public function store(ClassifiedRequest $request, $moduleId)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule(Product::MODULE_MARKETPLACE);
            $status = 'active';
            if (!auth()?->user()?->hasRole('admin') && !auth()?->user()?->hasRole('newspaper')) {
                $subscriptionPermission = $this->checkGrantedProducts($moduleId, auth()->id());
                !$subscriptionPermission ? $status = 'inactive' : $status = 'active';
            }
            Product::create(array_merge($request->validated(), [
                'user_id' => auth()->id(),
                'status' => $status,
            ]));
            \flash('Item created successfully.', 'success');
            DB::commit();
            return \redirect()
                ->route('classifieds.dashboard.classifieds.index', $moduleId);
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
            $product = Product::with(['secondaryImages', 'mainImage', 'user'])->where('uuid', $uuid)->firstOrFail();

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


            return inertia('Classifieds::Classifieds/Show', [
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
     *
     * @param int $moduleId
     * @param string $uuid
     * @return Renderable
     */
    public function edit($moduleId, $uuid)
    {
        try {
            $classified = Product::where('uuid', $uuid)->firstOrfail();
            $productLevelTwoTag = $classified->standardTags()->whereHas('levelTwo')->first();
            $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
                $query->where('L1', $moduleId);
            })->select(['id', 'name as text', 'slug'])->get();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Item', 'danger');
            return back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return inertia('Classifieds::Classifieds/Edit', [
            'classified' => $classified,
            'levelTwoTags' => $levelTwoTags,
            'productLevelTwoTag' => $productLevelTwoTag
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ClassifiedRequest $request
     * @param int $moduleId
     * @param string $uuid
     * @return Renderable
     */
    public function update(ClassifiedRequest $request, $moduleId, $uuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule(Product::MODULE_MARKETPLACE);
            $classified = Product::whereUuid($uuid)->firstOrFail();
            $classified->update($request->validated());
            \flash('Item updated successfully.', 'success');
            DB::commit();
            return \back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Item', 'danger');
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
     *
     * @param int $moduleId
     * @param string $uuid
     * @return Renderable
     */
    public function destroy($moduleId, $uuid)
    {
        try {
            $classified = Product::where('uuid', $uuid)->firstOrfail();
            $classified->delete();
            flash('Item deleted succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Item', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    /**
     * get of specified resource from storage.
     *
     * @param Request $request
     * @param int $moduleId
     * @param int $tagId
     * @param int $level
     * @return \Illuminate\Http\Response
     */
    public function getTags(Request $request, $moduleId, $tagId, $level)
    {
        try {
            $nextLevelTags = [];
            $productLevelThreeTag = null;
            $productLevelFourTag = null;
            if ($level == 2) {
                if (request()->classified) {
                    $product = Product::findOrFail(request()->classified);
                    $productLevelThreeTag = $product->standardTags()->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                        $query->where('L1', $moduleId)->where('L2', $tagId);
                    })->select(['id', 'name as text', 'slug'])->first();
                } else {
                    $nextLevelTags = StandardTag::where('name', 'like', '%' . request()->keyword . '%')->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                        $query->where('L1', $moduleId)->where('L2', $tagId);
                    })->select(['id', 'name as text', 'slug'])->paginate(50);
                }
            } else {
                if (request()->classified) {
                    $product = Product::findOrFail(request()->classified);
                    $productLevelFourTag = StandardTag::whereHas('productTags', function ($query) use ($moduleId, $tagId) {
                        $query->where('product_id', request()->classified)->whereHas('standardTags', function ($subQuery) use ($moduleId, $tagId) {
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
     * Change status of specified resource from storage.
     *
     * @param  int $moduleId
     * @param string $uuid
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($moduleId, $uuid)
    {
        try {
            ModuleSessionManager::setModule(Product::MODULE_MARKETPLACE);
            $product = Product::where('uuid', $uuid)->firstOrfail();
            $previousProductStatus = $product->status;
            if ($previousProductStatus == 'inactive') {
                //checking subscription permissions starts
                if (!auth()?->user()?->hasRole('admin') && !auth()?->user()?->hasRole('newspaper')) {
                    $subscriptionPermission = $this->checkGrantedProducts($moduleId, auth()->id());
                    if (!$subscriptionPermission) {
                        flash('You can not change status for this item due to subscription limitations.', 'danger');
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
                flash('Item status changed succesfully', 'success');
            }
            return back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Item', 'danger');
            return back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
    }

    public function searchClassifiedTags($moduleId)
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
