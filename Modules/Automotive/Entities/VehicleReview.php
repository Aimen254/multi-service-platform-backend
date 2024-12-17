<?php

namespace Modules\Automotive\Entities;

use App\Models\Review;
use App\Models\StandardTag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'make_id',
        'model_id',
        'year',
        'status',
        'overall_rating',
        'comfort',
        'interior_design',
        'performance',
        'value_for_the_money',
        'exterior_styling',
        'reliability',
        'title',
        'recommendation',
        'condition',
        'purpose',
        'user_id',
        'reviewer',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        /**
         * Handle the product "creating" event.
         *
         * @return void
         */
        static::created(function (VehicleReview $vehicleReview) {
            //finding avg rating
            $rating = [
                // $vehicleReview->overall_rating,
                $vehicleReview->comfort,
                $vehicleReview->interior_design,
                $vehicleReview->performance,
                $vehicleReview->value_for_the_money,
                $vehicleReview->exterior_styling,
                $vehicleReview->reliability,
            ];
            $averageRating = array_sum($rating)/count($rating);
            if ($vehicleReview->review) {
                $vehicleReview->review->rating = round($averageRating, 2);
                $vehicleReview->review->saveQuietly();
            }
        });
    }

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function maker()
    {
        return $this->belongsTo(StandardTag::class, 'make_id');
    }

    public function model()
    {
        return $this->belongsTo(StandardTag::class, 'model_id');
    }

    
}
