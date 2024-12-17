<?php
namespace App\Transformers\Business;

use App\Transformers\Transformer;

class TimingTransformer extends Transformer
{
    public function transform($timing, $options = null)
    {
        return [
            'open_at' => \timeFormat($timing->open_at, \false),
            'close_at' => \timeFormat($timing->close_at, \false),
        ];
    }
}