<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateEventDatesJob;

class UpdateEventDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:update_dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update event dates one month later';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        dispatch(new UpdateEventDatesJob());
        
        $this->info('Event dates update job dispatched successfully.');
    }
}
