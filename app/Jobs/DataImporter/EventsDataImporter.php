<?php

namespace App\Jobs\DataImporter;

use App\Models\User;
use JsonMachine\Items;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class EventsDataImporter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileBasePath, $count;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->count = 0;
        $this->fileBasePath = base_path("modules_json/EventsDataset.json");
        if (!file_exists($this->fileBasePath)) {
            Log::error("File not found: " . $this->fileBasePath);
            exit;
        }

        try {
            Log::info('Events Importer start');
            $module = StandardTag::whereSlug('events')->whereType('module')
                ->firstOrFail();
            $events = Items::fromFile($this->fileBasePath);
            foreach ($events as $index => $item) {
                Log::alert('Event product id is :' . $item->_id);
                Model::withoutEvents(function () use ($item, $module) {
                    $author = $this->getAuthor($item);
                    $product = Product::updateOrCreate(
                        ['name' => $item?->product_name],
                        [
                            'uuid' => Str::uuid(),
                            'user_id' => $author->id,
                            'description' => $item?->description,
                            'status' => 'active',
                            'previous_status' => 'inactive',
                            'is_featured' => $item?->is_featured,
                            'price' => $item?->price,
                            'max_price' => $item?->max_price,
                        ]
                    );

                    $this->storeItemMedia($product, $item);
                    $hierarchyTag = $this->getHierarchyTags($item, $module);
                    $product->standardTags()->syncWithoutDetaching($hierarchyTag);

                    $product->events()->updateOrCreate(['product_id' => $product], [
                        'performer' => $item->event->performer,
                        'event_ticket' => $item->event->event_ticket,
                        'event_date' => Carbon::parse($item->event->event_date),
                        'event_location' => $item->event->event_location,
                        'away_team' => $item->event->away_team
                    ]);

                    ProductTagsLevelManager::checkProductTagsLevel($product);
                    ProductTagsLevelManager::priorityOneTags($product);
                    ProductTagsLevelManager::priorityTwoTags($product);
                    ProductTagsLevelManager::priorityThree($product);
                    ProductTagsLevelManager::priorityFour($product);
                });

                $this->count++;
            }
            Log::info($this->count . ' items for events module are added to database');
            Log::info('Events Importer end');
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
            Log::warning($e->getLine());
            Log::warning($e->getFile());
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
    }

    // get author information
    private function getAuthor($event) {
        return User::where('email', $event->user_email)->firstOrFail();
    }

    // get all hierarhcy tags from L1 to L4
    private function getHierarchyTags($event, $module) {
        $tags = StandardTag::whereIn('name', [$event->l2, $event->l3, $event->l4])
            ->pluck('id')->toArray();
        return array_merge($tags, [$module->id]);
    }

    // save product images
    private function storeItemMedia($product, $event) {
        $product?->media()->where('type', 'image')->delete();
        foreach ($event?->media as $key => $value) {
            $product?->media()->create([
                'path' => $value?->path,
                'type' => 'image',
                'is_external' => 1
            ]);
        }
    }
}
