<?php

namespace Modules\Posts\Http\Controllers\Dashboard\Posts;

use App\Models\Product;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Posts\Entities\Comment;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Contracts\Support\Renderable;
use Modules\Posts\Http\Requests\PostRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;
use PhpParser\PrettyPrinter\Standard;
use App\Traits\ProductPriorityManager;

class PostController extends Controller
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

        $posts = Product::when(auth()?->user()?->hasRole('admin'), function ($query) use ($moduleId) {
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
        })->withCount('comments')
            ->orderBy('id', $orderBy)
            ->paginate($limit);


        return inertia('Posts::Posts/Index', [
            'postsList' => $posts,
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
        $mediaSizes = \config()->get('posts.media.posts');
        $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
            $query->where('L1', $moduleId);
        })->select(['id', 'name as text', 'slug'])->get();

        return inertia('Posts::Posts/Create', ['mediaSizes' => $mediaSizes, 'levelTwoTags' => $levelTwoTags]);
    }

    /**
     * Store a newly created resource in storage.
     * @param PostRequest $request
     * @return Renderable
     */
    public function store(PostRequest $request, $moduleId)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('posts');

            $status = 'active';
            if (!auth()?->user()?->hasRole('admin')) {
                $subscriptionPermission = $this->checkGrantedProducts($moduleId, auth()->id());
                !$subscriptionPermission ? $status = 'inactive' : $status = 'active';
            }

            Product::create(array_merge($request->validated(), [
                'user_id' => \auth()?->id(),
                'status' => $status
            ]));
            \flash('Post created successfully.', 'success');
            DB::commit();
            return \redirect()
                ->route('posts.dashboard.posts.index', $moduleId);
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
            $post = Product::with(['secondaryImages', 'mainImage', 'user'])->where('uuid', $uuid)->firstOrFail();

            $comments = Comment::with('user')
                ->where(function ($query) {
                    $keyword = request()->keyword;
                    $query->whereHas('user', function ($query) use ($keyword) {
                        $query->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere('comment', 'like', '%' . $keyword . '%')
                        ->orWhere('created_at', 'like', '%' . $keyword . '%');
                })
                ->whereHas('product', function ($query) use ($uuid) {
                    $query->where('uuid', $uuid);
                })
                ->latest()
                ->paginate($limit);

            return inertia('Posts::Posts/Show', [
                'post' => $post,
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
     * @param string $uuid
     * @return Renderable
     */
    public function edit($moduleId, $uuid)
    {
        try {
            $post = Product::where('uuid', $uuid)->firstOrfail();
            $productLevelTwoTag = $post->standardTags()->whereHas('levelTwo')->first();
            $levelTwoTags = StandardTag::whereHas('levelTwo', function ($query) use ($moduleId) {
                $query->where('L1', $moduleId);
            })->select(['id', 'name as text', 'slug'])->get();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Post', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
        return inertia('Posts::Posts/Edit', [
            'post' => $post,
            'levelTwoTags' => $levelTwoTags,
            'productLevelTwoTag' => $productLevelTwoTag,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param PostRequest $request
     * @param int $id
     * @return Renderable
     */
    public function update(PostRequest $request, $moduleId, $uuid)
    {
        try {
            DB::beginTransaction();
            ModuleSessionManager::setModule('posts');

            $post = Product::whereUuid($uuid)->firstOrFail();
            $post->update($request->validated());
            \flash('Post updated successfully.', 'success');
            DB::commit();
            return \back();
        } catch (ModelNotFoundException $e) {
            \flash('Unable to find this post.', 'danger');
            DB::rollBack();
            return \back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
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
            $post = Product::where('uuid', $uuid)->firstOrfail();
            $post->delete();
            flash('Post deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('posts.dashboard.posts.index', [$moduleId, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this post', 'danger');
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
                if (request()->post) {
                    $product = Product::findOrFail(request()->post);
                    $productLevelThreeTag = $product->standardTags()->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                        $query->where('L1', $moduleId)->where('L2', $tagId);
                    })->select(['id', 'name as text', 'slug'])->first();
                } else {
                    $nextLevelTags = StandardTag::where('name', 'like', '%' . request()->keyword . '%')->whereHas('levelThree', function ($query) use ($tagId, $moduleId) {
                        $query->where('L1', $moduleId)->where('L2', $tagId);
                    })->select(['id', 'name as text', 'slug'])->paginate(50);
                }
            } else {
                if (request()->post) {
                    $product = Product::findOrFail(request()->post);
                    $productLevelFourTag = StandardTag::whereHas('productTags', function ($query) use ($moduleId, $tagId) {
                        $query->where('product_id', request()->post)->whereHas('standardTags', function ($subQuery) use ($moduleId, $tagId) {
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
     * @param  int $uuid
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($moduleId, $uuid)
    {
        try {
            ModuleSessionManager::setModule('posts');
            $product = Product::where('uuid', $uuid)->firstOrfail();
            $previousProductStatus = $product->status;
            if ($previousProductStatus == 'inactive' && !$product->mainImage()->exists()) {
                flash('Primary image not found. Cannot proceed with status activation.', 'danger');
                return redirect()->back();
            }
            if ($previousProductStatus == 'inactive') {
                //checking subscription permissions starts
                if (!auth()?->user()?->hasRole('admin')) {
                    $subscriptionPermission = $this->checkGrantedProducts($moduleId, auth()->id());
                    if (!$subscriptionPermission) {
                        flash('You can not change the status for this post due to subscription limitations.', 'danger');
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
                flash('Post status changed succesfully', 'success');
            }
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this post', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function searchPostTags($moduleId)
    {
        try {
            // $module = StandardTag
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
