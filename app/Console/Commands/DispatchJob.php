<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class DispatchJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:dispatch {job} {--module=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch job';

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
        $module = $this->option('module');
        if ($module) {
            $class = '\\Modules\\' . Str::ucfirst($module) . '\\Jobs\\' . $this->argument('job');
        } else {
            $class = '\\App\\Jobs\\' . $this->argument('job');
        }
        dispatch(new $class());
    }
}
