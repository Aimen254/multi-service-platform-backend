<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Traits\BusinessSettings;
use App\Traits\ModuleSessionManager;
use Modules\Retail\Entities\Mailing;
use App\Enums\Business\BusinessStatus;
use Illuminate\Database\Eloquent\Model;
use Modules\Retail\Entities\DeliveryZone;
use Modules\Retail\Entities\ScheduleTime;
use Modules\Retail\Entities\BusinessHoliday;
use Modules\Retail\Entities\BusinessSetting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Retail\Entities\BusinessSchedule;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Retail\Entities\BusinessAdditionalEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'user_name',
        'uuid',
        'slug',
        'owner_id',
        'email',
        'phone',
        'mobile',
        'address',
        'street_address',
        'latitude',
        'longitude',
        'short_description',
        'long_description',
        'message',
        'shipping_and_return_policy',
        'shipping_and_return_policy_short',
        'url',
        'is_direct_url',
        'is_featured',
        'tax',
        'minimum_purchase',
        'status',
        'status_updated_by',
        'city',
        'home_delivery',
        'virtual_appointments',
        'facebook_id',
        'twitter_id',
        'pinterest_id',
        'instagram_id'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        /**
         * Handle the Business "creating" event.
         *
         * @param  \App\Models\Business  $business
         * @return void
         */
        static::creating(function (Business $business) {
            if (request()->input('slug')) {
                $business->slug = Str::slug(request()->slug);
            }
            $business->uuid = Str::uuid();
            //checking if business owner has not completed on-boarding process yet.
            if (!$business->businessOwner->completed_stripe_onboarding && $business->businessOwner->user_type != 'customer' && ModuleSessionManager::getModule() != 'real-estate') {
                // $business->status = 'inactive';
                $business->status = 'active';
            } else if (ModuleSessionManager::getModule() == 'real-estate') {
                $business->status = BusinessStatus::WAITING_APPROVAL;
            }
        });

        /**
         * Handle the Business "created" event.
         *
         * @param  \App\Models\Business  $business
         * @return void
         */
        static::created(function (Business $business) {
            BusinessSettings::addBusinessSettings($business);
            BusinessSettings::addBusinessSchedule($business);
            BusinessSettings::addBusinessDeliverySetting($business);
        });

        /**
         * Handle the Business "updating" event.
         *
         * @param  \App\Models\Business  $business
         * @return void
         */
        static::updating(function (Business $business) {
            if (request()->slug) {
                $business->slug = Str::slug(request()->slug);
            }
            if (ModuleSessionManager::getModule() == 'real-estate' && $business->status == 'rejected') {
                $business->status = BusinessStatus::WAITING_APPROVAL;
            }
        });

        /**
         * Handle the Business "deleting" event.
         *
         * @param  \App\Models\Business  $business
         * @return void
         */
        static::deleted(function (Business $business) {
            $business->media()->delete();
        });
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function logo()
    {
        return $this->morphOne(Media::class, 'model')->where('type', 'logo');
    }

    public function thumbnail()
    {
        return $this->morphOne(Media::class, 'model')->where('type', 'thumbnail');
    }

    public function banner()
    {
        return $this->morphOne(Media::class, 'model')->where('type', 'banner');
    }
    public function secondaryBanner()
    {
        return $this->morphOne(Media::class, 'model')->where('type', 'secondaryBanner');
    }

    public function secondaryImages()
    {
        return $this->morphMany(Media::class, 'model')->where('type', 'banner')->skip(1)->take(3);
    }
    public function deliveryZone()
    {
        return $this->morphOne(DeliveryZone::class, 'model');
    }

    public function settings()
    {
        return $this->hasMany(BusinessSetting::class);
    }

    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }

    public function businessschedules()
    {
        return $this->hasMany(BusinessSchedule::class);
    }

    public function scheduletimes()
    {
        return $this->hasManyThrough(ScheduleTime::class, businessschedule::class);
    }


    public function businessOwner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function coupons()
    {
        return $this->morphMany(Coupon::class, 'model');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'model');
    }

    public function sizes()
    {
        return $this->hasMany(Size::class);
    }

    public function colors()
    {
        return $this->hasMany(Color::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class)->with('reviews');
    }

    public function businessHolidays()
    {
        return $this->hasMany(BusinessHoliday::class);
    }

    public function mails()
    {
        return $this->hasMany(Mailing::class);
    }

    public function additionalEmails()
    {
        return $this->hasMany(BusinessAdditionalEmail::class);
    }

    public function standardTags()
    {
        return $this->belongsToMany(StandardTag::class)->withPivot('standard_tag_id', 'business_id');
    }

    public function scopeActive($query)
    {
        return $query->whereStatus('active');
    }

    public function extraTags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'business_tag');
    }

    public function wishList()
    {
        return $this->morphMany(Wishlist::class, 'model');
    }

    public function setIsFeaturedAttribute($value)
    {
        $this->attributes['is_featured'] = (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    public function setHomeDeliveryAttribute($value)
    {
        $this->attributes['home_delivery'] = (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    public function setVirtualAppointmentsAttribute($value)
    {
        $this->attributes['virtual_appointments'] = (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'business_id', 'id');
    }
}
