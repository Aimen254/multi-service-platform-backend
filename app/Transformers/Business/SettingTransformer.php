<?php
namespace App\Transformers\Business;

use App\Transformers\Transformer;

class SettingTransformer extends Transformer
{
    public function transform($setting, $options = null)
    {
        return [
            'key' => $setting->key,
            'value' => $setting->value,
            'status' => $setting->status,
        ];
    }
}