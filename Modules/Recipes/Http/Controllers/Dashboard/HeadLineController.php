<?php

namespace Modules\Recipes\Http\Controllers\Dashboard;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Models\HeadlineSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class HeadLineController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param null $moduleId
     * @return \Inertia\Response
     */
    public function index($moduleId = null)
    {
        $headLines = Product::whereHas('headline')
            ->with([
                'standardTags' => function ($query) use ($moduleId) {
                    $query->whereRelation('levelTwo', 'L1', $moduleId);
                },
                'mainImage',
                'headline'
            ])
            ->moduleBasedProducts($moduleId)
            ->whereNotNull('user_id')
            ->orderByRaw("CASE 
            WHEN (SELECT type FROM headline_settings WHERE product_id = products.id) = 'primary' THEN 0
            WHEN (SELECT type FROM headline_settings WHERE product_id = products.id) = 'secondary' THEN 1
            ELSE 2 END")
            ->paginate(5);
        return Inertia::render('Recipes::HeadLines/Index', [
            'headLines' => $headLines
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @param $moduleId
     * @return RedirectResponse|\Inertia\Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function create($moduleId)
    {
        $type = request()->has('type') ? request()->input('type') : (request()->has('form') && isset(request()->form['type']) ? request()->form['type'] : null);
        $tag = request()->has('tag') ? request()->input('tag') : (request()->has('form') && isset(request()->form['tag']) ? request()->form['tag'] : null);
        $recipes = null;
        $levelTwoTags = StandardTag::with('productTags')->whereHas('levelTwo', function ($query) use ($moduleId) {
            $query->where('L1', $moduleId);
        })->where('type', '!=', 'module')
            ->select(['id', 'name as text', 'slug'])
            ->whereHas('productTags', function ($query) use ($moduleId) {
                $query->whereRelation('standardTags', 'id', $moduleId)
                    ->where('status', 'active');
            })
            ->get();

        // Filter the collection based on the count of productTags
        $tags = $levelTwoTags->filter(function ($tag) {
            if ($tag->productTags()->whereRelation('headline', 'type', 'Secondary')->exists()) {
                return  $tag->productTags->count() > 1;
            } else {
                return $tag;
            }
        })->values();

        if ($type == 'Secondary' && !$tag) {
            flash('Please Select Level Two Tag', 'danger');
            return \redirect()->back();
        }

        if ($type) {
            $limit = \config()->get('settings.pagination_limit');
            $recipes = Product::moduleBasedProducts($moduleId)->where('user_id', '!=', null)->with(['mainImage', 'headline'])->where(function ($query) use ($tag) {
                if (request()->keyword) {
                    $keyword = request()->keyword;
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
                if ($tag) {
                    $query->whereHas('standardTags', function ($subQuery) use ($tag) {
                        $subQuery->where('id', $tag);
                    });
                }
            })->whereDoesntHave('headline', function ($subQuery) use ($type) {
                $subQuery->where('type', $type);
            })->where('status', 'active')->orderBy('id', 'desc')->paginate($limit);
        }
        return Inertia::render('Recipes::HeadLines/Create', [
            'levelTwoTags' => $tags,
            'recipesList' => $recipes?->count() > 0 ? $recipes : null,
            'type' => $type,
            'tag' => $tag,
            'searchedKeyword' => request()->keyword,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param $moduleId
     * @return JsonResponse
     */
    public function store(Request $request, $moduleId)
    {
        try {
            if ($request->type == 'Primary') {
                HeadlineSetting::updateOrCreate([
                    'module_id' => $moduleId,
                    'type' => $request->type
                ], [
                    'product_id' => $request->product_id,
                ]);
            } else {
                $existingHeadline = HeadlineSetting::where('type', $request->type)
                    ->where('module_id', $moduleId)
                    ->where('level_two_tag_id', $request->level_two_tag_id)
                    ->first();

                if ($existingHeadline) {
                    $existingHeadline->update([
                        'product_id' => $request->product_id,
                    ]);

                    return response()->json([
                        'status' => JsonResponse::HTTP_OK,
                        'message' => 'Recipes updated to ' . $request->type .  ' headline'
                    ], JsonResponse::HTTP_OK);
                } else {
                    $secondaryHeadLineCount = HeadlineSetting::where('type', $request->type)
                        ->where('module_id', $moduleId)
                        ->count();

                    if ($secondaryHeadLineCount < 4) {
                        HeadlineSetting::create([
                            'module_id' => $moduleId,
                            'type' => $request->type,
                            'product_id' => $request->product_id,
                            'level_two_tag_id' => $request->level_two_tag_id,
                        ]);
                    } else {
                        return response()->json([
                            'status' => JsonResponse::HTTP_BAD_REQUEST,
                            'message' => 'You can only have four secondary headlines at a time. Please remove an existing secondary headline before adding a new one'
                        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Recipes added to ' . $request->type .  ' headline'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['danger' => $e->getMessage()]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('recipes::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('recipes::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy($moduleId, $id, Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            $headline = HeadlineSetting::where('id', $id)->where('module_id', $moduleId)->firstOrfail();
            $headline->delete();
            flash('Headline deleted successfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('recipes.dashboard.headlines.index', [$moduleId, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this Headline', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}