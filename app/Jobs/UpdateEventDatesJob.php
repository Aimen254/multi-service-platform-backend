<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Events\Entities\Event;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Events\Entities\CalendarEvent;

class UpdateEventDatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $events = Event::all();
            
            foreach ($events as $event) {
                $newDate = Carbon::parse($event->event_date)->addMonth();
                $event->update(['event_date' => $newDate]);
            }
            $calendarEvents = CalendarEvent::all();
            
            foreach ($calendarEvents as $calendarEvent) {
                $newDate = Carbon::parse($calendarEvent->date)->addMonth();
                $calendarEvent->update(['date' => $newDate]);
            }

            Log::info('Event dates updated successfully.');
        } catch (Exception $exception) {
            Log::error('An error occurred while updating event dates: ' . $exception->getMessage());
        }
    }
}
