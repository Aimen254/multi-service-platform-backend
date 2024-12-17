<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;

use App\Models\Business;
use Stripe\StripeClient;
use App\Enums\OrderStatus;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\ModuleSessionManager;
use App\Transformers\BusinessTransformer;
use Modules\Automotive\Http\Requests\GarageRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Boats\Http\Controllers\API\DealershipController;
use Modules\Automotive\Http\Controllers\API\GarageController;
use Modules\RealEstate\Http\Controllers\API\BrokerController;
use Modules\Employment\Http\Controllers\API\EmployerController;
use Modules\Notices\Http\Controllers\API\OrganizationController;
use Modules\Government\Http\Controllers\API\DepartmentController;
use Modules\Services\Http\Controllers\API\ServiceBusinessController;
use Modules\Retail\Http\Controllers\API\BusinessController as BusinessesController;

class BusinessController extends Controller
{
    use StripeSubscription;
    protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($moduleTagId, Request $request)
    {
        try {
            $module = StandardTag::where('id', $moduleTagId)->orWhere('slug', $moduleTagId)->first()->slug;
            $filterFlag = $request->filled('header_filter') ?  true : false;
            $limit = $request->input('limit')
                ? $request->input('limit') : \config()->get('settings.pagination_limit');
            $tagId = request()->input('level_two_tag')
                ? request()->input('level_two_tag') : $moduleTagId;

            if ($request->filled('minimumData') && $request->input('minimumData')) {
                $businesses = Business::select('name as text', 'slug', 'id')
                    // ->whereHas('products')
                    ->whereHas('standardTags', function ($subQuery) use ($module) {
                        $subQuery->where('slug', $module);
                    })
                    ->active()
                    ->get();
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'data' => $businesses,
                ], JsonResponse::HTTP_OK);
            }
            switch ($module) {
                case 'retail':
                    $businessController = app(BusinessesController::class);
                    $business = $businessController->getBusinesses($module, $filterFlag, $tagId);
                    break;
                case 'automotive':
                    $garageController = app(GarageController::class);
                    $business = $garageController->getGarages($module, $tagId, $filterFlag);
                    break;
                case 'boats':
                    $garageController = app(DealershipController::class);
                    $business = $garageController->getGarages($module, $tagId, $filterFlag);
                    break;
                case 'services':
                    $serviceBusinessController = app(ServiceBusinessController::class);
                    $business = $serviceBusinessController->index($module, $tagId);
                    break;
                case 'employment':
                    $employerController = app(EmployerController::class);
                    $business = $employerController->index($module, $tagId, $filterFlag);
                    break;
                case 'government':
                    $departmentController = app(DepartmentController::class);
                    $business = $departmentController->index($module, $tagId, $filterFlag);
                    break;
                case 'notices':
                    $organizationController = app(OrganizationController::class);
                    $business = $organizationController->index($module, $tagId, $filterFlag);
                    break;
                case 'real-estate':
                    $brokerController = app(BrokerController::class);
                    $business = $brokerController->index($module, $tagId, $filterFlag);
                    break;
            }

