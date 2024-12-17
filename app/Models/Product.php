<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Traits\ManageMyCategory;
use App\Traits\ProductTitleTags;
use App\Events\UpdateBookedEvents;
use Illuminate\Support\Facades\DB;
use Modules\Events\Entities\Event;
use Modules\News\Entities\Comment;
use Illuminate\Support\Facades\Log;
use App\Traits\ModuleSessionManager;
use App\Traits\SyncMyCategoryProducts;
use Illuminate\Support\Facades\Route;
use App\Events\UpdateBookmarkedEvents;
use App\Traits\ActiveInactiveProducts;
use App\Traits\ProductPriorityManager;
use App\Traits\ApplyDiscountOnVariants;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;
use Modules\Automotive\Entities\DreamCar;
use Modules\Events\Entities\EventBooking;
use Modules\Events\Entities\CalendarEvent;
use Modules\Retail\Entities\ProductVariant;
use Modules\Retail\Entities\BusinessSetting;
use Modules\Automotive\Entities\ProductAutomotive;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;

    protected $fillable = [
        'uuid',
        'business_id',
        'user_id',
        'product_category_id',
        'external_id',
        'name',
        'type',
        'description',
        'price',
        'sku',
        'stock',
        'weight',
        'weight_unit',
        'status',
        'previous_status',
        'package_count',
        'available_items',
        'available_item',
        'discount_type',
        'user_id',
        'discount_price',
        'discount_start_date',
        'discount_end_date',
        'tax_type',
        'tax_percentage',
        'stock_status',
        'discount_value',
        'type',
        'tags',
        'is_featured',
        'is_flag',
        'is_hide',
        'is_deliverable',
        'is_commentable',
        'cryptocurrency_accepted',
        'date_of_birth',
        'date_of_death',
        'price_type',
        'pickup_location',
        'created_at',
        'max_price',
        'public_profile_id',
        'is_shareable',
        'is_repostable'

    ];
    protected $appends = ['views_order','is_reposted'];

    public function getViewsOrderAttribute()
    {
        $user = auth('sanctum')->user();
        return $this->views()->where('user_id', $user?->id)->orderBy('updated_at', 'desc')->first()?->updated_at;
    }

    // protected $casts = [
    //     'is_featured' => 'boolean',
    //     'is_deliverable' => 'boolean',
    //     'is_commentable' => 'boolean',
    //     'cryptocurrency_accepted' => 'boolean',
    // ];

    const COMMENTABLE = 1;
    const IS_FEATURED = 1;
    const ACTIVE = 'active';
    const INACTIVE = 0;
    const MODULE_MARKETPLACE = 'marketplace';
    protected $dates = ['discount_start_date', 'discount_end_date'];

    public function statusChanger()
    {
        ActiveInactiveProducts::activeInActiveProduct($this);
        return $this;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        /**
         * Handle the product "creating" event.
         *
         * @return void
         */
        static::creating(function (Product $product) {
            $product->uuid = Str::uuid();
            $businessSettings = BusinessSetting::where('business_id', $product->business_id)->where('key', 'deliverable')->first();
            if ($businessSettings) {
                $product->is_deliverable = request()->has('is_deliverable') ? request()->is_deliverable : $businessSettings->value;
            }
        });

        /**
         * Handle the product "created" event.
         *
         * @return void
         */
        static::created(function (Product $product) {
            $productImage = config()->get('image.media.product');
            $product->stock_status = request()->input('stock') > 0 ? 'in_stock' : 'out_of_stock';
            switch (ModuleSessionManager::getModule()) {
                case 'retail':
                    // creating tags from product title
                    ProductTitleTags::createTag($product);
                    // assinging basic tags to product
                    $levelTwoTags = request()->level_two_tag
                        ? array_map('intval', explode(',', request()->level_two_tag))
                        : $product->business->standardTags()->whereHas('levelTwo')->pluck('id')->toArray();
                    $levelThreeCount = $product->business->standardTags()->whereHas('levelThree')
                        ->get()->count();
                    $levelThreeTags = [];
                    if (request()->level_three_tag) {
                        $levelThreeTags = array_map('intval', explode(',', request()->level_three_tag));
                    } else {
                        if ($levelThreeCount > 1) {
                            $levelThreeTags = $product->business->standardTags()
                                ->where('name', $product->type)->pluck('id')->toArray();
                        } else {
                            $levelThreeTags = $product->business->standardTags()->pluck('id')
                                ->toArray();
                        }
                    }
                    $moduleTags = $product->business->standardTags()->where('type', 'module')
                        ->pluck('id')->toArray();
                    $levelFourTags = (ModuleSessionManager::getModule() == 'retail' && request()->input('frontendFlag'))
                        ? (request()->filled('level_four_tags') ? array_map('strval', explode(',', request()->level_four_tags)) : [])
                        : (Str::contains(Route::currentRouteName(), 'automotive') || request()->input('frontendFlag')
                            ? (request()->filled('level_four_tags') ? [request()->level_four_tags] : [])
                            : (request()->filled('level_four_tags') && count(request()->level_four_tags) > 0
                                ? Arr::pluck(request()->level_four_tags, 'id')
                                : []
                            ));

                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;

                case 'automotive':
                    $productImage = config()->get('automotive.media.vehicle');
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'automotive')->where('type', 'module')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    if (session('performAction')) {
                        // perform action when needed
                        $levelIds = [];
                        $requestHierarchies = is_array(request()->hierarchies) ? request()->hierarchies : json_decode(request()->hierarchies, true);
                        foreach ($requestHierarchies as $item) {
                            if (!is_null($item['level_two_tag']) && !is_null($item['level_three_tag']) && !is_null($item['level_four_tags'])) {
                                // $levelIds[] = $item['level_two_tag'];
                                // $levelIds[] = $item['level_three_tag'];
                                // $levelIds[] = $item['level_four_tags'];
                                $hierarchy = TagHierarchy::where('L1', $moduleTags[0])->where('L2', $item['level_two_tag'])
                                    ->where('L3', $item['level_three_tag'])->whereRelation('standardTags', 'id', $item['level_four_tags'])->first();
                                $hierarchyData[$item['level_two_tag']] = ['hierarchy_id' => $hierarchy->id];
                                $hierarchyData[$item['level_three_tag']] = ['hierarchy_id' => $hierarchy->id];

                                $hierarchyData[$item['level_four_tags']] = ['hierarchy_id' => $hierarchy->id];
                                $product->standardTags()->attach($hierarchyData);
                                $hierarchyid  = $hierarchy->id;
                            }
                        }
                    }
                    $product->standardTags()->syncWithoutDetaching($moduleTags);
                    break;
                case 'boats':
                    $productImage = config()->get('boats.media.boat');
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'boats')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $bodyStyle = request()->input('body_style')
                        ? array_map('intval', explode(',', request()->body_style)) : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'news':
                    $productImage = config()->get('news.media.news');
                    $moduleTag = StandardTag::where('slug', 'news')->where('type', 'module')
                        ->pluck('id')->toArray();
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'obituaries':
                    $productImage = config()->get('obituaries.media.obituaries');
                    $moduleTag = StandardTag::where('slug', 'obituaries')->where('type', 'module')
                        ->pluck('id')->toArray();
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'posts':
                    $productImage = config()->get('posts.media.posts');
                    $moduleTag = StandardTag::where('slug', 'posts')->where('type', 'module')
                        ->pluck('id')->toArray();
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'blogs':
                    $productImage = config()->get('blogs.media.blog');
                    $moduleTag = StandardTag::where('slug', 'blogs')->where('type', 'module')
                        ->pluck('id')->toArray();
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'recipes':
                    $productImage = config()->get('recipes.media.recipes');
                    $moduleTag = StandardTag::where('slug', 'recipes')->where('type', 'module')
                        ->pluck('id')->toArray();
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'services':
                    $productImage = config()->get('services-module.media.services');
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'services')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'marketplace':
                    $productImage = config()->get('classifieds.media.classified');
                    $moduleTag = StandardTag::where('slug', 'marketplace')->where('type', 'module')
                        ->pluck('id')->toArray();
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'government':
                    $productImage = config()->get('government.media.posts');
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'government')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'taskers':
                    $moduleTag = StandardTag::where('slug', 'taskers')->where('type', 'module')
                        ->pluck('id')->toArray();
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'employment':
                    $productImage = config()->get('employment.media.posts');
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'employment')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'notices':
                    $productImage = config()->get('notices.media.notice');
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'notices')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'real-estate':
                    $productImage = config()->get('realestate.media.property');
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'real-estate')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
                case 'events':
                    $productImage = config()->get('events.media.events');
                    $moduleTag = StandardTag::where('slug', 'events')->where('type', 'module')
                        ->pluck('id')->toArray();
                    $levelTwoTags = request()->input('level_two_tag')
                        ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                    $levelThreeTags = request()->input('level_three_tag')
                        ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                    $levelFourTags = request()->filled('level_four_tags')
                        ? [request()->level_four_tags] : [];
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag, $levelTwoTags, $levelThreeTags, $levelFourTags]));
                    break;
            }

            // product image section
            if (request()->image) {
                $width = $productImage['width'];
                $height = $productImage['height'];
                $extension = request()->image->extension();
                $filePath = saveResizeImage(request()->image, "products", $width, $height, $extension);

                $product->media()->create([
                    'path' => $filePath,
                    'size' => request()->file('image')->getSize(),
                    'mime_type' => request()->file('image')->extension(),
                    'type' => 'image'
                ]);
            }

            // Checking tags level
            if (ModuleSessionManager::getModule() == 'automotive' || ModuleSessionManager::getModule() == 'boats') {
                if (session('performAction')) {
                    ProductTagsLevelManager::checkProductTagsLevel($product);
                    ProductTagsLevelManager::priorityOneTags($product);
                    ProductTagsLevelManager::priorityTwoTags($product);
                    ProductTagsLevelManager::priorityThree($product);
                    ProductTagsLevelManager::priorityFour($product);
                }
            } else {
                ProductTagsLevelManager::checkProductTagsLevel($product);
                ProductTagsLevelManager::priorityOneTags($product);
                ProductTagsLevelManager::priorityTwoTags($product);
                ProductTagsLevelManager::priorityThree($product);
                ProductTagsLevelManager::priorityFour($product);
            }
            $moduleSlug = ModuleSessionManager::getModule();
            // $product->getProductCategory($moduleSlug, $product);
            if (!request()->input('frontendFlag')) {
                SyncMyCategoryProducts::syncProductToCategory($moduleSlug, $product);
            }
            // removing module session
            ModuleSessionManager::removeModule();
        });

        /**
         * Handle the product "updating" event.
         *
         * @return void
         */
        static::updating(function (Product $product) {
            //Detaching coupon from product if the value of product decreases from the value of coupon.
            if ($product->coupons->count() > 0) {
                foreach ($product->coupons as $coupon) {
                    if ($coupon->discount_type == "fixed") {
                        if ($product->price < $coupon->discount_value) {
                            $product->coupons()->detach($coupon->id);
                        }
                    }
                }
            }
            $product->stock_status = $product?->stock > 0 || $product?->stock == -1 ? 'in_stock' : 'out_of_stock';
            switch (ModuleSessionManager::getModule()) {
                case 'retail':
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'retail')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? (request()->input('frontendFlag')
                                ? array_map('strval', explode(',', request()->input('level_four_tags')))
                                : Arr::pluck(request()->input('level_four_tags'), 'id'))
                            : [];
                        $bodyStyle = request()->input('body_style')
                            ? array_map('intval', explode(',', request()->body_style)) : [];

                        $levelFourTagIds = null;
                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'retail')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        if (request()->input('body_style')) {
                            $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags') && request()->input('body_style')
                                ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags, $bodyStyle) : [];
                        } else {

                            $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                                ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        }

                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags, $bodyStyle]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags]));
                    break;

                case 'automotive':
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'automotive')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    if (session('performAction')) {
                        $removeTags = [];
                        // perform action when needed
                        $hierarchyid = null;

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'automotive')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();
                        $product->standardTags()->detach($prevStandardTagIds);
                        $requestHierarchies = is_array(request()->hierarchies) ? request()->hierarchies : json_decode(request()->hierarchies, true);
                        foreach ($requestHierarchies as $item) {
                            if (!is_null($item['level_two_tag']) && !is_null($item['level_three_tag']) && !is_null($item['level_four_tags'])) {
                                $hierarchy = TagHierarchy::where('L1', $moduleTags[0])->where('L2', $item['level_two_tag'])
                                    ->where('L3', $item['level_three_tag'])->whereRelation('standardTags', 'id', $item['level_four_tags'])->first();
                                if (!$hierarchyid || $hierarchyid != $hierarchy->id) {
                                    $hierarchyData[$item['level_two_tag']] = ['hierarchy_id' => $hierarchy->id];
                                    $hierarchyData[$item['level_three_tag']] = ['hierarchy_id' => $hierarchy->id];

                                    $hierarchyData[$item['level_four_tags']] = ['hierarchy_id' => $hierarchy->id];
                                    $product->standardTags()->attach($hierarchyData);

                                    $hierarchyid  = $hierarchy->id;
                                }
                            }
                        }

                        $newProductTags = $product->standardTags()->where('slug', '<>', 'automotive')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = count($newProductTags) > 0 ? array_diff($prevStandardTagIds, $newProductTags) : [];
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags]));
                    break;

                case 'boats':
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'boats')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];
                        $bodyStyle = request()->input('body_style')
                            ? array_map('intval', explode(',', request()->body_style)) : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'automotive')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        if (request()->input('body_style')) {
                            $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags') && request()->input('body_style')
                                ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags, $bodyStyle) : [];
                        } else {
                            $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                                ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        }
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags]));
                    break;
                case 'news':
                    $moduleTag = StandardTag::where('slug', 'news')->where('type', 'module')
                        ->pluck('id')->toArray();
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'news')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag]));
                    break;
                case 'obituaries':
                    $moduleTag = StandardTag::where('slug', 'obituaries')->where('type', 'module')
                        ->pluck('id')->toArray();
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'obituaries')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag]));
                    break;
                case 'posts':
                    $moduleTag = StandardTag::where('slug', 'posts')->where('type', 'module')
                        ->pluck('id')->toArray();
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'posts')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag]));
                    break;
                case 'blogs':
                    $moduleTag = StandardTag::where('slug', 'blogs')->where('type', 'module')
                        ->pluck('id')->toArray();
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'blogs')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag]));
                    break;
                case 'recipes':
                    $moduleTag = StandardTag::where('slug', 'recipes')->where('type', 'module')
                        ->pluck('id')->toArray();
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'obituaries')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag]));
                    break;
                case 'services':
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'services')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'automotive')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags]));
                    break;
                case 'marketplace':
                    $moduleTag = StandardTag::where('slug', 'marketplace')->where('type', 'module')
                        ->pluck('id')->toArray();
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'marketplace')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag]));
                    break;

                case 'government':
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'government')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'government')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags]));
                    break;
                case 'taskers':
                    $moduleTag = StandardTag::where('slug', 'taskers')->where('type', 'module')
                        ->pluck('id')->toArray();
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'taskers')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag]));
                    break;
                case 'employment':
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'employment')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'employment')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags]));
                    break;
                case 'notices':
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'notices')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'automotive')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags]));
                    break;
                case 'real-estate':
                    if (request()->input('frontendFlag')) {
                        $moduleTags = StandardTag::where('slug', 'real-estate')->pluck('id')->toArray();
                    } else {
                        $moduleTags = $product->business->standardTags()->where('type', 'module')
                            ->pluck('id')->toArray();
                    }
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'automotive')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTags]));
                    break;
                case 'events':
                    $moduleTag = StandardTag::where('slug', 'events')->where('type', 'module')
                        ->pluck('id')->toArray();
                    if (session('performAction')) {
                        $levelTwoTags = request()->input('level_two_tag')
                            ? array_map('intval', explode(',', request()->level_two_tag)) : [];
                        $levelThreeTags = request()->input('level_three_tag')
                            ? array_map('intval', explode(',', request()->level_three_tag)) : [];
                        $levelFourTags = request()->filled('level_four_tags')
                            ? [request()->level_four_tags] : [];

                        $prevStandardTagIds = $product->standardTags()->where('slug', '<>', 'events')
                            ->where(function ($query) {
                                $query->whereHas('levelTwo')->orWhereHas('levelThree')->orwhereHas('tagHierarchies');
                            })->pluck('id')->toArray();

                        $removeTags = request()->input('level_two_tag') && request()->input('level_three_tag') && request()->input('level_four_tags')
                            ? array_diff($prevStandardTagIds, $levelFourTags, $levelThreeTags, $levelTwoTags) : [];
                        $product->standardTags()->detach($removeTags);
                        $product->standardTags()->syncWithoutDetaching(Arr::collapse([$levelTwoTags, $levelThreeTags, $levelFourTags]));
                    } else {
                        $removeTags = [];
                    }
                    $product->standardTags()->syncWithoutDetaching(Arr::collapse([$moduleTag]));
                    break;
            }
            $moduleSlug = ModuleSessionManager::getModule();
            if (!request()->input('frontendFlag')) {
                SyncMyCategoryProducts::syncProductToCategory($moduleSlug, $product);
            }
            // $product->getProductCategory($moduleSlug, $product, $levelTwoTags, $levelThreeTags, $levelFourTags);
            ProductTagsLevelManager::priorityOneTags($product, $removeTags, 'update');
        });

        /**
         * Handle the product "updated" event.
         *
         * @return void
         */
        static::updated(function (Product $product) {
            $image = config()->get('image.media.product');
            switch (ModuleSessionManager::getModule()) {
                case 'automotive':
                    $image = config()->get('automotive.media.vehicle');
                    break;
                case 'boats':
                    $image = config()->get('boats.media.boat');
                    break;
                case 'news':
                    $image = config()->get('news.media.news');
                    break;
                case 'obituaries':
                    $image = config()->get('obituaries.media.obituaries');
                    break;
                case 'posts':
                    $image = config()->get('posts.media.posts');
                    break;
                case 'blogs':
                    $image = config()->get('blogs.media.blog');
                    break;
                case 'recipes':
                    $image = config()->get('recipes.media.recipes');
                    break;
                case 'services':
                    $image = config()->get('services-module.media.services');
                    break;
                case 'marketplace':
                    $image = config()->get('classifieds.media.classified');
                    break;
                case 'government':
                    $image = config()->get('government.media.posts');
                    break;
                case 'employment':
                    $image = config()->get('employment.media.posts');
                    break;
                case 'notices':
                    $image = config()->get('notices.media.notice');
                    break;
                case 'real-estate':
                    $image = config()->get('real-estate.media.property');
                    break;
                case 'events':
                    event(new UpdateBookedEvents($product, request()->event_date));
                    $image = config()->get('events.media.events');
                    break;
            }

            if (request()->input('image_id') && request()->file('image')) {
                $width = $image['width'];
                $height = $image['height'];
                $extension = request()->file('image')->extension();
                $filePath = saveResizeImage(request()->image, "products", $width, $height,  $extension);
                if (request()->input('image_id') != 0) {
                    $media = Media::findOrFail(request()->input('image_id'));
                    \deleteFile($media->path);
                    $media->update([
                        'path' => $filePath,
                        'size' => request()->file('image')->getSize(),
                        'mime_type' => $extension,
                        'type' => 'image',
                        'is_external' => \false
                    ]);
                } else {
                    $media = $product->media()->create([
                        'path' => $filePath,
                        'size' => request()->file('image')->getSize(),
                        'mime_type' => $extension,
                        'type' => 'image'
                    ]);
                }
            }
            ///////
            if ($product->discount_value) {
                ApplyDiscountOnVariants::variantDiscount($product);
            }
            if ($product->isDirty('name')) {
                ProductTagsLevelManager::priorityTwoTags($product);
            }
            // Checking tags level
            ProductTagsLevelManager::checkProductTagsLevel($product);
        });

        /**
         * Handle the Product "deleting" event.
         *
         * @param  \App\Models\Product  $product
         * @return void
         */
        static::deleting(function (Product $product) {
            //deleting media of a product like main and secondary images
            $media = $product->media()->get();
            foreach ($media as $key => $media) {
                if ($media) {
                    deleteFile($media->path);
                    $media->delete();
                }
            }
            //deleting images of variants of products
            $variants = $product->variants()->get();
            foreach ($variants as $key => $variant) {
                if ($variant && $variant->image) {
                    deleteFile($variant->image->path);
                    $variant->image()->delete();
                }
            }
            ProductTitleTags::deleteTag($product);
            //delete product vehicles
            $product->vehicle()->delete();
        });
        static::deleted(function (Product $product) {
            $moduleSlug = ModuleSessionManager::getModule();
            $product->dreamCars()->detach();
            ProductPriorityManager::removePriority($product->id);
        });
    }



    /**
     * |=====================================================================
     * | Mutators & Accessors
     * |=====================================================================
     */

    /**
     * Set Date.
     *
     * @param  string  $value
     * @return string
     */

    public function getProductCategory($module, $product, $levelTwoTag = null, $levelThreeTag = null, $levelFourTag = null, $detach = false)
    {
        $moduleTag = StandardTag::where('slug', $module)->where('type', 'module')->value('id');
        $levelTwoTag = $levelTwoTag ?? $product->productLevelTwo()->value('id');
        $levelThreeTag = $levelThreeTag ?? $product->productLevelThree()->value('id');
        $levelFourTag = $levelFourTag ?? $product->productLevelFour()->value('id');

        $dreamCars = DreamCar::where([
            ['module_id', $moduleTag],
            ['make_id', $levelTwoTag],
            ['model_id', $levelThreeTag],
            ['level_four_tag_id', $levelFourTag]
        ])->get();
        if ($dreamCars->isNotEmpty()) {
            foreach ($dreamCars as $dreamCar) {
                $detach ? $dreamCar->products()->detach($product->id) : $dreamCar->products()->syncWithoutDetaching([$product->id]);
            }
        } else {
            $product->dreamCars()->detach();
        }
    }


    public function setDiscountStartDateAttribute($value)
    {
        $this->attributes['discount_start_date'] = setDateValues($value);
    }

    public function setDiscountEndDateAttribute($value)
    {
        $this->attributes['discount_end_date'] = setDateValues($value);
    }

    public function setIsFeaturedAttribute($value)
    {
        $this->attributes['is_featured'] = (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    public function setIsDeliverableAttribute($value)
    {
        $this->attributes['is_deliverable'] = (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    public function setIsCommentableAttribute($value)
    {
        $this->attributes['is_commentable'] = (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    public function setCryptocurrencyAcceptedAttribute($value)
    {
        $this->attributes['cryptocurrency_accepted'] = (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }



    /**
     * |=====================================================================
     * | Scope Queries
     * |=====================================================================
     */

    public function scopeWithoutHiddenProducts($query)
    {
        return $query->whereDoesntHave('inappropriateProducts', function ($subQuery) {
            $subQuery->where('user_id', auth('sanctum')->id());
        });
    }

    public function scopeModuleBasedProducts($query, $moduleId)
    {
        $query->whereHas('standardTags', function ($subQuery) use ($moduleId) {
            $subQuery->where('id', $moduleId)->orWhere('slug', $moduleId);
        })->get();
    }

    public function scopeActive($query)
    {
        return $query->whereStatus('active')->where(function ($subQuery) {
            $subQuery->whereHas('business', function ($subQuery) {
                $subQuery->active();
            })->orWhereHas('user', function ($query) {
                $query->where('status', 'active');
            });
        });
    }

    public function scopeCommentable($query)
    {
        $query->where('is_commentable', self::COMMENTABLE);
    }

    public function scopeMatchOrphanTags($query, $keyword)
    {
        $query->whereHas('priority', function ($subQuery) use ($keyword) {
            $subQuery->whereRaw("MATCH (P1) AGAINST ('$keyword' IN BOOLEAN MODE)")
                ->orWhereRaw("MATCH (P2) AGAINST ('$keyword' IN BOOLEAN MODE)")
                ->orWhereRaw("MATCH (P3) AGAINST ('$keyword' IN BOOLEAN MODE)")
                ->orWhereRaw("MATCH (P4) AGAINST ('$keyword' IN BOOLEAN MODE)");
        });
    }

    public function scopeMatchAgainstWithPriority($query, $keyword)
    {
        $query->select(
            'products.*',
            DB::raw("MATCH (P1) AGAINST ('$keyword' IN BOOLEAN MODE) * 1000 as first_priority"),
            DB::raw("MATCH (P2) AGAINST ('$keyword' IN BOOLEAN MODE) * 100 as second_priority"),
            DB::raw("MATCH (P3) AGAINST ('$keyword' IN BOOLEAN MODE) * 10 as third_priority"),
            DB::raw("MATCH (P4) AGAINST ('$keyword' IN BOOLEAN MODE) * 1 as fourth_priority"),
        )
            // ->where(function ($subQuery) use ($keyword) {
            //     $subQuery->whereRaw("MATCH (P1) AGAINST ('$keyword' IN BOOLEAN MODE)")
            //         ->orWhereRaw("MATCH (P2) AGAINST ('$keyword' IN BOOLEAN MODE)")
            //         ->orWhereRaw("MATCH (P3) AGAINST ('$keyword' IN BOOLEAN MODE)")
            //         ->orWhereRaw("MATCH (P4) AGAINST ('$keyword' IN BOOLEAN MODE)");
            // })
            ->join('product_priorities', 'product_priorities.product_id', '=', 'products.id')
            ->orderByRaw('(first_priority + second_priority + third_priority + fourth_priority) desc');
    }

    public function scopeWhereStandardTag($query, $tagId)
    {
        $query->where(function ($subQuery) use ($tagId) {
            $subQuery->whereHas('business.standardTags', function ($query) use ($tagId) {
                $query->where('id', $tagId)->orWhere('slug', $tagId);
            })->orWhereHas('standardTags', function ($query) use ($tagId) {
                $query->where('id', $tagId)->orWhere('slug', $tagId);
            });
        });
    }

    public function scopeHierarchyBasedProducts($query, $search)
    {
        return $query->active()
            ->whereHas('standardTags', function ($subQuery) use ($search) {
                $subQuery->whereIn('id', [
                    $search->level_one_id,
                    $search->level_two_id,
                    $search->level_three_id,
                    $search->level_four_id,
                ])->select('*', DB::raw('count(*) as total'))
                    ->having('total', '>=', 4);
            });
    }
    public function scopeWhereEventDateNotPassed($query)
    {
        return $query->whereRelation('events', 'event_date', '>=', Carbon::now());
    }
    public function getIsRepostedAttribute()
    {
        return true;
    }


    // public function scopeWithoutHiddenProducts($query) {
    //     $hiddenProducts = InappropriateProduct::where('user_id', auth('sanctum')->id())->pluck('model_id');
    //     $query->whereNotIn('id', $hiddenProducts);
    // }

    public function scopeSearch($query, $keywords)
    {
        return $query->where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%')
                        ->orWhereHas('standardTags', function ($subQuery) use ($keyword) {
                            $subQuery->where('name', $keyword);
                        })->orWhere('description', 'like', '%' . $keyword . '%');
                });
            }
        });
    }


    /**
     * |=====================================================================
     * | Relationships
     * |=====================================================================
     */

    /**
     * Get all of the product media.
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function mainImage()
    {
        return $this->morphOne(Media::class, 'model')->where('type', 'image');
    }

    public function secondaryImages()
    {
        return $this->morphMany(Media::class, 'model')->where('type', 'image')->skip(1)->take(6);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class)->withPivot('status', 'previous_status');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function standardTags()
    {
        return $this->belongsToMany(StandardTag::class, 'product_standard_tag')->withPivot('attribute_id', 'hierarchy_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'model');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function priority()
    {
        return $this->hasOne(ProductPriority::class);
    }

    public function wishList()
    {
        return $this->morphMany(Wishlist::class, 'model');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'model');
    }

    public function vehicle()
    {
        return $this->hasOne(ProductAutomotive::class, 'product_id');
    }

    public function events()
    {
        return $this->hasOne(Event::class, 'product_id');
    }

    public function boat()
    {
        return $this->hasOne(ProductAutomotive::class, 'product_id');
    }

    public function ignoredTags()
    {
        return $this->hasOne(ProductIgnoredTags::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function publicProfile()
    {
        return $this->belongsTo(PublicProfile::class, 'public_profile_id');
    }

    public function reposts()
    {
        return $this->belongsToMany(PublicProfile::class, 'product_public_profile')->withPivot('user_id')->withTimestamps();
    }



    public function comments()
    {
        return $this->morphMany(Comment::class, 'model');
    }

    public function inappropriateProducts()
    {
        return $this->morphMany(InappropriateProduct::class, 'model');
    }

    public function views()
    {
        return $this->hasMany(ProductViews::class);
    }

    public function headline()
    {
        return $this->hasOne(HeadlineSetting::class);
    }

    public function bookings()
    {
        return $this->hasMany(EventBooking::class, 'product_id');
    }

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'product_id');
    }

    // to load product level one
    public function productLevelOne()
    {
        return $this->standardTags()->whereHas('levelOne');
    }

    // to load product level two
    public function productLevelTwo()
    {
        return $this->standardTags()->whereHas('levelTwo');
    }

    // to load product level three
    public function productLevelThree()
    {
        return $this->standardTags()->whereHas('levelThree');
    }
    // to load product level four
    public function productLevelFour()
    {
        return $this->standardTags()->whereHas('tagHierarchies');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'product_id');
    }

    public function dreamCars()
    {
        return $this->belongsToMany(DreamCar::class, 'dream_car_product', 'product_id', 'dream_car_id');
    }
}
