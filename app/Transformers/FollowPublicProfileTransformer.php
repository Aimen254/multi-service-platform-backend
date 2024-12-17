<?php

namespace App\Transformers;
use App\Transformers\Transformer;

class FollowPublicProfileTransformer extends Transformer {

    public function transform($follow, $options = null) {
        return [
            'id' => $follow?->id,
            'status' => $follow?->status,
            'follower' => (new PublicProfileTransformer)->transform($follow?->followerProfile),
            'following' => (new PublicProfileTransformer)->transform($follow?->followingProfile),
            'updated_at' => $follow?->updated_at,
            'created_at' => $follow?->created_at,
        ];
    }
}