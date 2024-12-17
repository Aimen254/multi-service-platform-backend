<?php

namespace App\Transformers\MyLv;

use App\Transformers\Transformer;

class BusinessTransformer extends Transformer
{
    public function transform($business, $options = null)
    {
        return [
            'id' => (int) $business?->id,
            'name' => (string) $business?->name,
            'uuid' => (string) $business?->uuid,
            'logo' => $business->logo
                ? getImage($business?->logo?->path, 'image', $business?->logo?->is_external)
                : getImage(NULL, 'image'),
            'slug' => (string) $business?->slug
        ];
    }
}
