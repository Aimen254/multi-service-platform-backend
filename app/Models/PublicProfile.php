<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'module_id',
        'image',
        'cover_image',
        'name',
        'description',
        'is_name_visible',
        'is_public',
        'nick_name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function followers()
    {
        return $this->belongsToMany(PublicProfile::class, 'public_profile_follower', 'following_public_profile_id', 'follower_public_profile_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(PublicProfile::class, 'public_profile_follower', 'follower_public_profile_id', 'following_public_profile_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }
    public function repostedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_public_profile')->withPivot('user_id')->withTimestamps();
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
