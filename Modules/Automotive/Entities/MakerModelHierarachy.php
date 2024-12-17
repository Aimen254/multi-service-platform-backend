<?php

namespace Modules\Automotive\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MakerModelHierarachy extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'make',
        'model',
        'year',
        'trim',
        'trim (description)'
    ];

    protected static function newFactory()
    {
        return \Modules\Automotive\Database\factories\MakerModelHierarachyFactory::new();
    }
}
