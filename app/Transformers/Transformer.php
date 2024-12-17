<?php

namespace App\Transformers;

abstract class Transformer
{
    /**
     * Transform a collection.
     *
     * @param $items
     * @return array
     */
    public function transformCollection($collection , $options = null)
    {
        return $collection->transform(function($item, $key) use ($options) {
            return $this->transform($item, $options);
        });
    }
    
    public abstract function transform($item, $options = null);
}