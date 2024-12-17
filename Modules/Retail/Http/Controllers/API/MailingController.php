<?php

namespace Modules\Retail\Http\Controllers\API;

use App\Models\Business;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;
use Modules\Retail\Entities\Mailing;
use Modules\Retail\Http\Requests\MailingRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MailingController extends Controller
{



    public function index($moduleId, $businessUuid)
    {
        $limit = request()->input('limit') ? request()->input('limit') : config('settings.pagination_limit');
        $business = Business::whereUuid($businessUuid)->first();
        $query = Mailing::where('business_id', $business->id)
            ->orderBy('id', 'desc');
    
        if (request()->keyword) {
            $keyword = request()->keyword;
            $query->where(function ($query) use ($keyword) {
                $query->whereRaw('title like ?', ["%{$keyword}%"])
                    ->orWhere('price', $keyword)
                    ->orWhere('minimum_amount', $keyword);
            });
        }
        $mailings = $query->paginate($limit);
        $mailings->getCollection();
        $paginate = apiPagination($mailings, $limit);
        return response()->json([
            'mailings' => $mailings->items(),
            'searchedKeyword' => request()->keyword,
            'meta' => $paginate
        ]);
    }
    
    
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(MailingRequest $request)
    {
        try {
            $business = Business::whereUuid($request->business_id)->first();
            $mailExists = Mailing::where('title', $request->input('title'))
                ->where('business_id', $business->id)
                ->onlyTrashed()
                ->first();
            if ($mailExists) {
                $mailExists->restore();
            }
            $mailing = Mailing::updateOrCreate(
                ['title' => $request->input('title'), 'business_id' =>  $business->id],
                ['minimum_amount' => $request->input('minimum_amount'), 'price' => $request->input('price'), 'status' => 'active']
            );
            return response()->json([
                'message' => 'Business Mail setting added successfully.',
                'mailing' => $mailing,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add business mail setting.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function show(Request $request, $moduleId, $id)
    {
        try {
            $mailing = Mailing::findOrFail($id);
            return response()->json([
                'mailing' => $mailing
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Unable to find this business mail setting',
                'status' => 'danger'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'danger'
            ], 500);
        }
    }


    public function update(MailingRequest $request, $uuid, $id)
    {
        try {
            $mailing = Mailing::findOrFail($id);
            $updateData = $request->except('business_id');
            $mailing->update($updateData);
            return response()->json([
                'mailing' => $mailing,
                'message'=>'mailing setting updated succesfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Unable to find this business mail setting',
                'status' => 'danger'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'danger'
            ], 500);
        }
    }


    public function destroy(Request $request, $moduleId, $id)
    {
        try {
            $mailing = Mailing::findOrFail($id);
            $mailing->delete();
            return response()->json([
                'message' => 'Business mail setting deleted successfully',
                'status' => 'success'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Unable to find this business mail setting',
                'status' => 'danger'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'danger'
            ], 500);
        }
    }

    public function changeStatus($uuid, $id)
    {
        try {
            $mailing = Mailing::findOrFail($id);
            $mailing->statusChanger()->save();

            return response()->json([
                'message' => 'Business mail setting status changed successfully',
                'status' => 'success'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Unable to find this business mail setting',
                'status' => 'danger'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'danger'
            ], 500);
        }
    }

}
