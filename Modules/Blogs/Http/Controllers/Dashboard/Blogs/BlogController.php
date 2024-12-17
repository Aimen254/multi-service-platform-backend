<?php

namespace Modules\Blogs\Http\Controllers\Dashboard\Blogs;

use App\Models\Product;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Models\HeadlineSetting;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\News\Entities\Comment;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductPriorityManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Support\Renderable;
use Modules\Blogs\Http\Requests\BlogRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogController extends Controller
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
     * @param mixed $moduleId
     * @return Renderable
     */
    public function index($moduleId = null)
{
    $requestForm = request()->form ?? [];
    $orderBy = $requestForm['orderBy'] ?? 'desc';
    $limit = config('settings.pagination_limit');
    $userId = auth()->id();
    $isAdmin = auth()?->user()?->hasRole('admin');

    $query = Product::query()
        ->with(['mainImage', 'comments', 'standardTags']);

    // Apply module-based product filter
    $query->whereHas('standardTags', function ($subQuery) use ($moduleId) {
        $subQuery->where('id', $moduleId);
    });

    // Apply user-specific filters
    if ($isAdmin) {
        $query->whereNotNull('user_id');
    } else {
        $query->where('user_id', $userId);
    }

    // Apply additional filters
    if (request()->keyword) {
        $keyword = request()->keyword;
        $query->where('name', 'like', '%' . $keyword . '%');
    }

    if (isset($requestForm['status'])) {
        $query->where('status', $requestForm['status']);
    }

    if (isset($requestForm['tag'])) {
        $tagId = $requestForm['tag'];
        $query->whereHas('standardTags', function ($subQuery) use ($tagId) {
            $subQuery->where('id', $tagId);
        });
    }

    // Apply ordering and pagination
    $blogs = $query->orderBy('id', $orderBy)
        ->paginate($limit);

    // Prepare data for the view
    return inertia('Blogs::Blogs/Index', [
        'blogsList' => $blogs,
        'searchedKeyword' => request()->keyword,
        'orderBy' => $orderBy,
        'status' => $requestForm['status'] ?? null,
        'tag' => $requestForm['tag'] ?? null,
    ]);
}


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($moduleId)
    {
        $mediaSizes = \config()->get('blogs.media.blog');
        $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
            $query->where('L1', $moduleId);
        })->select(['id', 'name as text', 'slug'])->get();

        return inertia('Blogs::Blogs/Create', ['mediaSizes' => $mediaSizes, 'levelTwoTags' => $levelTwoTags]);
    }

    /**
     * Store a newly created resource in storage.
     * @param BlogRequest  $request
     * @param int $moduleId
     * @return Renderable
     */
    public function store(BlogRequest $request, $moduleId)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('blogs');
            $status = 'active';
            // if (!auth()?->user()?->hasRole('admin') && !auth()?->user()?->hasRole('newspaper')) {
            //     $subscriptionPermission = $this->checkGrantedProducts($moduleId, auth()->id());
            //     !$subscriptionPermission ? $status = 'inactive' : $status = 'active';
            // }
            Product::create(array_merge($request->validated(), [
                'user_id' => auth()->id(),
                'status' => $status,
            ]));
            \flash('Blog created successfully.', 'success');
            DB::commit();
            return \redirect()
                ->route('blogs.dashboard.blogs.index', $moduleId);
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


            return inertia('Blogs::Blogs/Show', [
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
            $blog = Product::where('uuid', $uuid)->firstOrfail();
            $productLevelTwoTag = $blog->standardTags()->whereHas('levelTwo')->first();
            $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
                $query->where('L1', $moduleId);
            })->select(['id', 'name as text', 'slug'])->get();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Blog', 'danger');
            return back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return inertia('Blogs::Blogs/Edit', [
            'blog' => $blog,
            'levelTwoTags' => $levelTwoTags,
            'productLevelTwoTag' => $productLevelTwoTag
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param BlogRequest $request
     * @param int $moduleId
     * @param uuid $uuid
     * @return Renderable
     */
    public function update(BlogRequest $request, $moduleId, $uuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('blogs');
            $blog = Product::whereUuid($uuid)->firstOrFail();
            $blog->update($request->validated());
            \flash('Blog updated successfully.', 'success');
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
     *
     * @param int $moduleId
     * @param uuid $uuid
     * @return Renderable
     */
    public function destroy($moduleId, $uuid,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $blog = Product::where('uuid', $uuid)->firstOrfail();
            $blog->delete();
            flash('Blog deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('blogs.dashboard.blogs.index', [$moduleId, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Blog', 'danger');
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
                if (request()->blog) {
                    $product = Product::findOrFail(request()->blog);
                    $productLevelThreeTag = $product->standardTags()->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                        $query->where('L1', $moduleId)->where('L2', $tagId);
                    })->select(['id', 'name as text', 'slug'])->first();
                } else {
                    $nextLevelTags = StandardTag::where('name', 'like', '%' . request()->keyword . '%')->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                        $query->where('L1', $moduleId)->where('L2', $tagId);
                    })->select(['id', 'name as text', 'slug'])->paginate(50);
                }
            } else {
                if (request()->blog) {
                    $product = Product::findOrFail(request()->blog);
                    $productLevelFourTag = StandardTag::whereHas('productTags', function ($query) use ($moduleId, $tagId) {
                        $query->where('product_id', request()->blog)->whereHas('standardTags', function ($subQuery) use ($moduleId, $tagId) {
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
            // if ($previousProductStatus == 'inactive') {
            //     //checking subscription permissions starts
            //     if (!auth()?->user()?->hasRole('admin') && !auth()?->user()?->hasRole('newspaper')) {
            //         $subscriptionPermission = $this->checkGrantedProducts($moduleId, auth()->id());
            //         if (!$subscriptionPermission) {
            //             flash('You can not change status for this blog due to subscription limitations.', 'danger');
            //             return \redirect()->back();
            //         }
            //     }
            //     //checking subscription permissions ends
            // }
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

    public function searchBlogTags($moduleId)
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

    // set a blog as headline
    public function makeHeadline(Request $request, $moduleId)
    {
        $request->validate([
            'type' => ['required', 'in:Primary,Secondary']
        ], [
            'type.required' => 'The headline type field is required.',
            'type.in' => 'The headline type is invalid.'
        ]);

        try {
            if ($request->type == 'Primary') {
                HeadlineSetting::updateOrCreate([
                    'module_id' => $moduleId,
                    'type' => $request->type
                ], [
                    'product_id' => $request->product_id,
                ]);
            } else {
                $secondaryHeadLineCount = HeadlineSetting::where('type', $request->type)->where('module_id', $moduleId)->get()->count();
                if ($secondaryHeadLineCount < 4) {
                    HeadlineSetting::create([
                        'module_id' => $moduleId,
                        'type' => $request->type,
                        'product_id' => $request->product_id,
                    ]);
                } else {
                    $oldestHeadLine = HeadlineSetting::where('type', $request->type)->where('module_id', $moduleId)->oldest()->first();
                    $oldestHeadLine->update([
                        'product_id' => $request->product_id,
                        'created_at' => now(),
                    ]);
                }
            }


            flash("Blog added to $request->type headline", "success");
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
        }

        return \back();
    }

    public function deleteHeadline($moduleId, $id)
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
