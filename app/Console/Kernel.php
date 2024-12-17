<?php

namespace App\Console;

use Carbon\Carbon;
use App\Models\Coupon;
use App\Jobs\CreateNewsHeadline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Console\Commands\CheckCardExpiry;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\UpdateEventDates::class,
        \App\Console\Commands\DispatchVehicleReviewTable::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $coupons = Coupon::whereDate('end_date', '<', Carbon::now())->get();
            foreach ($coupons as $coupon) {
                $coupon->update(['status' => 'expired']);
            }
        })->everyMinute();

        $schedule->command('check:cardExpiry')->daily();
        $schedule->job(new CreateNewsHeadline)->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
