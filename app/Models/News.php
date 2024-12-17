<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = ['news_category_id', 'title', 'slug', 'image', 'description'];


    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        /**
         * Handle the User "creating" event.
         *
         * @param  \App\Models\User  $user
         * @return void
        */
        static::creating(function (News $news) {
            if ($news->image) {
                $newsImage = config()->get('image.media.news');
                $width = $newsImage['width'];
                $height = $newsImage['height'];
                $extension = $news->image->extension();
                $news->image = saveResizeImage($news->image, "news_images", $width, $height, $extension);
            }
        });

        /**
         * Handle the User "updating" event.
         *
         * @param  \App\Models\User  $user
         * @return void
        */
        static::updating(function (News $news) {
            if (request()->hasFile('image')) {
                $newsImage = config()->get('image.media.news');
                $width = $newsImage['width'];
                $height = $newsImage['height'];
                $extension = $news->image->extension();
                $news->image = saveResizeImage($news->image,  "news_images", $width, $height, $extension);
            }
        });

    }

    public function newsCategory()
    {
        return $this->belongsTo(NewsCategory::class);
    }
}
