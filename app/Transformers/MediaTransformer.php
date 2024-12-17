<?php
namespace App\Transformers;

use App\Transformers\Transformer;

class MediaTransformer extends Transformer
{

    public function transform($media, $options = null)
    {
        return [
            'id' => $media->id,
            'image' => getImage($media->path, $media->type, $media->is_external),
            'size' => $media->size,
            'path' => $media->path,
        ];
    }
}
