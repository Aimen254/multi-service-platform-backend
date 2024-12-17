<?php

namespace Modules\Retail\Entities;

use App\Models\Tag;
use App\Models\Size;
use App\Models\User;
use App\Models\Color;
use App\Models\Media;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Mailing;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use App\Models\DeliveryZone;
use App\Models\ScheduleTime;
use App\Models\BusinessHoliday;
use App\Models\BusinessSchedule;
use App\Traits\BusinessSettings;
use App\Models\BusinessAdditionalEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'uuid',
        'slug',
        'owner_id',
        'email',
        'phone',
        'mobile',
        'address',
        'city',
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
        'home_delivery',
        'virtual_appointments',
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
            if (!$business->businessOwner->completed_stripe_onboarding) {
                $business->status = 'inactive';
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

    public function businessOwner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function standardTags()
    {
        return $this->belongsToMany(StandardTag::class)->withPivot('standard_tag_id', 'business_id');
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
        // dd($this);
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
}
