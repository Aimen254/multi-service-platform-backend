<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RestartSupervisorProgram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervisor:restart-program {name : queue-worker}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $programName = $this->argument('name');
        $check = exec("sudo supervisorctl restart $programName 2>&1");
    }
}
