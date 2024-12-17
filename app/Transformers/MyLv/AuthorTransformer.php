<?php

namespace App\Transformers\MyLv;

use App\Transformers\Transformer;

class AuthorTransformer extends Transformer
{
    function transform($author, $options = null)
    {
        return [
            'id' => (int)$author->id,
            'uuid' => (string)$author?->uuid,
            'name' => $this->getName($author, $options),
            'slug' => (string) $author->slug,
            'logo' => $this->getLogo($author, $options)
        ];
    }

    private function getName($author, $options): string
    {
        return $options['is_business']
            ? $author->name : $author->first_name . ' ' . $author->last_name;
    }

    function getLogo($author, $options): string
    {
        return $options['is_business']
            ? ($author->logo ? getImage($author->logo->path, 'image') : getImage(NULL, 'image'))
            : (getImage($author->avatar, 'avatar', $author->is_external));
    }
}
