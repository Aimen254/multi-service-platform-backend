<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadlineSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'module_id',
        'level_two_tag_id',
        'type',
        'created_at'
    ];

    public function article()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function levelTwoTag()
    {
        return $this->belongsTo(StandardTag::class, 'level_two_tag_id', 'id');
    }
}
