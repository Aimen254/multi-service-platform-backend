<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicProfileFollower extends Model
{
    use HasFactory;

    protected $table = 'public_profile_follower';

    protected $fillable = ['following_public_profile_id', 'follower_public_profile_id', 'status'];

    // Define relationships to the Profile model
    public function followingProfile()
    {
        return $this->belongsTo(PublicProfile::class, 'following_public_profile_id');
    }

    public function followerProfile()
    {
        return $this->belongsTo(PublicProfile::class, 'follower_public_profile_id');
    }
}
