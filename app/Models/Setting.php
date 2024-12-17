<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Business\Settings\TaxType;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Setting extends Model
{
    use HasFactory;
    
    protected $fillable = ['group', 'type', 'key', 'name', 'value', 'status', 'is_required'];

    public function getValueAttribute($value)
    {
        switch ($this->key) {
            case 'tax_model':
                $value = TaxType::fromValue((int)$value)->description;  
                break;
        }
        return $value;
    }

    public function setValueAttribute($value)
    {
        switch ($this->key) {
            case 'tax_model':
                $value = TaxType::coerce(str_replace(' ', '', ucwords($value)))->value;
                break;
        }
        $this->attributes['value'] = $value;
    }
}
