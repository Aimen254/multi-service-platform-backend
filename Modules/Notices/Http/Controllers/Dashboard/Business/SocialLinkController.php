<?php

namespace Modules\Notices\Http\Controllers\Dashboard\Business;

use Inertia\Inertia;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Notices\Http\Requests\SocialLinksRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SocialLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $business_id)
    {
        $business = Business::where('uuid', $business_id)->firstOrFail();
        return Inertia::render('Notices::Business/SocialLinks/Index', ['business' => $business]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('notices::create');
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
        return view('notices::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('notices::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(SocialLinksRequest $request, $moduleId, $id, $business_id)
    {
        try {
            $business = Business::where('uuid', $business_id)->firstOrFail();
            $business->update([
                'facebook_id' => $request->facebook_id,
                'twitter_id' => $request->twitter_id,
                'pinterest_id' => $request->pinterst_id,
                'instagram_id' => $request->instagram_id,
            ]);
            flash('Social links added successfully', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
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