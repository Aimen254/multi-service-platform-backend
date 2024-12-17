<?php

namespace App\Transformers;

use App\Models\Product;;

use App\Transformers\Transformer;

class SettingTransformer extends Transformer
{
    public function transform($setting, $options = null)
    {
        return [
            'id' => (int) $setting->id,
            'group' => (string) $setting->group,
            'type' => (string) $setting->type,
            'key' => (string) $setting->key,
            'name' => (string) $setting->name,
            'value' => (string) $setting->value,
            'status' => (string) $setting->status,
        ];
    }
}
