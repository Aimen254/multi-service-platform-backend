<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['attribute_id', 'name', 'slug', 'type', 'is_category', 'priority',  'is_show', 'status', 'mapped_to'];

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
        static::creating(function (Tag $tag) {
            $tag->slug = orphanTagSlug($tag->name);
        });
    }

    public function statusChanger()
    {
        $this->status = $this->status == 'active' ? 'inactive' : 'active';
        return $this;
    }

    public function attributeTags()
    {
        return $this->belongsToMany(AttributeTag::class);
    }

    public function standardTags()
    {
        return $this->belongsTo(StandardTag::class, 'mapped_to');
    }

    public function standardTags_()
    {
        return $this->belongsToMany(StandardTag::class, 'standard_tag_tag');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function mappedTo()
    {
        return $this->belongsTo(StandardTag::class, 'mapped_to');
    }

    public function scopeAsTag($query)
    {
        return $query->select(['id', 'name as text', 'mapped_to', 'is_show', 'priority', 'slug']);
    }

    public function scopeWhereType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_tag')->withPivot('is_extra');
    }
}
