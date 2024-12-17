<?php

namespace App\Jobs;

use App\Models\Tag;
use App\Models\Business;;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class MakeExtraTags implements ShouldQueue
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $orphans;
    public $businessIds;
    public function __construct($tags, $businessIds)
    {
        $this->orphans = $tags;
        $this->businessIds = $businessIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->orphans as $orphan) {
            $tag = Tag::find($orphan);
            foreach ($this->businessIds as $id) {
                $tag->businesses()->sync([$id => ['is_extra' => true]], false);
            }
        }
    }
}
