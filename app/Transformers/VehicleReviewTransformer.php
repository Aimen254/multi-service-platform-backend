<?php

namespace App\Transformers;

use stdClass;
use App\Transformers\Transformer;
use Modules\Automotive\Entities\VehicleReview;
use App\Models\Review;



class VehicleReviewTransformer extends Transformer
{
    public function transform($review, $options = null)
    {

        $model = intval(request()->input('model_id'));
        $make = intval(request()->input('make_id'));

        $minYear = VehicleReview::whereModelId($model)->whereMakeId($make)->min('year');
        $maxYear = VehicleReview::whereModelId($model)->whereMakeId($make)->max('year');
        $data = [
            'title' => (string) $review->title,
            'comfort' =>  (int) $review->comfort,
            'interior_design' =>  (int) $review->interior_design,
            'performance' =>  (int) $review->performance,
            'value_for_the_money' =>  (int) $review->value_for_the_money,
            'exterior_styling' =>  (int) $review->exterior_styling,
            'reliability' =>  (int) $review->reliability,
            'year' => $review->year,
            'min_year' => $minYear,
            'max_year' => $maxYear,
            'rating' => numberFormat($review->overall_rating)
        ];


        if (!request()->input('limitedData')) {
            $data['id'] = (int) $review->id;
            $data['recommendation'] = (string) $review->recommendation;
            $data['condition'] = (string) $review->condition;
            $data['purpose'] = (string) $review->purpose;
            $data['reviewer'] = (string) $review->reviewer;
            $data['overall_rating'] = (int) $review->overall_rating;
            $data['year'] = $review->year;
            $data['model'] = $review->relationLoaded('model') && $review->model ? (new StandardTagTransformer)->transform($review->model) : [];
            $data['maker'] = $review->relationLoaded('maker') && $review->maker ? (new StandardTagTransformer)->transform($review->maker) : [];
            $data['created_at'] = timeFormat($review->created_at);
        }

        return $data;
    }
}