            // applying orderBy filter
            $topBusinesses = gettype(request()->top_businesses) == 'boolean'
                ? request()->top_businesses : (request()->top_businesses == 'true' ? 1 : 0);
            if (request()->input('top_weekly_vendors')) {
                $business->orWhereHas('orders', function ($query) {
                    $start = Carbon::now()->subWeek()->startOfWeek();
                    $end = Carbon::now()->subWeek()->endOfWeek();
                    $query->where('order_status_id', OrderStatus::Completed)
                        ->whereBetween('created_at', [$start, $end]);
                })->withSum('orders as weekly_sale', 'total')->orderBy('weekly_sale', 'desc');
            } else if (request()->input('top_businesses') && $topBusinesses) {
                $topBusinesses ?? $business->orderBy('reviews_avg', 'desc');
            } else if (request()->has('store_filter') && request()->input('store_filter')) {
                switch (request()->input('store_filter')) {
                    case 'name':
                        $business->orderBy('name', 'asc');
                        break;
                    case 'created_at':
                        $business->orderBy('created_at', 'desc');
                        break;
                    case 'rating':
                        $business->orderBy('reviews_avg', 'desc');
                        break;
                    case 'popular':
                        $business->withSum('orders as weekly_sale', 'total')->orderBy('weekly_sale', 'desc');
                        break;
                    default:
                        $business->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                        break;
                }
                $business->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
            } else if (request()->has('created_date_filter') && request()->input('created_date_filter')) {
                $business->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
            } else if (request()->has('created_date_asc_filter') && request()->input('created_date_asc_filter')) {
                $business->orderBy('created_at', 'asc');
            } else {
                $order_by = $request->input('order') ? $request->input('order') : 'desc';
                $business->orderBy('created_at', $order_by);
            }
            $business = $business->paginate($limit);
            $paginate = apiPagination($business, $limit);
            $options = [
                'module' => $moduleTagId,
                'withProducts' => $request->input('withProducts') && gettype(request()->withProducts) == 'boolean' ? request()->withProducts : (request()->withProducts == 'true' ? \true : \false),
                'withLevelTwoTags' => $request->input('withLevelTwoTags') && gettype(request()->withLevelTwoTags) == 'boolean' ? request()->withLevelTwoTags : (request()->withLevelTwoTags == 'true' ? \true : \false),
                'withLevelThreeTags' => $request->input('withLevelThreeTags') && gettype(request()->withLevelThreeTags) == 'boolean' ? request()->withLevelThreeTags : (request()->withLevelThreeTags == 'true' ? \true : \false),
            ];
            $businesses = (new BusinessTransformer)->transformCollection($business, $options);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $businesses,
                'meta' => $paginate,
            ], JsonResponse::HTTP_OK);

            $business = Business::with([
                'reviews',
                'orders',
                'deliveryZone',
                'businessschedules',
                'businessHolidays',
            ])->whereHas('standardTags', function ($subQuery) use ($tagId) {
                $subQuery->where('id', $tagId)->orWhere('slug', $tagId);
            })
                ->where(function ($query) use ($request) {
                    if ($request->input('open_stores') && $request->input('time_zone')) {
                        //To get day, timezone and local time of user
                        $day = now()->format('l');
                        $time = strtotime(now()->format('H:i:s') . ' UTC');
                        date_default_timezone_set($request->input('time_zone'));
                        $date = now()->format('Y-m-d');
                        $local_time = date('H:i:s', $time);
                        //ends
                        $query->whereHas('businessschedules', function ($query) use ($day, $local_time) {
                            $query->where('name', '=', $day)
                                ->where('status', '=', 'active')
                                ->whereHas('scheduletimes', function ($subQuery) use ($local_time) {
                                    $subQuery->where('open_at', '<=', $local_time)
                                        ->where('close_at', '>=', $local_time);
                                });
                        });
                        $query->whereHas('businessHolidays', function ($subQuery) use ($date) {
                            $subQuery->whereRaw('NOT FIND_IN_SET(?, date)', [$date]);
                        });
                    }
                })->withCount(['reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(rating)'));
                }])->orderBy('id', $order_by)->paginate($limit);
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
    public function store(GarageRequest $request, $moduleId)
    {
        try {
            $message = '';
            DB::beginTransaction();
            $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->firstOrFail();
            ModuleSessionManager::setModule($module->slug);
            $garage = Business::create($request->all());
            if ($garage && $request->file('logo')) {
                $this->addMedia($garage, $request->file('logo'), 'logo');
            }
            if ($garage && $request->file('thumbnail')) {
                $this->addMedia($garage, $request->file('thumbnail'), 'thumbnail');
            }
            if ($garage && $request->file('banner')) {
                $this->addMedia($garage, $request->file('banner'), 'banner');
            }
            if ($garage && $request->file('secondaryBanner')) {
                $this->addMedia($garage, $request->file('secondaryBanner'), 'secondaryBanner');
            }
            $garage->standardTags()->syncWithoutDetaching($module->id);
            DB::commit();
            switch ($module->slug) {
                case 'employment':
                    $message = 'Employer Added Successfully.';
                    break;
                case 'services':
                    $message = 'Provider Added Successfully.';
                    break;
                case 'automotive':
                case 'boats':
                    $message = 'Dealership Added Successfully';
                    break;
                case 'notices':
                    $message = 'Organization Added Successfully';
                    break;
                case 'government':
                    $message = 'Department Added Successfully';
                    break;
                case 'real-estate':
                    $message = 'Broker Added Successfuly';
                    break;
                case 'retail':
                    $message = 'Store Added Successfully';
                    break;
                default:
                    $message = 'Business Added Successfully.';
                    break;
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
                'data'=>  $garage 
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($moduleId, $uuid)
    {
        try {
            $module = StandardTag::where('slug', $moduleId)->orWhere('id', $moduleId)->firstOrFail();
            $options = [
                'module' => $moduleId,
                'withDetails' => true,
                'withOwner' => true,
                'withProducts' => request()->input('withProducts') && gettype(request()->withProducts) == 'boolean' ? request()->withProducts : (request()->withProducts == 'true' ? \true : \false),
                'withLevelTwoTags' => request()->input('withLevelTwoTags') && gettype(request()->withLevelTwoTags) == 'boolean' ? request()->withLevelTwoTags : (request()->withLevelTwoTags == 'true' ? \true : \false),
                'withLevelThreeTags' => request()->input('withLevelThreeTags') && gettype(request()->withLevelThreeTags) == 'boolean' ? request()->withLevelThreeTags : (request()->withLevelThreeTags == 'true' ? \true : \false),
                'header_filter' => request()->input('header_filter'),
                'module' => $module->slug,
                'withUsers' => request()->input('withUsers') ?? false
            ];
            $business = Business::where('uuid', $uuid)->when(!request()->input('disableStatusFilter'), function ($query) {
                $query->where('status', 'active');
            })->withCount(['reviews as reviews_avg' => function ($query) {
                $query->select(DB::raw('avg(rating)'));
            }])->firstOrFail();
            $businesses = (new BusinessTransformer)->transform($business, $options);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $businesses,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GarageRequest $request, $moduleId, $businessUuid)
    {
        try {
            $message = '';
            DB::beginTransaction();
            $module = StandardTag::where('slug', $moduleId)->orWhere('id', $moduleId)->firstOrFail();
            ModuleSessionManager::setModule($module->slug);
            $garage = Business::where('uuid', $businessUuid)->first();
            if (isDataDirty($garage, $request->all())) {
                $garage->update($request->all());
                if ($garage && $request->file('logo')) {
                    $this->addMedia($garage, $request->file('logo'), 'logo');
                }
                if ($garage && $request->file('thumbnail')) {
                    $this->addMedia($garage, $request->file('thumbnail'), 'thumbnail');
                }
                if ($garage && $request->file('banner')) {
                    $this->addMedia($garage, $request->file('banner'), 'banner');
                }
                if ($garage && $request->file('secondaryBanner')) {
                    $this->addMedia($garage, $request->file('secondaryBanner'), 'secondaryBanner');
                }
            }
            DB::commit();
            switch ($module->slug) {
                case 'employment':
                    $message = 'Employer Updated Successfully.';
                    break;
                case 'services':
                    $message = 'Provider Updated Successfully.';
                    break;
                case 'automotive':
                case 'boats':
                    $message = 'Dealership Updated Successfully';
                    break;
                case 'notices':
                    $message = 'Organization Updated Sucsessfully';
                    break;
                case 'government':
                    $message = 'Department Updated Successfully';
                    break;
                case 'real-estate':
                    $message = 'Broker Updated Successfully';
                    break;
                case 'reatil':
                    $message = 'Store Updated Successfully';
                    break;
                default:
                    $message = 'Business Updated Successfully.';
                    break;
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
                'data'=> $garage
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($moduleId, $id)
    {
        try {
            $module = StandardTag::where('slug', $moduleId)->orWhere('id', $moduleId)->firstOrFail();
            $message = 'Dealership Deleted Successfully.';
            switch ($module?->slug) {
                case 'services':
                    $message = 'Service Provider Deleted Successfully.';
                    break;
                case 'employment':
                    $message = 'Emoloyer Deleted Successfully.';
                    break;
                case 'automotive':
                    $message = 'Dealership Deleted Successfully';
                    break;
                case 'notices':
                    $message = 'Organization Deleted Successfully';
                    break;
                case 'government':
                    $message = 'Department Deleted Successfully';
                    break;
                case 'real-estate':
                    $message = 'Broker Deleted Successfully';
                    break;
                case 'retail': 
                    $message = 'Store Deleted Successfully';
                    break;
            }

            $garage = Business::whereUuid($id)->firstOrFail();
            $garage->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function businessSearchSuggestion(Request $request)
    {
        try {
            $businesses = Business::select('id', 'uuid', 'name', 'street_address')
                ->where('name', 'like', '%' . request()->input('keyword') . '%')
                ->orWhere('street_address', 'like', '%' . request()->input('keyword') . '%')
                ->get();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $businesses,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addMedia($garage, $file, $type)
    {
        $width = 280;
        $height = 280;
        switch ($type) {
            case 'logo':
                $logo = config()->get('image.media.logo');
                $logoKeys = array_keys($logo);
                $width = $logoKeys[0];
                $height = $logoKeys[1];
                break;
            case 'banner':
                $logo = config()->get('image.media.banner');
                $logoKeys = array_keys($logo);
                $width = $logoKeys[0];
                $height = $logoKeys[1];
                break;
            case 'secondaryBanner':
                $logo = config()->get('image.media.secondaryBanner');
                $logoKeys = array_keys($logo);
                $width = $logoKeys[0];
                $height = $logoKeys[1];
                break;
        }
        $extension = $file->extension();
        $filePath = saveResizeImage($file, "business/{$type}", $width, $height, $extension);
        $media = $garage->media()->where('type', $type)->first();
        if ($media) {
            \deleteFile($media->path);
            $media->update([
                'path' => $filePath,
                'size' => $file->getSize(),
                'mime_type' => $extension
            ]);
        } else {
            $garage->media()->create([
                'path' => $filePath,
                'size' => $file->getSize(),
                'mime_type' => $extension,
                'type' => $type
            ]);
        }
        return;
    }


    // change business status
    public function changeStatus($moduleId, $id)
    {
        try {
            $module = StandardTag::where('slug', $moduleId)->orWhere('id', $moduleId)->firstOrFail();
            $businessType = '';
            switch ($module->slug) {
                case 'services':
                    $businessType = 'Service Provider';
                    break;
                case 'employment':
                    $businessType = 'Employer';
                    break;
                case 'automotive':
                    $businessType = 'Dealership';
                    break;
                case 'notices':
                    $businessType = 'Organization';
                    break;
                case 'government':
                    $businessType = 'Department';
                    break;
                case 'real-estate':
                    $businessType = 'Broker';
                    break;
                case 'retail':
                    $businessType = 'Store';
                    break;
            }
            DB::beginTransaction();
            $business = Business::findOrFail($id);
            if (!(auth('sanctum')->user()->hasRole(['admin', 'newspaper'])) && ($business->status == 'inactive') && ($business->status_updated_by && $business->status_updated_by != auth()->user('sanctum')->id)) {
                $message = $businessType . " is deactivated by administrators.";
                $status = "danger";
                DB::rollBack();
            } else {
                $business->status = $business->status == 'active' ? 'inactive' : 'active';
                $business->status_updated_by = auth()->user('sanctum')->id;
                $business->save();
                DB::commit();
                $message = "{$businessType}"  . " Status changed successfully.";
                $status = "success";
            }
            return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => $business
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // business  chat permission
    public function businessChatPermission($module, $conversationId) {
        try {
            $business = Business::whereHas('products', function($query) use($conversationId) {
                $query->whereRelation('conversations', 'id', $conversationId)->whereIn('status', ['active', 'sold']);
            })->first();
            if($business) {
                $permission = $business->status == 'active' ? $business?->can_chat : false;
            } else {
                $user = User::where('status', 'active')->whereHas('products', function($query) use($conversationId) {
                    $query->whereRelation('conversations', 'id', $conversationId)->whereIn('status', ['active', 'sold']);
                })->first();
                $permission = $user ? true : false;
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'permission' => $permission
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
