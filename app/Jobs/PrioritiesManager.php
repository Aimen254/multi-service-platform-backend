<?php

namespace App\Jobs;

use App\Models\Tag;
use App\Models\StandardTag;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PrioritiesManager implements ShouldQueue
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
        Tag::whereDoesntHave('standardTags_')->delete();
        $toTruncate = [
            'products',
            'product_standard_tag',
            'product_tag',
            'product_variants',
            'product_priorities'
        ];

        Schema::disableForeignKeyConstraints();
        foreach($toTruncate as $table) {
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();

        $tags = StandardTag::whereIn('type', ['brand', 'attribute'])->orwhereHas('levelOne')
            ->orWhereHas('levelTwo')->orWhereHas('levelThree')->orWhereHas('tagHierarchies')->get();
    
        foreach ($tags as $key => $tag) {
            if ($tag->type == 'brand') {
                $tag->priority = 3;
            } else if ($tag->type == 'attribute') {
                $tag->priority = 2;
            } else {
                $tag->priority = 1;
            }
            $tag->updateQuietly();

            if ($tag->tags) {
                $tag->tags()->update([
                    'priority' => $tag->priority,
                    'type' => $tag->type,
                    'attribute_id' => $tag->attribute_id,
                ]);
            }
        }
    }
}
