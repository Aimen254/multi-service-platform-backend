<?php

namespace App\Console\Commands;

use App\Jobs\VehicleReviewsTable;
use Illuminate\Console\Command;

class DispatchVehicleReviewTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:vehicle-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'vehicle-reviews table is dispatch';

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
        dispatch(new VehicleReviewsTable());
        $this->info('reviews-table job dispatched successfully.');
    }
}
