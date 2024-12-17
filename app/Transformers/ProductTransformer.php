<?php

namespace App\Transformers;

use stdClass;
use Carbon\Carbon;
use App\Models\Like;
use App\Models\User;
use Modules\News\Entities\Comment;
use App\Models\Wishlist;
use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use App\Models\InappropriateProduct;
use Modules\Automotive\Entities\VehicleReview;
use Modules\Retail\Entities\ProductVariant;

class ProductTransformer extends Transformer
{
    public function transform($product, $options = null)
    {
        $date = Carbon::now()->format('Y-m-d');
        $review = null;
        $levelThreeTag = '';
        $levelOneTag = '';
        $levelTwoTag = '';
        $discount_start_date = $product?->discount_start_date ? $product?->discount_start_date->format('Y-m-d') : '';
        $discount_end_date = $product?->discount_end_date ? $product?->discount_end_date->format('Y-m-d') : '';
        $busienssOptions = [
            'withLevelThreeTags' => isset($options['businessLevelThreeTags']) && $options['businessLevelThreeTags'],
        ];

        $levelOneTag = $product->standardTags()->whereHas('levelOne')->first();
        $productLevelFourTag = ($levelOneTag->slug == 'retail') ? [] : '';
        $levelTwoTag = $product->standardTags()->whereHas('levelTwo', function ($query) use ($levelOneTag) {
            $query->where('L1', $levelOneTag->id);
        })->first();
        if ($levelTwoTag) {
            $levelThreeTag = $product->standardTags()->whereHas('levelThree', function ($query) use ($levelOneTag, $levelTwoTag) {
                $query->where('L1', $levelOneTag->id)->where('L2', $levelTwoTag->id);
            })->first();

            if ($levelThreeTag) {
                $productLevelFourTagQuery = StandardTag::whereHas('productTags', function ($query) use ($levelOneTag, $levelTwoTag, $levelThreeTag, $product) {
                    $query->where('product_id', $product->id)->whereHas('standardTags', function ($subQuery) use ($levelOneTag, $levelTwoTag, $levelThreeTag) {
                        if ($levelOneTag && $levelTwoTag && $levelThreeTag) {
                            $subQuery->whereIn('id', [$levelOneTag->id, $levelTwoTag->id, $levelThreeTag->id]);
                        }
                    });
                })->whereHas('tagHierarchies', function ($query) use ($levelOneTag, $levelTwoTag, $levelThreeTag) {
                    $query->where('L1', $levelOneTag->id)->where('L2', $levelTwoTag->id)->where('L3', $levelThreeTag->id);
                    $query->where('level_type', 4);
                });
                $productLevelFourTag = ($levelOneTag->slug == 'retail')
                    ? $productLevelFourTagQuery->get()
                    : $productLevelFourTagQuery->first();
                $review = $this->calculateReviewsAverage($product, $levelOneTag, $levelTwoTag, $levelThreeTag);
            }
        }
        $variantsData = $this->getVariantColorsAndSizes($product->id);
        if (isset($options['withMinimumData']) && $options['withMinimumData']) {
            if ($product->relationLoaded('vehicle')) {
                $busienssOptions['module'] = $levelOneTag->slug;
                $data = [
                    'id' => $product->id,
                    'uuid' => (string) $product->uuid,
                    'name' => (string) $levelOneTag->slug != 'notices' ? $product->name : '',
                    'main_image' => $product->mainImage
                        ? getImage($product->mainImage->path, 'image', $product->mainImage->is_external)
                        : getImage(NULL, 'image'),
                    'discount_price' => $product?->discount_price && $discount_start_date <= $date && $discount_end_date >= $date ? numberFormat($product?->discount_price) : '',
                    'discount_type' => $product->discount_type ?  $product->discount_type : '',
                    'discount_value' => $product->discount_value ?  $product->discount_value : '',
                    'in_wishlist' => $this->productsExistsInWishlist($product->id),
                    'in_liked' => $this->productsExistsInlike($product->id),
                    'reviews_avg' => $review,
                    'stock_status' => (string) $product->stock_status,
                    'price' => $this->getPriceFormat($product->price, $levelOneTag),
                    'max_price' => $this->getPriceFormat($product->max_price, $levelOneTag),
                    'price_type' => $product->price_type,
                    'type' => $product->type,
                    'status' => $product?->status,
                    'level_two_tag' => $levelTwoTag,
                    'level_three_tag' => $levelThreeTag,
                    'level_four_tag' => $productLevelFourTag,
                    'comments_count' => (int) $product?->comments_count,
                    'created_at' => convertDate($product?->created_at, 'M d, Y'),
                    'description' => in_array($levelOneTag->slug, ['notices', 'government', 'posts']) ? $product->description : '',
                    'colors' => $variantsData['colors'],
                    'sizes' => $variantsData['sizes'],
                ];
                if ($product->business) {
                    $data['business'] = (new BusinessTransformer)->transform($product->business,  $busienssOptions);
                } else if ($product->user) {
                    $data['user'] = (new UserTransformer)->transform($product->user);
                }

                if ($product?->type) {
                    $data['type'] = $product->type;
                }

                if (in_array($levelOneTag->slug, ['real-estate'])) {
                    //  display attributes
                    $data['attributes'] = $this->getAttributes($product);
                    $userOptions = [
                        'levelOneTag' => $levelOneTag->id,
                        'withAddress' => request()->input('with_user_address') ? true : false
                    ];
                    $data['user'] = $product->user ?  (new UserTransformer)->transform($product->user, $userOptions) : '';
                }
            } else if ($product->relationLoaded('user')) {
                $options = [
                    'levelOneTag' => $levelOneTag->id,
                    'withAddress' => request()->input('with_user_address') ? true : false
                ];
                $data = [
                    'id' => $product->id,
                    'uuid' => (string) $product?->uuid,
                    'name' => (string) $levelOneTag->slug != 'notices' ? $product->name : '',
                    'stock' => (int) $product?->stock,
                    'description' => in_array($levelOneTag->slug, ['notices', 'government', 'posts']) ? $product->description : '',
                    'main_image' => $product->mainImage
                        ? getImage($product->mainImage->path, 'image', $product->mainImage->is_external)
                        : (in_array($levelOneTag->slug, ['posts']) ? '' : getImage(NULL, 'image')),
                    'in_wishlist' => $this->productsExistsInWishlist($product->id),
                    'in_liked' => $this->productsExistsInlike($product->id),
                    'reviews_avg' => (float) $product?->reviews()?->avg('rating'),
                    'date_of_birth' => (string) $product?->date_of_birth,
                    'date_of_death' => (string) $product?->date_of_death,
                    'user' => (new UserTransformer)->transform($product->user, $options),
                    'user_like'=>$product->likes->pluck('user'),
                    'comment_count'=>(int) $product->comments->count(),
                    'events' => $product->events,
                    'condition' => $product?->standardTags()->where('type', 'attribute')->first(),
                    'weight' => (int) $product?->weight,
                    'weight_unit' => $product?->weight_unit,
                    'is_featured' => (bool) $product?->is_featured,
                    'is_deliverable' => (bool) $product?->is_deliverable,
                    'is_commentable' => (bool) $product?->is_commentable,
                    'is_shareable'=>(bool) $product->is_shareable,
                    'is_repostable'=>(bool) $product->is_repostable,
                    'comments_count' => (int) $product?->comments_count,
                    'cryptocurrency_accepted' => (bool) $product?->cryptocurrency_accepted,
                    'package_count' => $product?->package_count,
                    'status' => $product?->status,
                    'price' => $this->getPriceFormat($product->price, $levelOneTag),
                    'max_price' => $this->getPriceFormat($product->max_price, $levelOneTag),
                    'price_type' => $product?->price_type,
                    'level_two_tag' => $levelTwoTag,
                    'level_three_tag' => $levelThreeTag,
                    'level_four_tag' => $productLevelFourTag,
                    'time_since_creation' => $product->created_at ? $this->calculatePostCReationTime($product->created_at) : null,
                    'created_at' => convertDate($product?->created_at, 'M d, Y'),
                    'type' => $product->type,
                    'colors' => $variantsData['colors'],
                    'sizes' => $variantsData['sizes'],
                ];

                if($levelOneTag?->slug == 'posts') {
                    $data['public_profile'] = (new PublicProfileTransformer)->transform($product?->publicProfile);
                }

                if ($product?->type) {
                    $data['type'] = $product->type;
                }
            } else {
                $busienssOptions['module'] = $levelOneTag?->slug;
                $data = [
                    'id' => $product->id,
                    'uuid' => (string) $product->uuid,
                    'name' => (string) $levelOneTag->slug != 'notices' ? $product->name : '',
                    'stock' => (int) $product->stock,
                    'stock_status' => (string) $product->stock_status,
                    'discount_price' => $product?->discount_price && $discount_start_date <= $date && $discount_end_date >= $date ? numberFormat($product?->discount_price) : '',
                    'discount_type' => $product->discount_type ?  $product->discount_type : '',
                    'discount_value' => $product->discount_value ?  $product->discount_value : '',
                    'description' => in_array($levelOneTag->slug, ['notices', 'government']) ? $product->description : '',
                    'main_image' => $product->mainImage
                        ? getImage($product->mainImage->path, 'image', $product->mainImage->is_external)
                        : (in_array($levelOneTag->slug, ['government']) ? '' : getImage(NULL, 'image')),
                    'in_wishlist' => $this->productsExistsInWishlist($product->id),
                    'in_liked' => $this->productsExistsInlike($product->id),
                    'reviews_avg' => $review,
                    'price' => $this->getPriceFormat($product->price, $levelOneTag),
                    'max_price' => $this->getPriceFormat($product->max_price, $levelOneTag),
                    'price_type' => $product->price_type,
                    'level_two_tag' => $levelTwoTag,
                    'level_three_tag' => $levelThreeTag,
                    'level_four_tag' => $productLevelFourTag,
                    'comments_count' => (int) $product?->comments_count,
                    'business' => (new BusinessTransformer)->transform($product->business,  $busienssOptions),
                    'created_at' => convertDate($product?->created_at, 'M d, Y'),
                    'time_since_creation' => $product->created_at ? $this->calculatePostCReationTime($product->created_at) : null,
                    'status' => $product?->status,
                    'type' => $product->type,
                    'colors' => $variantsData['colors'],
                    'sizes' => $variantsData['sizes'],
                ];

                if (in_array($levelOneTag->slug, ['real-estate'])) {
                    //  display attributes
                    $data['attributes'] = $this->getAttributes($product);
                    $userOptions = [
                        'levelOneTag' => $levelOneTag->id,
                        'withAddress' => request()->input('with_user_address') ? true : false
                    ];
                    $data['user'] = $product->user ? (new UserTransformer)->transform($product->user, $userOptions) : null;
                }
            }
        } else {
            // $levelTwoTag = $product->standardTags()->whereHas('levelTwo')->first();
            $busienssOptions['module'] = $levelOneTag->slug;
            switch ($levelOneTag->slug) {
                case 'automotive':
                    $media = \config()->get('automotive.media.vehicle');
                    break;
                case 'boats':
                    $media = \config()->get('boats.media.boat');
                    break;
                case 'news':
                    $media = \config()->get('news.media.news');
                    break;
                case 'obituaries':
                    $media = \config()->get('obituaries.media.obituaries');
                    break;
                case 'posts':
                    $media = \config()->get('posts.media.posts');
                    break;
                case 'blogs':
                    $media = \config()->get('blogs.media.blog');
                    break;
                case 'recipes':
                    $media = \config()->get('recipes.media.recipes');
                    break;
                case 'services':
                    $media = \config()->get('services-module.media.services');
                    break;
                case 'marketplace':
                    $media = \config()->get('classifieds.media.classified');
                    break;
                case 'taskers':
                    $media = \config()->get('taskers.media.tasker');
                    break;
                case 'employment':
                    $media = \config()->get('employment.media.posts');
                    break;
                case 'government':
                    $media = \config()->get('government.media.posts');
                    break;
                case 'real-estate':
                    $media = \config()->get('realestate.media.property');
                    break;
                case 'events':
                    $media = \config()->get('events.media.events');
                    break;
                default:
                    $media = \config()->get('image.media.image');
            }
            if ($product->user) {
                $levelOneTag = $product->standardTags()->whereHas('levelOne')->first();
                $commentsQuery = Comment::where('model_id', $product->id)->where('model_type', 'App\Models\Product')->with('user')->latest()->paginate(5);  
                $comments = (new CommentTransformer)->transformCollection($commentsQuery); 
                $commentsMeta = apiPagination($commentsQuery, 5);
                $user = $product->user()->withCount(['products' => function ($query) use ($levelOneTag) {
                    $query->whereRelation('standardTags', 'id', $levelOneTag->id);
                }])->first();
                $userOptions = [
                    'withAddress' => true,
                    'levelOneTag' => $levelOneTag->id
                ];
                $data = [
                    'id' => $product->id,
                    'uuid' => (string) $product->uuid,
                    'name' => (string) $levelOneTag->slug != 'notices' ? $product->name : '',
                    'stock' => (int) $product?->stock,
                    'sku' =>  $product->sku,
                    'description' => $product->description,
                    'main_image' => $product->mainImage
                        ? getImage($product->mainImage->path, 'image', $product->mainImage->is_external)
                        : (in_array($levelOneTag->slug, ['posts']) ? '' : getImage(NULL, 'image')),
                    'main_image_id' => $product->mainImage ? $product->mainImage->id : null,
                    'created_at' => convertDate($product?->created_at, 'M d, Y'),
                    'in_wishlist' => $this->productsExistsInWishlist($product->id),
                    'in_liked' => $this->productsExistsInlike($product->id),
                    'in_flag' => $this?->productsExistsInFlag($product->id),
                    'is_hide' => $this?->productsExistsInHide($product),
                    'review_count' => (int) $product->reviews()->count(),
                    'reviews_avg' => $review,
                     'user' => (new UserTransformer)->transform($user, $userOptions),
                     'likedUsers' => $product->likes->pluck('user'),
                     'commentCount'=>$product->comments->count(),
                    'comments'=>$comments,
                    'comments_meta' => $commentsMeta,
                    'events' => $product->events,
                    'level_one_tag' => $levelOneTag ? (new StandardTagTransformer)->transform($levelOneTag) : new stdClass,
                    'media_width' => $media['width'],
                    'media_height' => $media['height'],
                    'is_featured' => (bool) $product?->is_featured,
                    'is_deliverable' => (bool) $product?->is_deliverable,
                    'is_commentable' => (bool) $product?->is_commentable,
                    'is_shareable'=>(bool) $product->is_shareable,
                    'is_repostable'=>(bool) $product->is_repostable,
                    'cryptocurrency_accepted' => (bool) $product?->cryptocurrency_accepted,
                    'date_of_birth' => (string) $product?->date_of_birth,
                    'date_of_death' => (string) $product?->date_of_death,
                    'weight' => (int) $product?->weight,
                    'weight_unit' => $product?->weight_unit,
                    'package_count' => $product?->package_count,
                    'price' => $this->getPriceFormat($product->price, $levelOneTag),
                    'max_price' => $this->getPriceFormat($product->max_price, $levelOneTag),
                    'price_type' => $product?->price_type,
                    'level_two_tag' => $levelTwoTag,
                    'level_three_tag' => $levelThreeTag,
                    'level_four_tag' => $productLevelFourTag,
                    'comments_count' => (int) $product?->comments_count,
                    'status' => $product?->status,
                    'business_id' =>  $product->business_id,
                    'pickup_location' => $product->pickup_location,
                    'colors' => $variantsData['colors'],
                    'sizes' => $variantsData['sizes'],
                ];

                if($levelOneTag?->slug == 'posts') {
                    $data['public_profile'] = (new PublicProfileTransformer)->transform($product?->publicProfile);
                }

                if (isset($options['withSecondaryImages']) && $options['withSecondaryImages']) {
                    $data['secondary_images'] = $product->secondaryImages->count() > 0
                        ? (new MediaTransformer)->transformCollection($product->secondaryImages) : [];
                }
                if ($product?->type) {
                    $data['type'] = $product->type;
                }

                // get business in detail page in case of real estate
                if (in_array($levelOneTag->slug, ['real-estate', 'government'])) {
                    $data['business'] = (new BusinessTransformer)->transform($product->business,  $busienssOptions);
                }

                // get booking for sepecific user
                if (in_array($levelOneTag?->slug, ['events'])) {
                    $data['booking'] = $this->getEventBooking($product);
                    $data['comments'] = $this->getEventComments($product);
                    $data['calendar_event'] = $this->getCalendarEvents($product);
                }
            } else {
                $data = [

                    'id' => $product->id,
                    'uuid' => (string) $product->uuid,
                    'name' => (string) $levelOneTag->slug != 'notices' ? $product->name : '',
                    'description' => $product->description,
                    'main_image' => $product->mainImage
                        ? getImage($product->mainImage->path, 'image', $product->mainImage->is_external)
                        : getImage(NULL, 'image'),
                    'main_image_id' => $product->mainImage ? $product->mainImage->id : null,
                    'stock' => (int) $product->stock,
                    'stock_status' => (string) $product->stock_status,
                    'discount_price' => $product->discount_price && $discount_start_date <= $date && $discount_end_date >= $date ? numberFormat($product->discount_price) : '',
                    'discount_type' => $product->discount_type ? $product->discount_type : '',
                    'discount_value' => $product->discount_type === 'fixed' ? number_format($product->discount_value, 2, '.', '') : ($product->discount_type === 'percentage' ? $product->discount_value : ''),
                    'is_featured' => (bool) $product->is_featured,
                    'is_deliverable' => (bool) $product->is_deliverable,
                    'is_commentable' => $product->is_commentable,
                    'in_wishlist' => $this->productsExistsInWishlist($product->id),
                    'in_liked' => $this->productsExistsInlike($product->id),
                    'in_flag' => $this?->productsExistsInFlag($product->id),
                    'is_hide' => $this?->productsExistsInHide($product),
                    'review_count' =>  (int) $product->reviews()->count(),
                    'reviews_avg' => $review,
                    'business' => (new BusinessTransformer)->transform($product->business,  $busienssOptions),
                    'level_one_tag' => $levelOneTag ? (new StandardTagTransformer)->transform($levelOneTag) : new stdClass,
                    'media_width' => $media['width'],
                    'media_height' => $media['height'],
                    'level_two_tag' => $levelTwoTag,
                    'level_three_tag' => $levelThreeTag,
                    'level_four_tag' => $productLevelFourTag,
                    'comments_count' => (int) $product?->comments_count,
                    'price' => $this->getPriceFormat($product->price, $levelOneTag),
                    'max_price' => $this->getPriceFormat($product->max_price, $levelOneTag),
                    'price_type' => $product?->price_type,
                    'status' => $product?->status,
                    'sku' => $product?->sku,
                    'weight' => $product?->weight,
                    'weight_unit' => $product?->weight_unit,
                    'pickup_location' => $product->pickup_location,
                    'created_at' => convertDate($product?->created_at, 'M d, Y'),
                    'time_since_creation' => $product->created_at ? $this->calculatePostCReationTime($product->created_at) : null,
                    'body_styles' => $product?->standardTags()->whereHas('levelTwo')->whereIn('name', \config()->get('automotive.body_styles'))->select(['id', 'name as text', 'slug'])->first(),
                    'colors' => $variantsData['colors'],
                    'sizes' => $variantsData['sizes'],
                ];

                if ($product?->type) {
                    $data['type'] = $product->type;
                }
            }
        }

        if (in_array($levelOneTag?->slug, ['government', 'posts'])) {
            $levelOneTag = $product->standardTags()->whereHas('levelOne')->first();

            $data['likes_count'] = $product->likes_count;
            $data['wish_list_count'] = $product->wish_list_count;
            $data['views_count'] = $product->views_count;
            $data['reposts_count'] = $product->reposts_count;
        }
        if ($product->relationLoaded('vehicle') && !request()->input('favoriteProducts')) {
            $levelTwoTag = $product->standardTags()->whereHas('levelTwo', function ($query) use ($levelOneTag) {
                $query->where('L1', $levelOneTag->id);
            })->first();
            $data['vehicle'] = $product->vehicle ? (new VehicleTransformer)->transform($product->vehicle) : [];
            $data['level_two_tag'] = $levelTwoTag ? (new StandardTagTransformer)->transform($levelTwoTag) : new stdClass;
            $data['level_three_tag'] = $levelThreeTag ? (new StandardTagTransformer)->transform($levelThreeTag) : new stdClass;
            $data['level_four_tag'] = ($productLevelFourTag instanceof \Illuminate\Support\Collection)
                ? ($productLevelFourTag->isNotEmpty()
                    ? $productLevelFourTag->map(fn ($tag) => (new StandardTagTransformer)->transform($tag))
                    : new stdClass)
                : ($productLevelFourTag
                    ? (new StandardTagTransformer)->transform($productLevelFourTag)
                    : new stdClass);
            if (isset($options['withSecondaryImages']) && $options['withSecondaryImages']) {
                $data['secondary_images'] = $product->secondaryImages->count() > 0
                    ? (new MediaTransformer)->transformCollection($product->secondaryImages()->get()) : [];
            }

            if (isset($options['withVariants']) && Arr::exists($options, 'withVariants') && $options['withVariants']) {
                $data['variants'] = count($product->variants) > 0
                    ? (new ProductVariantTransformer)->transformCollection($product->variants()->activeAndInStock()->get(), $options)
                    : [];
            }
        }

        if ($product->relationLoaded('events')) {
            $data['event'] = $product?->events ? (new EventTransformer)->transform($product?->events) : new stdClass;
        }
        return $data;
    }

