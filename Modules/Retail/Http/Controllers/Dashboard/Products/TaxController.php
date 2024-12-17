<?php

namespace Modules\Retail\Http\Controllers\Dashboard\Products;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Http\Requests\TaxRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaxController extends Controller
{
    protected $product;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->product = Product::whereUuid(Route::current()->parameters['uuid'])->firstOrFail();
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $uuid)
    {
        try {
            return Inertia::render('Retail::Products/Tax/Index', [
                'product' => $this->product,
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('retail::create');
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
        return view('retail::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('retail::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaxRequest $request, $moduleId, $uuid)
    {
        try {
            DB::beginTransaction();
            $product = Product::findOrfail($this->product->id);
            $product->update($request->all());
            flash('Product Tax information updated successfully.', 'success');
            DB::commit();
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this product.', 'danger');
            DB::rollBack();
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            DB::rollBack();
            return \redirect()->back();
        }
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
}
