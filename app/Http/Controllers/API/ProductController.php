<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use Stripe\StripeClient;
use Illuminate\Support\Str;
use FontLib\Table\Type\name;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\StripeSubscription;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Traits\ProductTagsLevelManager;
use App\Transformers\ProductTransformer;
use App\Transformers\LikesTransformer;
use Illuminate\Support\Facades\Validator;
use Modules\News\Http\Controllers\API\NewsController;
use Modules\Blogs\Http\Controllers\API\BlogController;
use Modules\Boats\Http\Controllers\API\BoatController;
use Modules\Posts\Http\Controllers\API\PostController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Events\Http\Controllers\API\EventController;
use Modules\Events\Http\Controllers\API\EventsController;
use Modules\Recipes\Http\Controllers\API\RecipeController;
use Modules\Taskers\Http\Controllers\API\TaskerController;
use Modules\Notices\Http\Controllers\API\NoticesController;
use Modules\Services\Http\Controllers\API\ServicesController;
use Modules\Automotive\Http\Controllers\API\VehicalController;
use Modules\RealEstate\Http\Controllers\API\PropertyController;
use Modules\Obituaries\Http\Controllers\API\ObituariesController;
use Modules\Classifieds\Http\Controllers\API\ClassifiedController;
use Modules\Government\Http\Controllers\API\GovernmentPostController;
use App\Http\Requests\API\{ProductMediaRequest, ProductMainImageRequest};
use Modules\Retail\Http\Controllers\API\ProductController as RetailProductController;
use App\Models\{Product, Media, Attribute, ProductViews, StandardTag, HeadlineSetting, TagHierarchy};
use Modules\Employment\Http\Controllers\API\PostController as EmploymentPostController;
use App\Traits\ProductPriorityManager;
use App\Traits\SyncMyCategoryProducts;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use StripeSubscription;
    public StripeClient $stripeClient;
    public $deptIds = [];
    /**
     * Display a listing of the resource
     *
     * @return \Illuminate\Http\Response
     */
    private $filterParams;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }


    public function index(Request $request, $module)
    {
        try {
            $module = StandardTag::where('id', $module)->orWhere('slug', $module)->firstOrFail();
            switch ($module->slug) {
                case 'automotive':
                    $vehicle = app(VehicalController::class)->index($request, $module->id);
                    return $vehicle;
                    break;
                case 'retail':
                    $products = app(RetailProductController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'boats':
                    $products = app(BoatController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'posts':
                    $products = app(PostController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'news':
                    $products = app(NewsController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'blogs':
                    $products = app(BlogController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'obituaries':
                    $products = app(ObituariesController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'recipes':
                    $products = app(RecipeController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'services':
                    $products = app(ServicesController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'marketplace':
                    $products = app(ClassifiedController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'taskers':
                    $products = app(TaskerController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'employment':
                    $products = app(EmploymentPostController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'government':
                    $products = app(GovernmentPostController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'notices':
                    $products = app(NoticesController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'real-estate':
                    $products = app(PropertyController::class)->index($request, $module->id);
                    return $products;
                    break;
                case 'events':
                    $products = app(EventsController::class)->index($request, $module->id);
                    return $products;
                    break;
            }
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request, $moduleId)
    {
        try {
            DB::beginTransaction();
            $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->firstOrFail();
            $message = 'Product Added Successfully.';
            $product = null;
            switch ($module->slug) {
                case 'automotive':
                    $product = app(VehicalController::class)->store($request, $module->id);
                    $message = 'Vehicle added successfully.';
                    break;
                case 'retail':
                    $product = app(RetailProductController::class)->store($request, $module->id);
                    $message = 'Product added successfully.';
                    break;
                case 'boats':
                    $product = app(BoatController::class)->store($request, $module->id);
                    $message = 'Boat added successfully.';
                    break;
                case 'marketplace':
                    $product = app(ClassifiedController::class)->store($request, $module->id);
                    $message = 'Item added successfully.';
                    break;
                case 'news':
                    $product = app(NewsController::class)->store($request, $module->id);
                    $message = 'Article added successfully.';
                    break;
                case 'taskers':
                    $product = app(TaskerController::class)->store($request, $module->id);
                    $message = 'Task added successfully.';
                    break;
                case 'recipes':
                    $product = app(RecipeController::class)->store($request, $module->id);
                    $message = 'Recipe added successfully.';
                    break;
                case 'blogs':
                    $product = app(BlogController::class)->store($request, $module->id);
                    $message = 'Blog added successfully.';
                    break;
                case 'obituaries':
                    $product = app(ObituariesController::class)->store($request, $module->id);
                    $message = 'Obituary added successfully.';
                    break;
                case 'services':
                    $product = app(ServicesController::class)->store($request, $module->id);
                    $message = 'Service added successfully.';
                    break;
                case 'employment':
                    $product = app(EmploymentPostController::class)->store($request, $module->id);
                    $message = 'Position added successfully.';
                    break;
                case 'notices':
                    $product = app(NoticesController::class)->store($request, $module->id);
                    $message = 'Notice added successfully.';
                    break;
                case 'government':
                    $product = app(GovernmentPostController::class)->store($request, $module->id);
                    $message = 'Post added successfully.';
                    break;
                case 'real-estate':
                    $product = app(PropertyController::class)->store($request, $module->id);
                    $message = 'Property added successfully.';
                    break;
                case 'events':
                    $product = app(EventsController::class)->store($request, $module->id);
                    $message = 'Event added successfully.';
                    break;
                case 'posts':
                    $product = app(PostController::class)->store($request, $module->id);
                    $message = 'Posts added successfully.';
            }
            if ($module?->slug != 'real-estate') {
                SyncMyCategoryProducts::syncProductToCategory($module->id, $product);
            }
            if (request()->input('tags')) {
                $this->assignAttributeTag($product, $module);
                if ($module?->slug == 'real-estate') {
                    $this->assignSquareFeet($product, $module);
                    SyncMyCategoryProducts::syncProductToCategory($module->id, $product);
                }
            }
            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'product' => $product,
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($moduleId = null, $uuid)
    {
        try {
            $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first();
            $moduleId = $module ? $module->id : null;
            $options = [
                'withDetails' => true,
                'withSecondaryImages' => true,
                'withVariants' => true,
                'businessLevelThreeTags' => request()->input('businessLevelThreeTags'),
            ];

            $product = Product::when(!request()->input('disableStatusFilter') && $module?->slug === 'events', function ($query) {
                $query->whereEventDateNotPassed();
            })->with(['vehicle.maker', 'vehicle.model', 'mainImage', 'user', 'publicProfile', 'events'])
                ->withCount(['likes'])
                ->where('uuid', $uuid)
                ->firstOrFail();
            if (!request()->input('disableStatusFilter')) {
                if (in_array($module->slug, ['automotive', 'boats']) && in_array($product->status, ['sold', 'pending'])) {
                    $business = $product->business()->first();
                    $isOwner = false;
                    if ($business && $business->owner_id == auth('sanctum')->id()) {
                        $isOwner = true;
                    } else if ($product->user_id == auth('sanctum')->id()) {
                        $isOwner = true;
                    }
                    if (!$isOwner) {
                        return response()->json([
                            'status' => JsonResponse::HTTP_NOT_FOUND,
                            'message' => 'Product is inactive'
                        ], JsonResponse::HTTP_NOT_FOUND);
                    }
                } else if ($product?->status == 'inactive') {
                    return response()->json([
                        'status' => JsonResponse::HTTP_NOT_FOUND,
                        'message' => 'Product is inactive'
                    ], JsonResponse::HTTP_NOT_FOUND);
                }
            }

            // update product views count
            if (request()->input('ip_address')) {
                $this->updateProductViews($product, request()->ip_address);
            }
            $products = (new ProductTransformer)->transform($product, $options);

            //body styles
            $bodyStyles = StandardTag::whereHas('levelTwo')->whereIn('name', \config()->get('automotive.body_styles'))->select(['id', 'name as text', 'slug'])
                ->get();

            //product attached body style.
            $productBodyStyle = $product->standardTags()->whereHas('levelTwo')->whereIn('name', \config()->get('automotive.body_styles'))
                ->select(['id', 'name as text', 'slug'])->first();

            // product assigned standard tags
            $assignedStandards = $product->standardTags()->with(['tags_' => function ($query) use ($product) {
                $query->whereHas('products', function ($subQuery) use ($product) {
                    $subQuery->where('id', $product->id);
                });
            }])->withPivot(['attribute_id'])->with('attribute')->asTag()->active()->whereType('attribute')->get();

            //all attributes for automoive
            $attributes = Attribute::with(['standardTags' => function ($query) {
                $query->where('type', 'attribute')->with('attribute');
            }])->active()->whereHas('moduleTags', function ($query) use ($moduleId) {
                $query->where('id', $moduleId);
            })->get();
            $productTags = $product->standardTags()->withPivot(['hierarchy_id'])->get();
            $hierarchyIds = $productTags->pluck('pivot.hierarchy_id')->filter()->unique();

            $hierarchies = [];

            foreach ($hierarchyIds as $id) {
                $hierarchy = TagHierarchy::where('id', $id)->with([
                    'levelTwo' => function ($query) {
                        $query->select(['id', 'name as text', 'slug']);
                    },
                    'levelThree' => function ($query) {
                        $query->select(['id', 'name as text', 'slug']);
                    }
                ])->first();


                // Get a single standardTag that meets the criteria
                $levelFour = StandardTag::whereHas('tagHierarchies', function ($query) use ($moduleId, $hierarchy) {
                    $query->where('L1', $moduleId)->where('L2', $hierarchy->L2)->where('L3', $hierarchy->L3);
                })->whereHas('productTags', function ($subQuery) use ($id, $product) {
                    $subQuery->where('id', $product->id);
                    $subQuery->where('product_standard_tag.hierarchy_id', $id);
                })
                    ->select(['id', 'name as text', 'slug'])
                    ->first();

                $hierarchies[] = (object) [
                    'L2' => $hierarchy->levelTwo,
                    'L3' => $hierarchy->levelThree,
                    'L4' => $levelFour
                ];
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $products,
                'hierarchies' => $hierarchies,
                'bodyStyles' => $bodyStyles,
                'productBodyStyle' => $productBodyStyle ?? null,
                'assignedStandardTags' =>  $assignedStandards,
                'attributes' => $attributes,
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
    public function update(ProductRequest $request, $moduleId, $productUuid)
    {
        try {
            DB::beginTransaction();
            $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->firstOrFail();
            $message = 'Product Updated Successfully.';
            $product = null;
            switch ($module->slug) {
                case 'automotive':
                    $product =  app(VehicalController::class)->update($request, $moduleId, $productUuid);
                    $message = 'Vehicle Updated Successfully.';
                    break;
                case 'marketplace':
                    $product = app(ClassifiedController::class)->update($request, $moduleId, $productUuid);
                    $message = 'Item Updated Successfully.';
                    break;
                case 'taskers':
                    $product = app(TaskerController::class)->update($request, $moduleId, $productUuid);
                    $message = 'Task Updated Successfully.';
                    break;
                case 'boats':
                    $product = app(BoatController::class)->update($request, $moduleId, $productUuid);
                    $message = 'Boat Updated Successfully.';
                    break;
                case 'news':
                    $product = app(NewsController::class)->update($request, $moduleId, $productUuid);
                    $message = 'Article Updated Successfuly.';
                    break;
                case 'recipes':
                    $product = app(RecipeController::class)->update($request, $moduleId, $productUuid);
                    $message = 'Recipe Updated Successfuly.';
                    break;
                case 'blogs':
                    $product = app(BlogController::class)->update($request, $module->id, $productUuid);
                    $message = 'Blog Updated Successfuly.';
                    break;
                case 'obituaries':
                    $product = app(ObituariesController::class)->update($request, $module->id, $productUuid);
                    $message = 'Obituary Updated successfully.';
                    break;
                case 'services':
                    $product = app(ServicesController::class)->update($request, $module->id, $productUuid);
                    $message = 'Service Updated successfully.';
                    break;
                case 'employment':
                    $product = app(EmploymentPostController::class)->update($request, $module->id, $productUuid);
                    $message = 'Position Updated Successfuly.';
                    break;
                case 'notices':
                    $product = app(NoticesController::class)->update($request, $module->id, $productUuid);
                    $message = 'Notice Updated successfully.';
                    break;
                case 'government':
                    $product = app(GovernmentPostController::class)->update($request, $module->id, $productUuid);
                    $message = 'Post Updated successfully.';
                    break;
                case 'real-estate':
                    $product = app(PropertyController::class)->update($request, $module->id, $productUuid);
                    $message = 'Property Updated successfully.';
                    break;
                case 'events':
                    $product = app(EventsController::class)->update($request, $module->id, $productUuid);
                    $message = 'Event Updated successfully.';
                    break;
                case 'retail':
                    $product = app(RetailProductController::class)->update($request, $module->id, $productUuid);
                    $message = 'Product Updated successfully.';
                    break;
                case 'posts':
                    $product = app(PostController::class)->update($request, $module->id, $productUuid);
                    $message = 'Post Updated successfully.';
                    break;
            }
            if ($module?->slug != 'real-estate') {
                SyncMyCategoryProducts::syncProductToCategory($module->id, $product);
            }
            if (request()->input('tags')) {
                $this->assignAttributeTag($product, $module);
                if ($module?->slug == 'real-estate') {
                    $this->assignSquareFeet($product, $module);
                    SyncMyCategoryProducts::syncProductToCategory($module->id, $product);
                }
            }
            DB::commit();
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($moduleId, $productUuid)
    {
        try {
            $product = Product::where('uuid', $productUuid)->firstOrfail();
            $product->delete();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Deleted Successfully.',
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
     * Update the main Image.
     *
     * @param ProductMainImageRequest $request
     * @param $moduleId
     * @param $uuid
     * @param $id
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function updateMainImage(ProductMainImageRequest $request, $moduleId, $uuid, $id = null)
    {
        try {
            $news = Product::whereUuid($uuid)->firstOrFail();
            $previousStatus = $news->status;

            switch ($moduleId) {
                case 'automotive':
                    $image = \config()->get('automotive.media.vehicle');
                    break;
                case 'boats':
                    $image = \config()->get('boats.media.boat');
                    break;
                case 'taskers':
                    $image = \config()->get('taskers.media.tasker');
                    break;
                case 'news':
                    $image = \config()->get('news.media.news');
                    break;
                case 'recipes':
                    $image = \config()->get('recipes.media.recipes');
                    break;
                case 'blogs':
                    $image = \config()->get('blogs.media.blog');
                    break;
                case 'obituaries':
                    $image = \config()->get('obituaries.media.obituaries');
                    break;
                case 'services':
                    $image = \config()->get('services-module.media.services');
                    break;
                case 'employment':
                    $image = \config()->get('employment.media.posts');
                    break;
                case 'notices':
                    $image = \config()->get('notices.media.notice');
                    break;
                case 'government':
                    $image = \config()->get('government.media.posts');
                    break;
                case 'real-estate':
                    $image = \config()->get('realestate.media.property');
                    break;
                case 'events':
                    $image = \config()->get('events.media.events');
                    break;
                case 'posts':
                    $image = \config()->get('posts.media.posts');
                    break;
                default:
                    $image = \config()->get('classifieds.media.classified');
                    break;
            }

            $width = $image['width'];
            $height = $image['height'];

            $mediaData = [
                'path' => saveResizeImage($request->image, 'products', $width, $height, $request->image->extension()),
                'size' => $request->image->getSize(),
                'mime_type' => $request->image->extension(),
                'type' => 'image',
                'is_external' => 0,
            ];

            if ($id) {
                $media = Media::findOrFail($id);
                deleteFile($media->path);
                $media->update($mediaData);
            } else {
                $media = $news->media()->create($mediaData);
            }
            if (!in_array($previousStatus, ['tags_error', 'active'])) {
                $news->status = 'active';
                $news->previous_status = $previousStatus;
                $news->saveQuietly();
            }

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Main image updated successfully.',
                'data' => $media,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function productMedia(ProductMediaRequest $request, $moduleId, $uuid)
    {
        $config = $this->fileConfigurations($moduleId);

        try {
            DB::beginTransaction();
            $product = Product::whereUuid($uuid)->firstOrFail();
            $items = array();
            foreach ($request->file as $value) {
                $extension = $value->extension();
                $filePath = saveResizeImage($value, "products", $config['width'], $config['height'],  $extension);
                $media = $product->media()->create([
                    'path' => $filePath,
                    'size' => $value->getSize(),
                    'mime_type' => $value->extension(),
                    'type' => 'image'
                ]);
            }
            $product = Product::with(['secondaryImages'])->whereUuid($uuid)->firstOrFail();
            DB::commit();
            return \response()->json([
                'media' => $product->secondaryImages,
                'message' => 'Image added!'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function fileConfigurations($moduleId): array
    {
        $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first()->slug;
        switch ($module) {
            case 'automotive':
                $image = \config()->get('automotive.media.vehicle');
                break;
            case 'boats':
                $image = \config()->get('boats.media.boat');
                break;
            case 'taskers':
                $image = \config()->get('taskers.media.tasker');
                break;
            case 'news':
                $image = \config()->get('news.media.news');
                break;
            case 'recipes':
                $image = \config()->get('recipes.media.recipes');
                break;
            case 'blogs':
                $image = \config()->get('blogs.media.blog');
                break;
            case 'obituaries':
                $image = \config()->get('obituaries.media.obituaries');
                break;
            case 'employment':
                $image = \config()->get('employment.media.posts');
                break;
            case 'notices':
                $image = \config()->get('notices.media.notice');
                break;
            case 'government':
                $image = \config()->get('government.media.posts');
                break;
            case 'real-estate':
                $image = \config()->get('realestate.media.property');
                break;
            case 'events':
                $image = \config()->get('events.media.events');
                break;
            default:
                $image = \config()->get('classifieds.media.classified');
                break;
        }

        $width = $image['width'];
        $height = $image['height'];
        $size = $image['size'];

        return [
            'width' => $width,
            'height' => $height,
            'size' => $size
        ];
    }

    public function destroyMedia($moduleId, $uuid, $id)
    {
        try {
            $media = Media::findOrFail($id);
            deleteFile($media->path);
            $media->delete();
            $product = Product::whereUuid($uuid)->withCount('secondaryImages')->firstOrFail();
            $count = $product->secondary_images_count;

            return \response()->json([
                'count' => $count,
                'message' => 'Image Removed!'
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return \response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function relatedItems(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'level_one' => 'required|exists:standard_tags,id',
            'level_two' => 'required|exists:standard_tags,id',
            'level_three' => 'required|exists:standard_tags,id',
            'level_four' => 'required|exists:standard_tags,id',
            'product_id' => 'required|exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $limit = request()->limit ? request()->limit : 4;

        $products = Product::active()
            ->whereHas('standardTags', function ($query) {
                $query->select('standard_tags.*', DB::raw('count(*) as total'))
                    ->whereIn('id', [
                        request()->level_one,
                        request()->level_two,
                        request()->level_three,
                        request()->level_four,
                    ])->having('total', '>=', 4);
            })->whereNotIn('id', [request()->product_id])
            ->orderBy('created_at', 'desc')->paginate($limit);

        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => (new ProductTransformer)->transformCollection($products)
        ], JsonResponse::HTTP_OK);
    }


    // product views by user ip to make them popular
    private function updateProductViews($product, $ip)
    {
        $user = auth('sanctum')->user();
        $data = !$product->views()->where('ip_address', $ip)->get();
        if (!$product->views()->where('ip_address', $ip)->where('user_id', $user?->id)->exists()) {
            // creating record in product views table
            $product->views()->create([
                'ip_address' => $ip,
                'module_id' => request()->module_id,
                'user_id' => $user?->id
            ]);

            // incrementing the count in products table
            $product->views_count = ++$product->views_count;
            $product->saveQuietly();
        } else {

            $product->views()->where('ip_address', $ip)->update([
                'updated_at' => Carbon::now(),
                'module_id' => request()->input('module_id')
            ]);
        }
    }
    public function getHeadlines($moduleId)
    {
        try {
            $module = StandardTag::where('id', $moduleId)->where('type', 'module')->firstOrFail();
            $options = ['withSecondaryImages' => true]; // option to get articles with its all images
            $primayHeadline = null;
            // get primary headline
            if (!request()->level_two_tag_id) {
                $primayHeadline = HeadlineSetting::whereHas('article', function ($query) {
                    $query->where('status', 'active');
                })->where('module_id', $module->id)->when($module->slug == 'news', function ($query) {
                    //                    $query->whereDate('created_at', today());
                })->where('type', 'Primary')->first();
                $primayHeadline = $primayHeadline ? (new ProductTransformer)->transform($primayHeadline?->article, $options) : null;
            }

            // get secondary headlines
            $secondarHeadline = HeadlineSetting::whereHas('article', function ($query) {
                $query->where('status', 'active');
            })
                ->where('module_id', $module->id)
                ->when($module->slug == 'news', function ($query) {
                    // $query->whereDate('created_at', today());
                })
                ->where('type', 'Secondary')
                ->orderByRaw("CASE
            WHEN level_two_tag_id IN (
                SELECT id FROM standard_tags WHERE slug = 'world'
            ) THEN 1
            WHEN level_two_tag_id IN (
                SELECT id FROM standard_tags WHERE slug = 'nation'
            ) THEN 2
            WHEN level_two_tag_id IN (
                SELECT id FROM standard_tags WHERE slug = 'metro'
            ) THEN 3
            WHEN level_two_tag_id IN (
                SELECT id FROM standard_tags WHERE slug = 'sports'
            ) THEN 4
            ELSE 5
        END")
                ->take(4)
                ->latest()
                ->get();


            $secondarHeadline = $secondarHeadline->map(function ($item) {
                return $item->article;
            });
            $secondarHeadline = (new ProductTransformer)->transformCollection($secondarHeadline);

            // get secondary headline as primary for level two page on front end
            if (request()->level_two_tag) {
                $levelTowTag = StandardTag::where('slug', request()->level_two_tag)->firstOrFail();
                $primayHeadline = HeadlineSetting::whereHas('article', function ($query) {
                    $query->where('status', 'active');
                })->where('module_id', $module->id)->when($module->slug == 'news', function ($query) {
                    //                    $query->whereDate('created_at', today());
                })->where('level_two_tag_id', $levelTowTag?->id)->where('type', 'Secondary')->first();
                $primayHeadline = $primayHeadline ? (new ProductTransformer)->transform($primayHeadline->article, $options) : null;

                $secondarHeadline  = Product::query()->whereRelation('standardTags', 'id', $module->id)->where(function ($query) use ($module, $primayHeadline) {
                    $query->whereHas('standardTags', function ($query) {
                        $query->where(function ($query) {
                            $query->where('slug', request()->input('level_two_tag'));
                        })->where(function ($subQuery) {
                            $subQuery->whereHas('tagHierarchies', function ($subQuery) {
                                $subQuery->where('level_type', 2);
                            })->orWhereHas('levelTwo');
                        });
                    });
                })->where('id', '!=', $primayHeadline['id'])->take(4)->latest()->get();

                $secondarHeadline = (new ProductTransformer)->transformCollection($secondarHeadline);
            }

            return response()->json([
                'message' => 'Headline fetched successfully!',
                'primary_headline' => $primayHeadline,
                'secondary_headline' => $secondarHeadline
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateStatus($moduleId, $uuid)
    {
        try {
            $product = Product::whereUuid($uuid)->firstOrFail();
            $previousStatus = $product->status;
            // Check if the product has no media
            if (in_array($moduleId, ['automotive', 'boats']) && $product->media()->count() == 0) {
                return response()->json([
                    'status' => JsonResponse::HTTP_BAD_REQUEST,
                    'message' => "Product has no media, status cannot be changed."
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
            if (request()->input('status')) {
                $product->status = request()->input('status');
            } else {
                if ($product->status == 'active') {
                    $product->status = 'inactive';
                } else {
                    $product->status = 'active';
                }
            }
            $product->previous_status = $previousStatus;
            $product->saveQuietly();
            $product->refresh();
            ProductPriorityManager::updatePriorityBasedOnStatus($product);
            return response()->json([
                'message' => "Status updated successfully!"
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function assignSquareFeet($product, $module){
        if (request()->input('square_feet')) {
            $squareFeetStandardTag = StandardTag::where('name', request()->input('square_feet'))->where('name', request()->input('square_feet'))->first();
            $attribute = Attribute::whereIn('slug', ['square-feet', 'square-foot','square_feet',])->first();
            if (!$attribute) {
                $attribute = Attribute::create([
                    'name' => 'Square Feet',
                    'slug' => 'square-feet',
                    'global_tag_id' => null,
                    'manual_position' => 0,
                    'status' => 'active', 
                ]);
            }
            if (!$squareFeetStandardTag) {
                $squareFeetStandardTag = StandardTag::create([
                    'name' => request()->input('square_feet'),
                    'slug' => request()->input('square_feet'),
                    'priority' => 2,
                    'type' => 'attribute',
                    'attribute_id' => $attribute->id
                ]);
            }
            $attribute->standardTags()->syncWithoutDetaching($squareFeetStandardTag);
            // check if product has square-feet attribute tag
            $productSquareFeetTags = $product->standardTags()->whereRelation('attribute', 'id', $attribute->id)->where('type', '<>', 'module')->pluck('id');

            // remove previous square-feet tags
            $product->standardTags()->detach($productSquareFeetTags);

            // attach new square-feet tags
            $product->standardTags()->attach($squareFeetStandardTag->id, [
                'attribute_id' => $attribute->id,
                'product_id' => $product->id
            ]);
            // $product->standardTags()->syncWithoutDetaching($squareFeetStandardTag->id);
        }
    }
    public function assignAttributeTag($product, $module)
    {
        DB::beginTransaction();
        // check year attribute
        if (request()->input('year')) {
            $yearStandardTag = StandardTag::where('slug', request()->input('year'))->first();
            $attribute = Attribute::where('slug', 'year')->firstOrFail();
            if (!$yearStandardTag) {
                $yearStandardTag = StandardTag::create([
                    'name' => request()->input('year'),
                    'slug' => Str::slug(request()->input('year')),
                    'priority' => 2,
                    'type' => 'attribute',
                    'attribute_id' => $attribute?->id
                ]);
            }
            $attribute->standardTags()->syncWithoutDetaching($yearStandardTag);

            // check if product has years attribute tag
            $productsYearTags = $product->standardTags()->whereRelation('attribute', 'id', $attribute->id)->where('type', '<>', 'module')->pluck('id');

            // remove previous years tags
            $product->standardTags()->detach($productsYearTags);

            // attach new year tags
            $product->standardTags()->syncWithoutDetaching($yearStandardTag->id);
        }

        $standardTags = collect(json_decode(request()->input('tags')));

        // convert in to array
        $resultArray = $standardTags->flatMap(function ($item) {
            return collect($item)->flatMap(function ($value, $key) {
                return collect($value)->map(function ($attribute) use ($key) {
                    return (object)[
                        'id' => $attribute->id,
                        'text' => $attribute->text,
                        'type' => $attribute->type,
                        'status' => $attribute->status,
                        'slug' => $attribute->slug,
                        'priority' => $attribute->priority,
                        'pivot' => $attribute->pivot ?? null,
                        'attribute' => (object)[
                            'id' => $attribute->attribute[0]->id,
                            'global_tag_id' => $attribute->attribute[0]->global_tag_id,
                            'name' => $attribute->attribute[0]->name,
                            'slug' => $attribute->attribute[0]->slug,
                            'status' => $attribute->attribute[0]->status,
                            'manual_position' => $attribute->attribute[0]->manual_position,
                            'created_at' => $attribute->attribute[0]->created_at,
                            'updated_at' => $attribute->attribute[0]->updated_at,
                            'pivot' => (object)[
                                'standard_tag_id' => $attribute->attribute[0]->pivot->standard_tag_id,
                                'attribute_id' => $attribute->attribute[0]->pivot->attribute_id,
                            ],
                        ],
                    ];
                });
            });
        })->toArray();

        $standardTags = $resultArray;
        foreach ($standardTags as $standardTag) {
            if (isset($standardTag->pivot) && $standardTag->pivot->attribute_id) {
                $attribute = Attribute::where('id', $standardTag->pivot->attribute_id)->firstOrFail();

                if ($attribute && ($attribute->slug == 'interior-color' || $attribute->slug == 'exterior-color' || $module?->slug == 'real-estate' ||  $attribute->slug == 'test-attrbute')) {
                    $isExist = $product->standardTags()
                        ->withPivot(['attribute_id'])
                        ->wherePivot('attribute_id', $attribute->id)
                        ->wherePivot('standard_tag_id', $standardTag->id)
                        ->where('product_id', $product->id)
                        ->first();


                    if (!$isExist) {
                        $product->standardTags()->attach($standardTag->id, [
                            'attribute_id' => $attribute->id,
                            'product_id' => $product->id
                        ]);
                    }
                } else {
                    $product->standardTags()->syncWithoutDetaching($standardTag->id);
                }
            } else {
                if ($standardTag->attribute->slug == 'interior-color' || $standardTag->attribute->slug == 'exterior-color' || $module?->slug == 'real-estate') {
                    $isExist = $product->standardTags()
                        ->withPivot(['attribute_id'])
                        ->wherePivot('attribute_id', $standardTag->attribute->id)
                        ->wherePivot('standard_tag_id', $standardTag->id)
                        ->where('product_id', $product->id)
                        ->first();

                    if (!$isExist) {
                        $product->standardTags()->attach($standardTag->id, [
                            'attribute_id' => $standardTag->attribute->id,
                            'product_id' => $product->id
                        ]);
                    }
                } else {
                    $product->standardTags()->syncWithoutDetaching($standardTag->id);
                }
            }
        }

        // removed tags
        $removedTags = collect(json_decode(request()->input('removedTags')))->toArray();
        foreach ($removedTags as $removedTag) {
            if ($removedTag->pivot->attribute_id) {
                $attribute = Attribute::where('id', $removedTag->pivot->attribute_id)->firstOrFail();
                if ($attribute && ($attribute->slug == 'interior-color' || $attribute->slug == 'exterior-color') || $module?->slug == 'real-estate') {
                    $product->standardTags()
                        ->wherePivot('attribute_id', $attribute->id)
                        ->wherePivot('standard_tag_id', $removedTag->id)
                        ->where('product_id', $product->id)
                        ->detach();
                } else {
                    $product->standardTags()->detach($removedTag->id);
                }
            } else {
                $product->standardTags()->detach($removedTag->id);
            }
        }
        $removingAttributes = collect(json_decode(request()->input('removedTags')))->pluck('id')->toArray();
        if (count($removingAttributes) > 0) {
            ProductTagsLevelManager::priorityTwoTags($product, null, $removingAttributes, 'attribute');
        }
        ProductTagsLevelManager::priorityTwoTags($product);
        DB::commit();
    }


    public function likeUsers($moduleId = null, $uuid)
    {
        try {
            $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first();
            $moduleId = $module ? $module->id : null;
    
            $product = Product::where('uuid', $uuid)->firstOrFail();
            
            // Paginate the likes
            $likes = $product->likes()->paginate(request()->input('limit'));
            
            // Generate pagination meta data
            $paginate = apiPagination($likes);
            
            // Transform each like using the LikesTransformer
            $likesUser = $likes->map(function($like) {
                return (new LikesTransformer())->transform($like);
            });
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $likesUser,
                'meta' => $paginate
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
    
}