    private function productsExistsInWishlist($id)
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $wishlist = Wishlist::where('user_id', $user->id)->where('model_id', $id)->where('model_type', 'App\Models\Product')->first();
            return $wishlist ? true : false;
        } else {
            return false;
        }
    }

    private function productsExistsInlike($id)
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $wishlist = Like::where('user_id', $user->id)->where('model_id', $id)->where('model_type', 'App\Models\Product')->first();
            return $wishlist ? true : false;
        } else {
            return false;
        }
    }

    private function productsExistsInFlag($id)
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $flag = InappropriateProduct::where('user_id', $user->id)->where('model_id', $id)->where('model_type', 'App\Models\Product')->where('type', 'flag')->first();
            return $flag ? true : false;
        } else {
            return false;
        }
    }

    private function productsExistsInHide($product)
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $hide = $user->inappropriateProduct()->where('model_id', $product->id)->where('model_type', 'App\Models\Product')->where('type', 'hide')->first();
            return $hide ? true : false;
        } else {
            return false;
        }
    }

    private function calculatePostCReationTime($createdAt)
    {
        $currentTime = Carbon::now();
        $difference = $currentTime->diff($createdAt);

        $formattedTime = '';
        if ($difference->m > 0) {
            if ($difference->m == 1) {
                $formattedTime .= $difference->m . ' mon';
            } else {
                $formattedTime = convertDate($createdAt, 'M d, Y');
            }
        } else if ($difference->d > 0) {
            $formattedTime .= "{$difference->d}d";
        } else if ($difference->h > 0) {
            $formattedTime .= "{$difference->h}h";
        } else if ($difference->i > 0) {
            $formattedTime .= "{$difference->i}m";
        } else if ($difference->s > 0) {
            $formattedTime .= "{$difference->s}s";
        }

        return trim($formattedTime);
    }

    private function getPriceFormat($price, $levelOneTag)
    {
        if (in_array($levelOneTag->slug, ['automotive', 'boats', 'real-estate'])) {
            return number_format($price, 0, "", ",");
        } else {
            return numberFormat($price);
        }
    }

    private function getAttributes($product)
    {
        $singularAttributesSlug = ['bed', 'square-foot', 'bath'];
        $pluralAttributeSlug = ['beds', 'square-feet', 'baths'];

        $attributes = Attribute::with(['standardTags' => function ($query) use ($product) {
            $query->where('type', 'attribute')->whereRelation('productTags', 'id', $product->id);
        }])
            ->whereIn('slug', $singularAttributesSlug)
            ->orWhereIn('slug', $pluralAttributeSlug)
            ->orderByRaw(
                "CASE
            WHEN slug IN ('bed', 'beds') THEN 1
            WHEN slug IN ('bath', 'baths') THEN 2
            WHEN slug IN ('square-foot', 'square-feet') THEN 3
            ELSE 4
            END"
            )
            ->get()
            ->each(function ($attribute) use ($product) {
                $tags = $attribute->standardTags->filter(function ($tag) use ($product, $attribute) {
                    return $tag->productTags()->where('id', $product->id)
                        ->where('attribute_id', $attribute->id)
                        ->exists();
                })->values();
                // Assign filtered tags back to the attribute
                $attribute->standardTags = $tags;
            });
        return $attributes;
    }

    public function getEventBooking($product)
    {
        $booking = $product->bookings()->where('user_id', auth('sanctum')->user()?->id)->first();
        return $booking ? $booking : null;
    }

    public function getEventComments($product)
    {
        $comments = $product->comments()->with('user')->latest()->take(4)->get();
        $comments = (new CommentTransformer)->transformCollection($comments);
        return $comments ?? null;
    }

    private function getCalendarEvents($product)
    {
        $calendarEvent = $product->calendarEvents()->where('user_id', auth('sanctum')->user()?->id)->first();
        return $calendarEvent ? $calendarEvent : null;
    }


    private function calculateReviewsAverage($product, $levelOneTag,  $levelTwoTag, $levelThreeTag)
    {
        if ($levelOneTag->slug === 'automotive') {
            $ratings = VehicleReview::where('make_id',  $levelTwoTag->id)
                ->where('model_id',  $levelThreeTag?->id)
                ->where('year', $product->vehicle->year)
                ->get();
        }
        return (float)(in_array($levelOneTag->slug, ['automotive']) ? $ratings->avg('overall_rating') : $product->reviews->avg('rating'));
    }

    private function getVariantColorsAndSizes($productId)
    {
        $query = ProductVariant::where('product_id', $productId)->activeAndInStock();

        // Get standard colors
        $standardColors = clone $query;
        $standardColors = $standardColors->whereNotNull('color_id')
            ->groupBy('color_id')
            ->get();
        // Get standard sizes
        $standardSizes = clone $query;
        $standardSizes = $standardSizes->whereNotNull('size_id')
            ->groupBy('size_id')
            ->get();
        // Merge colors and sizes into a single array
        $colors = $standardColors;
        $sizes = $standardSizes;
        // Transform collections
        $colors = !empty($colors) ? (new ProductVariantTransformer)->transformCollection($colors) : null;
        $sizes = !empty($sizes) ? (new ProductVariantTransformer)->transformCollection($sizes) : null;
        return [
            'colors' => $colors,
            'sizes' => $sizes,
        ];
    }
}
