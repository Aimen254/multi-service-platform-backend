<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Conversation extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'sender_id',
        'reciever_id',
        'module_id',
        'message',
        'product_id'
    ];


    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'reciever_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
