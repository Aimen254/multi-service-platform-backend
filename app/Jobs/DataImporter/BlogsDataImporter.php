<?php

namespace App\Jobs\DataImporter;

use App\Models\User;
use JsonMachine\Items;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class BlogsDataImporter implements ShouldQueue
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        Log::info('Blogs Importer start');

        $jsonFilePath = base_path("modules_json/BlogDataset.json");
        if (!file_exists($jsonFilePath)) {
            Log::error("File not found: $jsonFilePath");
            return;
        }

        $blogs = Items::fromFile($jsonFilePath);
        $module = StandardTag::where('slug', 'blogs')->pluck('id')->toArray();
        if(count($module) > 0) {
            foreach ($blogs as $blog) {
                $userId = User::where('email', $blog?->user_email)->first()?->id;            
    
                $tags = StandardTag::whereIn('name', [$blog->l2, $blog->l3, $blog->l4])
                    ->pluck('id')->toArray();
                $tags = array_merge($tags, $module);
                $product = null;
                Model::withoutEvents(function () use ($blog, &$product, &$userId) { 
                    $product = Product::updateOrCreate(['name' => $blog?->product_name ], [
                        'uuid' => Str::uuid(),
                        'description' => $blog?->description,
                        'user_id' => $userId,
                        'status' => 'active',
                        'previous_status' => 'inactive',
                        'is_featured' => $blog?->is_featured,
                    ]);
                    Log::info(json_encode($product));
                });
    
                Log::info('blog created');
    
                $product?->media()->where('type', 'image')->delete();
                foreach ($blog?->media as $key => $value) {
                    $product?->media()->create([
                        'path' => $value?->path,
                        'type' => 'image',
                        'is_external' => 1
                    ]);
                }
                $product?->standardTags()->syncWithoutDetaching($tags);
                
                ProductTagsLevelManager::checkProductTagsLevel($product);
                ProductTagsLevelManager::priorityOneTags($product);
                ProductTagsLevelManager::priorityTwoTags($product);
                ProductTagsLevelManager::priorityThree($product);
                ProductTagsLevelManager::priorityFour($product);
            }
        }  

        Log::info("Blogs data importer end");
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
