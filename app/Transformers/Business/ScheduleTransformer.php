<?php
namespace App\Transformers\Business;

use App\Transformers\Transformer;
use App\Transformers\Business\TimingTransformer;

class ScheduleTransformer extends Transformer
{
    public function transform($schedule, $options = null)
    {
        $data = [
            'name' => $schedule->name,
            'status' => $schedule->status,
            'timings' => $schedule->status == 'active'
                ? (new TimingTransformer)->transformCollection($schedule->scheduletimes) : []
        ];
        return $data;
    }
}