<?php

namespace App\Transformers;

use App\Transformers\Transformer;

class EventTransformer extends Transformer
{

    public function transform($event, $options = null)
    {
        return [
            'id' => (int) $event?->id,
            'performer' => (string) $event?->performer,
            'event_ticket' => $event?->event_ticket,
            'event_date' => $event?->event_date,
            'event_location' => $event?->event_location,
            'away_team' => (string) $event?->away_team,
        ];
    }
}
