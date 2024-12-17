<?php

namespace Modules\Boats\Http\Controllers\Dashboard;

use Exception;
use Inertia\Inertia;
use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Jobs\HierarchyManagementJob;
use App\Jobs\HierarchyTagManagerJob;
use Illuminate\Contracts\Support\Renderable;
use Modules\Boats\Http\Requests\TagHierarchyRequest;

class HierarchiesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request, $module_id, $level)
    {
        $levelRelation = $this->getRelationName($level);
        $assignedTags = StandardTag::asTag()->where('type', 'product')->whereHas('tagHierarchies', function ($query) use ($module_id, $level) {
            $query->where('L1', $module_id)
                ->where('level_type', $level);
        })->orWhereHas($levelRelation, function ($query) use ($module_id) {
            $query->where('L1', $module_id);
        })->active()->get();

        if ($level > 2) {
            $levelTwoTags = StandardTag::asTag()->where('type', 'product')
                ->whereHas('tagHierarchies', function ($query) use ($module_id, $level) {
                    $query->where('L1', $module_id)
                        ->where('level_type', 2);
                })->orWhereHas('levelTwo', function ($query) use ($module_id) {
                    $query->where('L1', $module_id);
                })->active()->get();
        }

        return Inertia::render('Boats::TagHierarchy/Index', [
            'assignedTags' => $assignedTags,
            'searchedKeyword' => $request->input('keyword'),
            'levelTwoTags' => isset($levelTwoTags) ? $levelTwoTags : [],
            'level' => $level
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('boats::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(TagHierarchyRequest $request, $module_id, $level)
    {
        DB::beginTransaction();
        if ($level > 2) {
            HierarchyManagementJob::dispatch($request->all(), $module_id, $level);
        } else {
            HierarchyTagManagerJob::dispatch($request->all(), $module_id, $level);
        }
        DB::commit();
        flash('Hierarchy updated successfully.', 'success');
        return redirect()->back();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($module_id, $level, $id)
    {
        try {
            $tagHierarchy = TagHierarchy::where(function ($query) use ($module_id, $level, $id) {
                $query->where('L1', $module_id)->where('L' . $level, $id)->when($level == 2, function ($subquery) {
                    $subquery->whereNotNull('L3');
                })->when($level == 3, function ($subquery) {
                    $subquery->whereHas('standardTags');
                });
            })->orWhere(function ($query) use ($id) {
                $query->whereHas('standardTags.businesses.standardTags', function ($query) use ($id) {
                    $query->where('id', $id);
                });
            })->first();
            if ($tagHierarchy) {
                return response()->json([
                    'message' => 'Cannot remove the tag as it is linked against business or other levels on Hierarchies'
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
            return $tagHierarchy;
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('boats::edit');
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
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function getTagWithLevel($module_id, $levelTwo, $levelThree = null)
    {
        $relationName = $this->getRelationName($levelThree ? 4 : 3);
        $levelTags = StandardTag::asTag()->where('type', 'product')->active()
            ->where('name', 'like', '%' . request()->input('keyword') . '%')
            ->whereHas('tagHierarchies', function ($query) use ($module_id, $levelTwo, $levelThree) {
                $query->where('L1', $module_id)->where('L2', $levelTwo)
                    ->where('level_type', is_null($levelThree) ? 3 : 4);
            })->orWhereHas($relationName, function ($query) use ($module_id, $levelTwo, $levelThree) {
                $query->where('L1', $module_id)->where('L2', $levelTwo)
                    ->when($levelThree, function ($query) use ($levelThree) {
                        $query->where('L3', $levelThree);
                    });
            })->paginate(50);

        return $levelTags;
    }

    public function searchStandardTags(Request $request)
    {
        try {
            $tags = StandardTag::where('name', 'like', '%' . request()->input('tag') . '%')->asTag()->whereNotIn('type', ['module', 'brand', 'attribute'])->active()->get();
            return \response()->json([
                'tags' => $tags,
            ], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getRelationName($level)
    {
        switch ($level) {
            case 2:
                return "levelTwo";
            case 3:
                return "levelThree";
            case 4:
                return "levelFour";
        }
    }
}
