<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TagHierarchy extends Model
{
    use HasFactory;

    protected $fillable = [
        'L1',
        'L2',
        'L3',
        'L4',
        'level_type',
        'is_multiple',
        'status',
    ];

    protected static function booted()
    {
        parent::boot();

        /**
         * Handle the tag "creating" event.
         *
         * @return void
        */
        static::deleting(function (TagHierarchy $tagHierarchy) {
            $tagHierarchy->standardTags()->detach();
        });
    }

    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }

    public function levelOne()
    {
        return $this->belongsTo(StandardTag::class, 'L1');
    }
    public function levelTwo()
    {
        return $this->belongsTo(StandardTag::class, 'L2');
    }
    public function levelThree()
    {
        return $this->belongsTo(StandardTag::class, 'L3');
    }
    public function levelFour()
    {
        return $this->belongsTo(StandardTag::class, 'L4');
    }

    public function standardTags()
    {
        return $this->belongsToMany(StandardTag::class, 'tag_hierarchies_standard_tag');
    }

}
