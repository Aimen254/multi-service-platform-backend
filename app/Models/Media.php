<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Media extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['model_id', 'model_type', 'path', 'size', 'mime_type', 'type', 'is_external'];

    /**
     * Get the parent model
     */
    public function model()
    {
        return $this->morphTo();
    }
}
