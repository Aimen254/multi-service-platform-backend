<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Bus\Queueable;
use App\Models\HeadlineSetting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CreateNewsHeadline implements ShouldQueue
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
        // Level One Tag (News)
        $news = StandardTag::active()->where('slug', 'news')->where('type', 'module')->first();

        // Level Two Tags (News)
        $sports = StandardTag::active()->where('slug', 'sports')->where('type', 'product')->first();
        $world = StandardTag::active()->where('slug', 'world')->where('type', 'product')->first();
        $metro = StandardTag::active()->where('slug', 'metro')->where('type', 'product')->first();
        $national = StandardTag::active()->where('slug', 'national')->where('type', 'product')->first();


        /**
         * Get single record of news article related to level one tag and update
         * their creation date to today.
         */
        if (isset($news)) {
            $newsLevelOneArticle = Product::active()->whereHas('standardTags', function ($query) use ($news) {
                $query->whereHas('levelOne', function ($subQuery) use ($news) {
                    $subQuery->where('L1', $news->id);
                });
            })->whereDoesntHave('headline')->first();
            if ($newsLevelOneArticle) {
                $newsLevelOneArticle->created_at = Carbon::now();
                $newsLevelOneArticle->saveQuietly();

                // Creating news level one tag article's primary headline
                HeadlineSetting::create([
                    'product_id' => $newsLevelOneArticle->id,
                    'module_id' => $news->id,
                    'level_two_tag_id' => null,
                    'type' => 'Primary'
                ]);
            }
        }

        /**
         * Get single record of articles related to level two tags (sports, world, metro, national)
         * and update their creation date to today.
         * The purpose of modification of creation dates to set today's articles as headline.
         */
        if (isset($news) && isset($sports)) {
            $sportsArticle = Product::active()->whereHas('standardTags', function ($query) use ($news, $sports) {
                $query->whereHas('levelTwo', function ($subQuery) use ($news, $sports) {
                    $subQuery->where('L1', $news->id)->where('L2', $sports->id);
                });
            })->whereDoesntHave('headline')->first();
            
            if ($sportsArticle) {
                $sportsArticle->created_at = now();
                $sportsArticle->saveQuietly();

                // Creating sports' article secondary headline
                HeadlineSetting::create([
                    'product_id' => $sportsArticle->id,
                    'module_id' => $news->id,
                    'level_two_tag_id' => $sports->id,
                    'type' => 'Secondary'
                ]);
            }
        }

        if (isset($news) && isset($world)) {
            $worldArticle = Product::active()->whereHas('standardTags', function ($query) use ($news, $world) {
                $query->whereHas('levelTwo', function ($subQuery) use ($news, $world) {
                    $subQuery->where('L1', $news->id)->where('L2', $world->id);
                });
            })->whereDoesntHave('headline')->first();
            if ($worldArticle) {
                $worldArticle->created_at = now();
                $worldArticle->saveQuietly();

                // Creating world's article secondary headline
                HeadlineSetting::create([
                    'product_id' => $worldArticle->id,
                    'module_id' => $news->id,
                    'level_two_tag_id' => $world->id,
                    'type' => 'Secondary'
                ]);
            }
        }

        if (isset($news) && isset($metro)) {
            $metroArticle = Product::active()->whereHas('standardTags', function ($query) use ($news, $metro) {
                $query->whereHas('levelTwo', function ($subQuery) use ($news, $metro) {
                    $subQuery->where('L1', $news->id)->where('L2', $metro->id);
                });
            })->whereDoesntHave('headline')->first();
            if ($metroArticle) {
                $metroArticle->created_at = now();
                $metroArticle->saveQuietly();

                // Creating metro's article secondary headline
                HeadlineSetting::create([
                    'product_id' => $metroArticle->id,
                    'module_id' => $news->id,
                    'level_two_tag_id' => $metro->id,
                    'type' => 'Secondary'
                ]);
            }
        }

        if (isset($news) && isset($national)) {
            $nationalArticle = Product::active()->whereHas('standardTags', function ($query) use ($news, $national) {
                $query->whereHas('levelTwo', function ($subQuery) use ($news, $national) {
                    $subQuery->where('L1', $news->id)->where('L2', $national->id);
                });
            })->whereDoesntHave('headline')->first();
            if ($nationalArticle) {
                $nationalArticle->created_at = now();
                $nationalArticle->saveQuietly();

                // Creating national's article secondary headline
                HeadlineSetting::create([
                    'product_id' => $nationalArticle->id,
                    'module_id' => $news->id,
                    'level_two_tag_id' => $national->id,
                    'type' => 'Secondary'
                ]);
            }
        }
    }
}
