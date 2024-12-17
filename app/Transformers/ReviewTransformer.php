<?php
namespace App\Transformers;

use stdClass;
use App\Transformers\Transformer;
use App\Transformers\UserTransformer;
use App\Transformers\VehicleReviewTransformer;

class ReviewTransformer extends Transformer
{
    public function transform($review, $options = null)
    {

        
        
        $data = [
            'id' => (int) $review->id,
            'rating' =>  $review->rating,
            'comment' => (string) $review->comment,
            'created_at'=> timeFormat($review->created_at),
            'user' => $review->user ? (new UserTransformer)->transform($review->user) : new stdClass(),
        ];

        if ($review->relationLoaded('vehicleReview')) {
            $data['vehicle_review'] = $review->vehicleReview ? (new VehicleReviewTransformer)->transform($review->vehicleReview) : new stdClass();
        }
    
        return $data;
    }
}
