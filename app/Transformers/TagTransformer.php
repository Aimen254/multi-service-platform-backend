<?php
namespace App\Transformers;

use stdClass;
use Illuminate\Support\Arr;
use App\Transformers\Transformer;
use App\Transformers\AttributeTypeTransformer;

class TagTransformer extends Transformer
{
    public function transform($orphanTag, $options = null)
    {
        $orphanTagResponse = [
            'id' => (int) $orphanTag->id,
            'name' => (string) $orphanTag->name,
            'slug' => (string) $orphanTag->slug,
            'type' => (string) $orphanTag->type,
        ];

        if(!(isset($options['withFilters']) && $options['withFilters'])){
            $orphanTagResponse['created_at'] = timeFormat($orphanTag->created_at);
            $orphanTagResponse['parent'] = $orphanTag->parent ? (new StandardTagTransformer)->transform($orphanTag->parent) : new stdClass();
        }
        if ($orphanTag->type == 'attribute' && !(isset($options['withFilters']) && $options['withFilters'])) 
        {
            $orphanTagResponse['attribute'] =  $orphanTag->attribute ? (new AttributeTypeTransformer)->transform($orphanTag->attribute) : new stdClass();
        }
        return $orphanTagResponse;
    }
}
