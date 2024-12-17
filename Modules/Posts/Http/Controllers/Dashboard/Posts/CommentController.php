<?php

namespace Modules\Posts\Http\Controllers\Dashboard\Posts;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Posts\Entities\Comment;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('posts::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('posts::create');
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
    public function show($moduleId, $uuid, $id)
    {
        try
        {
            $product = Product::where('uuid',$uuid)->firstOrFail();
            $comment = Comment::with('user')->where(function ($query) use ($product, $id) {
                $query->where('model_id', $product->id);
                $query->where('id', $id);
            })->firstOrFail();
            return Inertia::render('Posts::Comments/Show', [
                'commentList' => $comment
            ]);
        }
        catch (ModelNotFoundException $e) {
            flash('Unable to find comment', 'danger');
            return \redirect()->back();
        }
        catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('posts::edit');
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
    public function destroy($moduleId, $uuid, $id)
    {
        try {
            if (auth()->user()->hasRole(['admin', 'newspaper', 'business_owner'])) {
                $comment = Comment::when(auth()->user()->hasRole('business_owner'), function ($query) {
                    $query->whereHas('product', function ($subQuery) {
                        $subQuery->where('user_id', auth()->user()->id);
                    });
                })->findOrFail($id)->delete();
            }           
            flash('Comment deleted succesfully', 'success');
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to delete this comment', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
