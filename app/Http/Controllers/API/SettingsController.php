<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Transformers\SettingTransformer;
use Modules\Retail\Entities\BusinessSetting;
use App\Models\Business;
use Modules\Retail\Http\Requests\BusinessSettingRequest;



class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $business = null;
        try {
            if ($request->input('checkSandBox')) {
                $flag = Setting::where('group', 'stripe_connect_settings')->where('key', 'sandbox')->first()->value;
                $setting = Setting::when($flag == 'yes', function ($query) {
                    $query->where('group', 'stripe_connect_settings')->where('key', 'client_id_sandbox');
                }, function ($query) {
                    $query->where('group', 'stripe_connect_settings')->where('key', 'client_id_production');
                })->get();
            } else {
                $setting = Setting::where(function ($query) use ($request) {
                    if ($request->input('type')) {
                        $query->where('type', $request->input('type'));
                    }
                    if ($request->input('group')) {
                        $query->where('group', $request->input('group'));
                    }
                    if ($request->input('key')) {
                        $query->where('key', $request->input('key'));
                    }
                })->get();
            }
            if (request()->input('buinessSettings')) {
                $business = Business::with(['banner', 'logo', 'thumbnail', 'settings' => function ($query) {
                    $query->whereIn('key', ['minimum_purchase', 'tax_apply', 'tax_type', 'tax_percentage', 'deliverable']);
                }])->where('uuid', request()->input('uuid'))->first();
            }
            $settings = (new SettingTransformer)->transformCollection($setting);
            $mediaSizes = \config()->get('image.media');
            $data['settings'] = $settings;
            $data['mediaSizes'] = $mediaSizes;
            $data['automotiveMedia'] = \config()->get('automotive.media');
            $data['boatsMedia'] = \config()->get('boats.media');
            $data['classifiedsMedia'] = \config()->get('classifieds.media');
            $data['taskersMedia'] = \config()->get('taskers.media');
            $data['newsMedia'] = \config()->get('news.media');
            $data['blogsMedia'] = \config()->get('blogs.media');
            $data['obituariesMedia'] = \config()->get('obituaries.media');
            $data['recipesMedia'] = \config()->get('recipes.media');
            $data['employmentMedia'] = \config()->get('employment.media');
            $data['body_styles'] = \config()->get('automotive.body_styles');
            $data['automotiveMedia'] = \config()->get('automotive.media');
            $data['noticesMedia'] = \config()->get('notices.media');
            $data['governmentMedia'] = \config()->get('government.media');
            $data['RealEstateMedia'] = \config()->get('realestate.media');
            $data['eventsMedia'] = \config()->get('events.media');
            $data['retailMedia'] = \config()->get('retail.media');
            $data['postsMedia'] = \config()->get('posts.media');


            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $data,
                'business' => $business

            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)

    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BusinessSettingRequest $request, $businessUuid)
    {
        try {
            DB::beginTransaction();
            $business = Business::where('uuid', $businessUuid)->firstOrFail(); 
            $business->update([
                'url' => $request->businessUrl,
                'is_direct_url' => $request->input('businessUrl') ? 1 : 0,
                'is_featured' => $request->input('isFeatured'),
            ]);
            // Updating business settings
            foreach ($request->settings as $setting) {
                $businessSetting = BusinessSetting::find($setting['id']);
                if ($businessSetting) {
                    $businessSetting->value =  $setting['value'];
                    $businessSetting->save();
                }
            }
            // Update deliverable setting
            $businessDeliverable = BusinessSetting::where('key', 'deliverable')->where('business_id', $business->id)->first();
            if ($businessDeliverable) {
                $businessDeliverable->value = $request->deliverable ? 1 : 0;
                $businessDeliverable->save();
            }
            DB::commit();
            // Return a JSON response indicating success
            return response()->json([
                'message' => 'Settings updated successfully',
                'data'=>  $business
        ], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Business not found'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // sales banners

    public function salesBanner()
    {
        try {
            $banners = [
                [
                    'id' => 1,
                    'image' => url('/images/adds/blackfriday.jpg')
                ],
                [
                    'id' => 2,
                    'image' => url('/images/adds/halloweensale.webp')
                ],
                [
                    'id' => 2,
                    'image' => url('/images/adds/sale.png')
                ],
            ];
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $banners,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
