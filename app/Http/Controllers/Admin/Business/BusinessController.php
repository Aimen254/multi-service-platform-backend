<?php

namespace App\Http\Controllers\Admin\Business;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Business;
use Stripe\StripeClient;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\BusinessStreetAddress;
use App\Http\Requests\BusinessRequest;
use App\Jobs\ActivateDeactivateTagProducts;
use App\Http\Requests\BusinessModuleTagRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BusinessController extends Controller
{
    use StripeSubscription;
    public StripeClient $stripeClient;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
        $this->middleware('can:edit_business')->only('edit');
        $this->middleware('can:add_business')->only('create');
        $this->middleware('can:delete_business')->only('destroy');
    }

    public function index($id = null)
    {
        $orderBy = request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc';
        $sortByOrder = request()->form && isset(request()->form['sortByOrder']) ? request()->form['sortByOrder'] : 'desc';
        $user = auth()->user();
        $limit = \config()->get('settings.pagination_limit');
        $business = Business::with(['logo', 'thumbnail', 'banner', 'businessOwner', 'reviews', 'orders', 'standardTags' => function ($query) {
            $query->select(['id', 'name as text', 'type'])->where('type', 'module');
        }])->when($user->user_type === 'business_owner' ||  $user->user_type === 'customer', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->where(function ($query) use ($id) {
            // if ($id != 'settings') {
            //     $query->whereHas('standardTags', function ($query) use ($id) {
            //         $query->where('id', $id);
            //     });
            // }
            if (request()->form && isset(request()->form['status'])) {
                $query->where('status', request()->form['status']);
            }
            if (request()->keyword) {
                $keyword = request()->keyword;
                $query->where('name', 'like', '%' . $keyword . '%');
            }
            if (request()->form && isset(request()->form['reviewRating'])) {
                $rating = request()->form['reviewRating'];
                $query->whereHas('reviews', function ($subQuery) use ($rating) {
                    $subQuery->havingRaw('round(avg(rating)) = ?', ["{$rating}"]);
                });
            }
        })->withCount(['reviews as reviews_avg' => function ($query) {
            $query->select(DB::raw('avg(rating)'));
        }])->withCount('orders')->orderBy('orders_count', $sortByOrder)->orderBy('id', $orderBy)->paginate($limit);

        $businessOwners = User::select(['id', DB::raw('CONCAT(id, \' - \', last_name) as text')])
            ->where('user_type', 'business_owner')->orWhere('user_type', 'customer')->get();
        $moduleTags = StandardTag::select(['id', 'name as text', 'type'])->where('type', 'module')->active()->get();
        return Inertia::render('Business/Index', [
            'businessList' => $business,
            'searchedKeyword' => request()->keyword,
            'businessOwners' => $businessOwners,
            'moduleTags' => $moduleTags,
            'orderBy' => request()->form && isset(request()->form['orderBy']) ? request()->form['orderBy'] : 'desc',
            'status' => request()->form && isset(request()->form['status']) ? request()->form['status'] : null,
            'rating' => request()->form && isset(request()->form['reviewRating']) ? request()->form['reviewRating'] : null,
            'noOfOrders' => request()->form && isset(request()->form['sortByOrder']) ? request()->form['sortByOrder'] : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $businessOwners = User::select(['id', DB::raw('CONCAT(id, \' - \', last_name) as text')])
            ->where('user_type', 'business_owner')->orWhere('user_type', 'customer')->where('status', 'active')->get();
        $moduleTags = StandardTag::select(['id', 'name as text', 'type'])->where('type', 'module')->active()->get();
        return Inertia::render('Business/Create', [
            'businessOwners' => $businessOwners,
            'moduleTags' => $moduleTags
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BusinessRequest $request)
    {
        try {
            return 'called';
            DB::beginTransaction();
            $modules = Arr::flatten(array_column($request->input('module'), 'id'));
            $business = Business::create($request->all());
            $business->standardTags()->syncWithoutDetaching($modules);
            BusinessStreetAddress::streetAddress($business);
            DB::commit();
            flash('Business Added Sucessfully!', 'success');
            return \redirect(route('dashboard.businesses.index', 'settings'));
        } catch (\Exception $e) {
            Db::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($businessUuid)
    {
        try {
            $mediaLogoSizes = \config()->get('image.media.logo');
            $mediaThumbnailSizes = \config()->get('image.media.thumbnail');
            $mediaBannerSizes = \config()->get('image.media.banner');
            $businessOwners = User::select([
                'id',
                DB::raw('CONCAT(id, \' - \', last_name) as text')
            ])->where('user_type', 'business_owner')->orWhere('user_type', 'customer')->get();

            $business = Business::with(['logo', 'thumbnail', 'banner', 'secondaryImages'])
                ->orderBy('created_at', 'desc')
                ->where('uuid', $businessUuid)
                ->first();
            return Inertia::render('Business/Edit', [
                'business' => $business,
                'businessOwners' => $businessOwners,
                'mediaLogoSizes' => $mediaLogoSizes,
                'mediaThumbnailSizes' => $mediaThumbnailSizes,
                'mediaBannerSizes' => $mediaBannerSizes,
                'token' => csrf_token()
            ]);
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BusinessRequest $request, $businessUuid)
    {
        try {
            DB::beginTransaction();
            $business = Business::where('uuid', $businessUuid)->first();
            $business->update($request->all());
            BusinessStreetAddress::streetAddress($business);
            DB::commit();
            flash('Business Updated Successfully.', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this business', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function changeStatus($id)
    {
        try {
            DB::beginTransaction();
            $business = Business::findOrFail($id);
            //checking if business owner has the permission to activate this business or not
            if ($business->status == 'inactive') {
                $permission = $this->checkActiveBusinesses($business, 'check_active_businesses');
                if (!$permission) {
                    DB::rollBack();
                    if (auth()->user()->user_type == 'admin') {
                        flash('Business can not be activated due to subscription limitations.', 'danger');
                        return redirect()->back();
                    }
                    flash('You have activated maximum no of businesses according to your subscription plan.', 'danger', 'dashboard.subscription.subscribe.index');
                    return redirect()->back();
                }
            }
            if (!(auth()->user()->user_type == 'admin' || \auth()->user()->user_type == 'newspaper') && ($business->status == 'inactive') && ($business->status_updated_by && $business->status_updated_by != auth()->user()->id)) {
                $message = "Business is deactivated by administrators.";
                $status = "danger";
                DB::rollBack();
            }
//            elseif (!$business->businessOwner->completed_stripe_onboarding && $business->status == 'inactive') {
//                $message = "Business owner's stripe on-boarding process in incomplete. You can not active this business.";
//                $status = "danger";
//                DB::rollBack();
//            }
            else {
                $business->status = $business->status == 'active' ? 'inactive' : 'active';
                $business->status_updated_by = auth()->user()->id;
                $business->save();
                DB::commit();
                $message = "Business status changed succesfully.";
                $status = "success";
            }
            flash($message, $status);
            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            flash('Unable to find this business', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    /**
     * Store a newly created image in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function storeImages(Request $request)
    {
        try {
            $business = Business::findOrFail($request->businessId);
            $extension = $request->file('image')->extension();
            $filePath = saveResizeImage($request->image, "business/{$request->type}", 1024, $extension);
            $media = $business->media()->where('type', $request->type)->first();
            if ($media) {
                \deleteFile($media->path);
                $media->update([
                    'path' => $filePath,
                    'size' => $request->file('image')->getSize(),
                    'mime_type' => $extension
                ]);
            } else {
                $business->media()->create([
                    'path' => $filePath,
                    'size' => $request->file('image')->getSize(),
                    'mime_type' => $extension,
                    'type' => $request->type
                ]);
            }
            flash('Image changed succesfully', 'success');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * show list of business in header dropdown
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function getBusinessList($id)
    {
        try {
            $user = auth()->user();
            $standardTag = StandardTag::find($id);
            $businesses = $standardTag->businesses()
                ->when($user->user_type === 'business_owner' || $user->user_type === 'customer', function ($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->when(auth()->user()->user_type == 'government_staff', function($query) {
                    $query->whereHas('users', function($query) {
                        $query->where('id', auth()->user()->id);
                    });
                })->get();

            return \response()->json([
                'businesses' => $businesses,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteImages(Request $request, $id)
    {
        try {
            $business = Business::findOrFail($request->businessId);
            $media = $business->media()->findOrFail($id);
            if ($media) {
                \deleteFile($media->path);
            }
            $media->delete();
            $msg = $media->type . ' deleted succesfully';
            flash($msg, 'success');
            return \redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    public function moduleTags(BusinessModuleTagRequest $request)
    {
        try {
            DB::beginTransaction();
            $business = Business::findOrFail($request->id);
            $modules = Arr::flatten(array_column($request->input('module'), 'id'));
            $moduleTags = $business->standardTags()->whereType('module')->whereNotIn('id', $modules)->pluck('id');
            $business->standardTags()->detach($moduleTags);
            $business->standardTags()->syncWithoutDetaching($modules);
            ActivateDeactivateTagProducts::dispatch($business, $modules, $moduleTags);
            DB::commit();
            flash('Module Tag Assigned Successfully.', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this business', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }
}
