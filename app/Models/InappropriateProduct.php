<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InappropriateProduct extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'model_type', 'model_id', 'type'];

    public function model()
    {
        return $this->morphTo();
    }
}
