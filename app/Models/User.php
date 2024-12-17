<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Business;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Modules\Automotive\Entities\DreamCar;
use Modules\Retail\Entities\DeliveryZone;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'avatar',
        'dob',
        'user_type',
        'is_social',
        'neighborhood_name',
        'about',
        'phone_otp',
        'email_otp',
        'otp',
        'is_verified',
        'email_verified_at',
        'is_external',
        'stripe_customer_id',
        'stripe_connect_id',
        'stripe_bank_id',
        'completed_stripe_onboarding',
        'cover_img',
        'business_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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
         * Handle the User "creating" event.
         *
         * @param  \App\Models\User  $user
         * @return void
         */
        static::creating(function (User $user) {
            $avatar = config()->get('image.media.avatar');

            $width = $avatar['width'];
            $height = $avatar['height'];

            if (!$user->is_social && $user->avatar) {
                $extension = $user->avatar->extension();
                $user->avatar = saveResizeImage($user->avatar, "avatars", $width, $height, $extension);
            }
            if ($user->user_type == 'government_staff') {
                $user->email_verified_at = Carbon::now();
            }
        });

        /**
         * Handle the User "created" event.
         *
         * @param  \App\Models\User  $user
         * @return void
         */
        static::created(function (User $user) {
            $user->assignRole($user->user_type);
        });

        /**
         * Handle the User "updating" event.
         *
         * @param  \App\Models\User  $user
         * @return void
         */
        static::updating(function (User $user) {
            $avatar = config()->get('image.media.avatar');
            $width = $avatar['width'];
            $height = $avatar['height'];
            if (request()->hasFile('avatar')) {
                $userAvatar = request()->file('avatar');
                $extension = $userAvatar->extension();
                $user->avatar = saveResizeImage($userAvatar, "avatars", $width, $height, $extension);
            }

            if (request()->hasFile('cover_image')) {
                $coverImg = config()->get('image.media.banner');
                $width = $coverImg['width'];
                $height = $coverImg['height'];
                $coverFile = request()->file('cover_image');
                $extension = $coverFile->extension();
                $user->cover_img = saveResizeImage($coverFile, "cover", $width, $height, $extension);
            }
        });

        static::updated(function (User $user) {
            $user->assignRole($user->user_type);
        });

        /**
         * Handle the User "updated" event.
         *
         * @param  \App\Models\User  $user
         * @return void
         */
        static::deleting(function (User $user) {
            $user->businesses()->update(['status' => 'inactive']);
        });
    }

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function scopeActive($query)
    {
        return $query->whereStatus('active');
    }

    public function scopeFavoriteUsers($query, $request, $module)
    {

        return $query->when($request->favorite === 'favorite', function ($subQuery) use ($request, $module) {
            $subQuery->whereHas('favoriteUsers',function($query) use ($request, $module) {
                $query->where('user_id', $request->user()->id)->where('module_id',$module->id);
            });
        }, function ($subQuery) use ($request, $module) {
            $subQuery->whereDoesntHave('favoriteUsers', function ($subQuery) use ($request, $module) {
                $subQuery->where('user_id', $request->user()->id)->where('module_id',$module->id);
            });
        });
    }


    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }

    public function businesses()
    {
        return $this->hasMany(Business::class, 'owner_id');
    }

    public function latestAddress()
    {
        return $this->hasOne(Address::class)->latest();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'model');
    }

    public function orders()
    {
        return $this->morphMany(Order::class, 'model');
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function cards()
    {
        return $this->hasMany(CreditCard::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function deliveryZone()
    {
        return $this->morphOne(DeliveryZone::class, 'model');
    }

    public function wishList()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function businessWishList()
    {
        return $this->hasMany(BusinessWishlist::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function searchHistory()
    {
        return $this->hasMany(Search::class, 'user_id');
    }

    public function dreamCars()
    {
        return $this->hasMany(DreamCar::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function inappropriateProduct()
    {
        return $this->hasMany(InappropriateProduct::class, 'user_id');
    }

    public function resumes()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function favoriteUsers()
    {
        return $this->morphMany(Wishlist::class, 'model');
    }

    public function publicProfiles(): HasMany
    {
        return $this->hasMany(PublicProfile::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function standardTags(): BelongsToMany
    {
        return $this->belongsToMany(StandardTag::class)->withPivot('standard_tag_id', 'user_id');
    }

    public function recepients()
    {
        return $this->hasMany(Recepient::class);
    }
}
