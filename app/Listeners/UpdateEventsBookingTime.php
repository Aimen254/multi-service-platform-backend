<?php

namespace App\Listeners;

use App\Events\UpdateBookedEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Events\Entities\CalendarEvent;

class UpdateEventsBookingTime implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param UpdateBookedEvents $event
     * @return void
     */
    public function handle(UpdateBookedEvents $event)
    {
        $calendar_events = CalendarEvent::where('product_id',$event->product->id)->get();
        foreach ($calendar_events as $calendarEvent) {
            $calendarEvent->update([
                'title' => $event->product->name,
                'date' => $event->date
            ]);
        }
    }
}
