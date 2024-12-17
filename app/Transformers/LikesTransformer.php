<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class LikesTransformer extends Transformer
{
    public function transform($like, $options = null)
    {
        $options = [
            'withAddress' => request()->input('with_user_address') ? true : false
        ];

        return [
            'user' => (new UserTransformer)->transform($like->user, $options),
            'avatar' => getImage($like->user->publicProfile?->image ?? $like->user->avatar, 'avatar', $like->user->is_external)
        ];
    }
}
