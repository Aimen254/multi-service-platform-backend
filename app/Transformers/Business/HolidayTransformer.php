<?php
namespace App\Transformers\Business;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Transformers\Transformer;

class HolidayTransformer extends Transformer
{
    public function transform($holiday, $options = null)
    {
        $collection = Str::of($holiday->date)->explode(',');
        $dates = [];
        foreach ($collection as $key => $signleItem) {
            $dates[] = convertDate($signleItem, 'd M Y');
        }
        
        $data = [
            'title' => $holiday->title,
            'dates' => $dates
        ];
        return $data;
    }
}