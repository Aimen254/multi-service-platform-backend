<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Business;
use Illuminate\Support\Str;
// use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\UpdateProductPriorityOneTag;
use Modules\Automotive\Entities\DreamCar;
use Modules\Automotive\Entities\VehicleReview;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class StandardTag extends Model
{
    use HasFactory, HasEagerLimit;

    protected $fillable = ['name', 'type', 'attribute_id', 'slug', 'icon', 'priority', 'can_chat'];

    protected static function booted()
    {
        parent::boot();

        /**
         * Handle the tag "creating" event.
         *
         * @return void
         */
        static::creating(function (StandardTag $tag) {
            $tag->slug = Str::slug($tag->name);
            $icon = config()->get('image.media.icon');
            $width = $icon['width'];
            $height = $icon['height'];
            if ($tag->icon) {
                $extension = $tag->icon->extension();
                $tag->icon = saveResizeImage($tag->icon, "Icons/industryTags", $width, $height, $extension);
            }
        });

        /**
         * Handle the tag "created" event.
         *
         * @return void
         */
        static::created(function (StandardTag $tag) {
            if ($tag->type == 'brand') {
                //finding orphan tag and mapping it.
                $orphanTags = Tag::where('slug', $tag->slug)->where('type', $tag->type)->first();
                if ($orphanTags) {
                    $orphanTags->mapped_to = $tag->id;
                    $orphanTags->save();
                    $productIds = $orphanTags->products()->pluck('id');
                    $tag->productTags()->sync($productIds);
                }
            }
        });

        /**
         * Handle the tag "updating" event.
         *
         * @return void
         */
        static::updating(function (StandardTag $tag) {
            $tag->slug = Str::slug($tag->name);
            $icon = config()->get('image.media.icon');
            $width = $icon['width'];
            $height = $icon['height'];
            if (request()->hasFile('icon')) {
                $extension = $tag->icon->extension();
                $tag->icon = saveResizeImage($tag->icon, "Icons/industryTags", $width, $height, $extension);
            }
        });

        /**
         * Handle the tag "updated" event.
         *
         * @return void
         */
        static::updated(function (StandardTag $tag) {
            if ($tag->type == 'brand') {
                // detaching old orphan tags and product tags.
                $orphanTag = $tag->tags()->first();
                if ($orphanTag) {
                    $orphanTag->mapped_to = null;
                    $orphanTag->save();
                }
                $tag->productTags()->detach();

                // attaching new orphan tags and product tags.
                $orphanTag = Tag::where('slug', $tag->slug)->where('type', $tag->type)->first();
                if ($orphanTag) {
                    $orphanTag->mapped_to = $tag->id;
                    $orphanTag->save();
                    $productIds = $orphanTag->products()->pluck('id');
                    $tag->productTags()->sync($productIds);
                }
            }else if($tag->type == 'product'){
                if ($tag->isDirty('name')){
                    dispatch(new UpdateProductPriorityOneTag($tag));
                }
            }
        });

        /**
         * Handle the tag "deleting" event.
         *
         * @return void
         */
        static::deleting(function (StandardTag $tag) {
            if ($tag->tags) {
                $tag->tags()->update(['mapped_to' => null]);
            }
            $tag->productTags()->detach();
        });
    }

    public function scopeSearchName($query, $keyword)
    {
        $query->where('name', 'like', '%' . $keyword . '%');
    }

    public function scopeActiveProducts($query)
    {
        return $query->whereHas('productTags', function ($subQuery) {
            // $subQuery->whereRelation('business', 'status', 'active');
            $subQuery->where('status', 'active')
                ->select('*', DB::raw('count(*) as active_products'))
                ->having('active_products', '>', 0);
        });
    }

    public function scopeFilterProducts($query)
    {
        return $query->whereHas('productTags', function ($subQuery) {
            $subQuery->when(request()->input('header_filter'), function ($innerQuery) {
                switch (\request()->header_filter) {
                    case 'delivery':
                        $innerQuery->where('is_deliverable', '!=', 0)
                            ->whereRelation('business.deliveryZone', 'delivery_type', '!=', 0);
                        break;
                    case 'new':
                    case 'used':
                        $innerQuery->whereRelation('vehicle', 'type', request()->input('header_filter'));
                        break;
                }
            });
            $subQuery->where(function ($query) {
                $query->whereHas('business', function ($innerQuery) {
                    $innerQuery->where('status', 'active');
                })->orWhereDoesntHave('business');
            });
            $subQuery->where('status', 'active')
                ->select('*', DB::raw('count(*) as active_products'))
                ->having('active_products', '>', 0);
        });
    }

    public function parents()
    {
        return $this->belongsToMany(StandardTag::class, 'standard_tag_parent_child', 'child_id', 'parent_id');
    }

    public function productTags()
    {
        return $this->belongsToMany(Product::class, 'product_standard_tag');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class, 'mapped_to');
    }

    public function tags_()
    {
        return $this->belongsToMany(Tag::class, 'standard_tag_tag');
    }

    public function tag()
    {
        return $this->belongsToMany(Tag::class, 'standard_tag_tag');
    }

    public function attribute()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_standard_tag')->withPivot('attribute_id');
    }

    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }

    public function scopeAsTag($query)
    {
        return $query->select(['id', 'name as text', 'type', 'status', 'slug', 'priority']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function parent()
    {
        return $this->belongsToMany(StandardTag::class, 'standard_tag_parent_child', 'child_id', 'parent_id');
    }

    public function children()
    {
        return $this->belongsToMany(StandardTag::class, 'standard_tag_parent_child', 'parent_id', 'child_id');
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class);
    }

    public function tagHierarchies()
    {
        return $this->belongsToMany(TagHierarchy::class, 'tag_hierarchies_standard_tag');
    }

    public function levelOne()
    {
        return $this->hasMany(TagHierarchy::class, 'L1');
    }
    public function levelTwo()
    {
        return $this->hasMany(TagHierarchy::class, 'L2');
    }
    public function levelThree()
    {
        return $this->hasMany(TagHierarchy::class, 'L3');
    }
    public function levelFour()
    {
        return $this->hasMany(TagHierarchy::class, 'L4');
    }
    public function attributePosition()
    {
        return $this->belongsToMany(Attribute::class, 'attributes_standard_tags_positioning')->withPivot('position');
    }
    public function wishList()
    {
        return $this->morphMany(Wishlist::class, 'model');
    }
    public function vehicleMakeReview()
    {
        return $this->hasMany(VehicleReview::class, 'make_id');
    }
    public function vehicleModelReview()
    {
        return $this->hasMany(VehicleReview::class, 'model_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function module()
    {
        return $this->hasMany(DreamCar::class, 'module_id');
    }

    public function maker()
    {
        return $this->hasMany(DreamCar::class, 'make_id');
    }

    public function model()
    {
        return $this->hasMany(DreamCar::class, 'model_id');
    }

    public function levelFourTag()
    {
        return $this->hasMany(DreamCar::class, 'level_four_tag_id');
    }

}
