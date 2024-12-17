<?php

namespace Modules\Services\Http\Controllers\Dashboard\Service;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\News\Entities\Comment;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {

        $limit = \config()->get('settings.pagination_limit');
        $product = Product::where('uuid', $uuid)->firstOrFail();

        $comments = Comment::with('user')
            ->whereHas('product', function ($query) use ($uuid) {
                $query->where('uuid', $uuid);
            })->where(function ($query) {
                $keyword = request()->keyword;
                if ($keyword) {
                    $query->whereHas('user', function ($query) use ($keyword) {
                        $query->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere('comment', 'like', '%' . $keyword . '%')
                        ->orWhere('created_at', 'like', '%' . $keyword . '%');
                }
            })->latest()
            ->paginate($limit);
        return inertia('Services::Services/Comments/Index', [
            'service' => $product,
            'commentList' => $comments,
            'searchedKeyword' => request()->keyword
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('services::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('services::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('services::edit');
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
    public function destroy($moduleId, $uuid, $id,Request $request)
    {
        try {
            $currentPage = $request->query('page');
            $currentCount = $request->query('currentCount');
            if (auth()->user()->hasRole(['admin', 'newspaper', 'business_owner'])) {
                $comment = Comment::when(auth()->user()->hasRole('business_owner'), function ($query) {
                    $query->whereHas('product.business', function ($subQuery) {
                        $subQuery->where('owner_id', auth()->user()->id);
                    });
                })->findOrFail($id)->delete();
            }
            flash('Comment deleted succesfully', 'success');
            if ($currentCount > 1) {
                return redirect()->back();
            } else {
                $previousPage = max(1, $currentPage - 1);
                return Redirect::route('ervices.dashboard.services.comments.index', [$moduleId,$uuid, 'page' => $previousPage]);
            }
        } catch (ModelNotFoundException $e) {
            flash('Unable to delete this comment', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
