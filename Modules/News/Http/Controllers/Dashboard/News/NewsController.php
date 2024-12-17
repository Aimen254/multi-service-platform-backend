<?php

namespace Modules\News\Http\Controllers\Dashboard\News;

use App\Models\HeadlineSetting;
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
use Modules\News\Http\Requests\NewsRequest;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;
use App\Traits\ProductPriorityManager;

class NewsController extends Controller
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
    public function index($moduleId)
    {
        $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
        $limit = \config()->get('settings.pagination_limit');
        $news = Product::with(['standardTags' => function ($query) {
            $query->whereHas('levelTwo');
        }, 'headline' => function ($query) {
            $query->whereDate('created_at', today());
        }])->when(auth()?->user()?->hasRole('admin'), function ($query) use ($moduleId) {
            $query->moduleBasedProducts($moduleId)->where('user_id', '!=', null)->with(['mainImage'])->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
                if (request()->form && isset(request()->form['status'])) {
                    $query->where('status', request()->form['status']);
                }
                if (request()->form && isset(request()->form['tag'])) {
                    $query->whereHas('standardTags', function ($subQuery) {
                        $subQuery->where('id', request()->form['tag']);
                    });
                }
                if (isset(request()->form['today_created']) && request()->form['today_created'] == '1') {
                    $query->whereDate('created_at', today());
                }
            });
        }, function ($query) use ($moduleId) {
            $query->where('user_id', auth()->id())->moduleBasedProducts($moduleId)->with(['mainImage'])->where(function ($query) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
                if (request()->form && isset(request()->form['status'])) {
                    $query->where('status', request()->form['status']);
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

        return inertia('News::News/Index', [
            'newsList' => $news,
            'searchedKeyword' => request()->keyword,
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
        $mediaSizes = \config()->get('news.media.news');
        $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
            $query->where('L1', $moduleId);
        })->select(['id', 'name as text', 'slug'])->get();

        return inertia('News::News/Create', ['mediaSizes' => $mediaSizes, 'levelTwoTags' => $levelTwoTags]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(NewsRequest $request, $moduleId)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('news');
            $status = 'active';
            if (!auth()?->user()?->hasRole('admin') && !auth()?->user()?->hasRole('newspaper')) {
                $subscriptionPermission = $this->checkGrantedProducts($moduleId, auth()->id());
                !$subscriptionPermission ? $status = 'inactive' : $status = 'active';
            }
            Product::create(array_merge($request->validated(), [
                'user_id' => auth()->id(),
                'status' => $status,
            ]));
            \flash('News created successfully.', 'success');
            DB::commit();
            return \redirect()
                ->route('news.dashboard.news.index', $moduleId);
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

            return inertia('News::News/Show', [
                'product' => $product,
                'commentList' => $comments,
                'searchedKeyword' => request()->keyword,
            ]);
        } catch (ModelNotFoundException $e) {
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
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
            $news = Product::where('uuid', $uuid)->firstOrfail();
            $productLevelTwoTag = $news->standardTags()->whereHas('levelTwo')->first();
            $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
                $query->where('L1', $moduleId);
            })->select(['id', 'name as text', 'slug'])->get();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this News', 'danger');
            return back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return inertia('News::News/Edit', [
            'news' => $news,
            'levelTwoTags' => $levelTwoTags,
            'productLevelTwoTag' => $productLevelTwoTag
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param NewsRequest $request
     * @param int $id
     * @return Renderable
     */
    public function update(NewsRequest $request, $moduleId, $uuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('news');
            $news = Product::whereUuid($uuid)->with('standardTags')->firstOrFail();

            // removing from headline in case of change level two tag
            if ($news->standardTags[1]->id != $request->level_two_tag) {
                HeadlineSetting::where('product_id', $news->id)->delete();
            }

            $news->update($request->validated());
            \flash('News updated successfully.', 'success');
            DB::commit();
            return \back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this News', 'danger');
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
            $news = Product::where('uuid', $uuid)->firstOrfail();
            $news->delete();
            flash('News deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('news.dashboard.news.index', [$moduleId, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this News', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    /**
     * get of specified resource from storage.
     *
     * @param  int $module
     * @return \Illuminate\Http\Response
     */
    public function getTags(Request $request, $moduleId, $tagId, $level)
    {
        try {
            $nextLevelTags = [];
            $productLevelThreeTag = null;
            $productLevelFourTag = null;
            if ($level == 2) {
                if (request()->news) {
                    $product = Product::findOrFail(request()->news);
                    $productLevelThreeTag = $product->standardTags()->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                        $query->where('L1', $moduleId)->where('L2', $tagId);
                    })->select(['id', 'name as text', 'slug'])->first();
                } else {
                    $nextLevelTags = StandardTag::where('name', 'like', '%' . request()->keyword . '%')->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                        $query->where('L1', $moduleId)->where('L2', $tagId);
                    })->select(['id', 'name as text', 'slug'])->paginate(50);
                }
            } else {
                if (request()->news) {
                    $product = Product::findOrFail(request()->news);
                    $productLevelFourTag = StandardTag::whereHas('productTags', function ($query) use ($moduleId, $tagId) {
                        $query->where('product_id', request()->news)->whereHas('standardTags', function ($subQuery) use ($moduleId, $tagId) {
                            $subQuery->whereIn('id', [$moduleId, $tagId, request()->levelTwoTag])->select('*', DB::raw('count(*) as total'))
                                ->having('total', '>=', 3);
                        });
                    })->whereHas('tagHierarchies', function ($query) use ($moduleId, $tagId) {
                        $query->where('L1', $moduleId);
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
            ModuleSessionManager::setModule('news');
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
                        flash('You can not change status for this news due to subscription limitations.', 'danger');
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
                flash('News status changed succesfully', 'success');
            }
            return back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this news', 'danger');
            return back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
    }

    public function searchNewsTags($moduleId)
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

    // set a news as headline
    public function makeHeadline(Request $request, $moduleId)
    {
        $request->validate([
            'type' => ['required', 'in:Primary,Secondary']
        ], [
            'type.required' => 'The headline type field is required.',
            'type.in' => 'The headline type is invalid.'
        ]);

        try {
            $levelTwoTag = $request->type === 'Secondary' ? $request->level_two_tag_id : null;

            $validation = $this->validateHeadline($request, $moduleId, $levelTwoTag);

            if (!$validation) {
                HeadlineSetting::create([
                    'module_id' => $moduleId,
                    'product_id' => $request->product_id,
                    'level_two_tag_id' => $levelTwoTag,
                    'type' => $request->type
                ]);
            }

            flash("News added to $request->type headline", "success");
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
        }

        return \back();
    }

    /**
     * validate the headline with primary and secondary type and
     * check if it is exist with same tag
     */
    private function validateHeadline($request, $moduleId, $levelTwoTag)
    {
        $secondaryProduct = $secondaryProductLevelTwoTag = $primaryProduct = null;
        if ($levelTwoTag) {
            $secondaryProduct = HeadlineSetting::where('product_id', $request->product_id)
                ->where('module_id', $moduleId)
                ->where('level_two_tag_id', $levelTwoTag)->whereDate('created_at', today())->first();
        }

        if ($levelTwoTag) {
            $secondaryProductLevelTwoTag = HeadlineSetting::with('levelTwoTag')->where('module_id', $moduleId)
                ->where('level_two_tag_id', $levelTwoTag)->whereDate('created_at', today())->first();
        }

        if (!$levelTwoTag) {
            $primaryProduct = HeadlineSetting::where('module_id', $moduleId)
                ->where('level_two_tag_id', null)->whereDate('created_at', today())->first();
        }

        if ($secondaryProduct) {
            throw new \Exception("This News already added to {$secondaryProduct?->type} headline");
        }

        if ($secondaryProductLevelTwoTag) {
            throw new \Exception("The News with {$secondaryProductLevelTwoTag?->levelTwoTag?->name} tag already added to {$secondaryProductLevelTwoTag?->type} headline");
        }

        if ($primaryProduct) {
            throw new \Exception("The {$primaryProduct?->type} News already added to headline");
        }

        return false;
    }

    public function removeHeadline($moduleId, $id)
    {
        try {
            $headline = HeadlineSetting::where('id',$id)->where('module_id', $moduleId)->firstOrfail();
            $headline->delete();
            flash('Headline deleted successfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Headline', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
