<?php

namespace App\Transformers;

use Carbon\Carbon;

class CalendarEventTransformer extends Transformer
{
    public function transform($event, $options = null)
    {
        // Parse the event date string into a Carbon instance
        $endTime = Carbon::parse($event->date);
        // Format the Carbon instance as a string
        $endTimeFormatted = $endTime->endOfDay()->format('Y-m-d H:i:s.u');
        $data = [
            'id' => (int) $event?->id,
            'title' => (string) $event?->title,
            'product_uuid' => (string) $event?->product?->uuid,
            'start' => $event?->date,
            'end' => $endTimeFormatted,
            'allDay' => false,
            "extendedProps" => [
                'calendar' =>  (string) $event?->status
            ]

        ];
        return $data;
    }
}
