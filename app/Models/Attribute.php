<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'global_tag_id', 'slug', 'status', 'manual_position'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        /**
         * Handle the tag "creating" event.
         *
         * @return void
         */
        static::creating(function (Attribute $attribute) {
            $attribute->slug = Str::slug($attribute->name);
        });

        static::updating(function (Attribute $attribute) {
            $attribute->slug = Str::slug($attribute->name);
        });
    }

    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function moduleTags()
    {
        return $this->belongsToMany(StandardTag::class, 'attribute_standard_tag');
    }

    public function orphanTags()
    {
        return $this->hasMany(Tag::class, 'attribute_id');
    }

    public function standardTags()
    {
        return $this->belongsToMany(StandardTag::class, 'attribute_standard_tag');
    }

    public function standardTagPosition()
    {
        return $this->belongsToMany(StandardTag::class, 'attributes_standard_tags_positioning')->withPivot('position');
    }
}
