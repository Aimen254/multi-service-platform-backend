<?php

namespace Modules\News\Jobs;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\User;
use App\Models\Product;
use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ModuleSessionManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NewsScraper implements ShouldQueue
{
    // implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $totalRecords = 1;
    protected $limit = 50;
    protected $page = 1;
    protected $totalPages = 1;

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
        Log::alert("News Scraper running");
        $namesList = 'The-Advocate';
        $user = User::where('user_type', 'admin')->first();
        while ($this->totalPages >= $this->page) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer 1b7a1022-c21a-436f-a83d-56a9f249bdaf'
            ])->get('http://178.128.168.240:3020/api/v1/products', [
                'limit' => $this->limit,
                'page' => $this->page,
                'store' => $namesList
            ]);
            $this->totalRecords = $response->object()->meta->total;
            $this->totalPages = ceil($this->totalRecords / $this->limit);
            foreach ($response->object()->data as $product) {
                ModuleSessionManager::setModule('news', \false);
                try {
                    DB::beginTransaction();
                    //Creating or upadting product\
                    $newProduct = $this->createOrUpdateProduct($product, $user);
                    if ($newProduct) {
                        // product images
                        $this->saveProductImages($product, $newProduct);
                        // author images
                        $this->saveAuthorImages($product, $newProduct);
                        ProductTagsLevelManager::priorityOneTags($newProduct);
                        ProductTagsLevelManager::checkProductTagsLevel($newProduct);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    Log::error([
                        'message' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                        'store' => isset($business) ? $business->name : '',
                        'product' => isset($product) ? $product->title : ''
                    ]);
                    DB::rollBack();
                }
            }

            $this->page++;
            Log::alert('Page #' . $this->page);
        }
    }

    /**
     * create new product.
     *
     * @param  $product, $business
     * @return $newProduct
     */
    private function createOrUpdateProduct($product, $user)
    {
        $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
        $businessOwner1 = User::whereEmail('businessOwner1@interapptive.com')->first();
        $businessOwner2 = User::whereEmail('businessOwner2@interapptive.com')->first();

        $businessOwnerIds = [$businessOwner->id, $businessOwner1->id, $businessOwner2->id];
        $moduleTag = StandardTag::where('slug', 'news')->first();
        $allTag = StandardTag::where('slug', 'all')->first();
        ModuleSessionManager::setModule('news', \false);
        $chekHierarcy = $this->checkProductHierarchy($product, $moduleTag, $allTag);
        if ($chekHierarcy) {
            $newProduct = Product::updateOrCreate(
                ['external_id' => $product->product_id],
                [
                    'user_id' => $businessOwnerIds[array_rand($businessOwnerIds)],
                    'name' => $product->title ?? '',
                    'description' => $product->description ?? '',
                    'author' => $product->author->name ?? '',
                    'published_date' => $product->published_date ? Carbon::parse($product->published_date) : null
                ]
            );
            $newProduct->standardTags()->syncWithOutDetaching($moduleTag->id);
            if ($allTag) {
                $newProduct->standardTags()->syncWithOutDetaching($allTag->id);
            }
            $this->saveProductTags($product, $newProduct, $moduleTag);
            return $newProduct;
        }
    }


    private function saveProductImages($product, $newProduct)
    {
        $newProduct->media()->where('type', 'image')->delete();
        foreach ($product->images as $index => $image) {
            if ($index >= 7) {
                break;
            }
            if (
                \env('APP_ENV') == 'local'
            ) {
                $newProduct->media()->create([
                    'path' => $image->image,
                    'type' => 'image',
                    'is_external' => 1
                ]);
            } else {
                $imageExists = $this->checkRemoteFile($image->image);
                if ($imageExists) {
                    $newProduct->media()->create([
                        'path' => $image->image,
                        'type' => 'image',
                        'is_external' => 1
                    ]);
                }
            }
        }
        return true;
    }

    private function saveAuthorImages($product, $newProduct)
    {
        $newProduct->media()->where('type', 'author')->delete();

        if (
            \env('APP_ENV') == 'local'
        ) {
            $newProduct->media()->create([
                'path' => $product->author->image,
                'type' => 'author',
                'is_external' => 1
            ]);
        } else {
            $imageExists = $this->checkRemoteFile($product->author->image);
            if ($imageExists) {
                $newProduct->media()->create([
                    'path' => $product->author->image,
                    'type' => 'author',
                    'is_external' => 1
                ]);
            }
        }
        return true;
    }

    /**
     * check product image url.
     *
     * @param  $url
     * @return boolean
     */
    private function checkRemoteFile($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code == 200 ? \true : \false;
    }

    /**
     * create new product tags.
     * @param  $scrapeProduct, $newProduct
     */
    private function saveProductTags($product, $newProduct, $moduleTag)
    {
        $newTagsId = [];
        foreach ($product->tags as $key => $tag) {
            if (Str::slug($tag) != $moduleTag->slug) {
                $newTag = Tag::where('slug', orphanTagSlug($tag))->first();
                if (!$newTag) {
                    $newTag = Tag::create([
                        'slug' => orphanTagSlug($tag),
                        'name' => $tag,
                        'priority' => 4,
                    ]);
                } else {
                    $newTag->update([
                        'slug' => orphanTagSlug($tag),
                        'name' => $tag,
                        'priority' => 4,
                    ]);
                }
                \array_push($newTagsId, $newTag->id);
                $newProduct->tags()->syncWithoutDetaching($newTag->id);
                //Checking if tag is mapped and already attached to product as standard tag
                $this->mappedTagsAssignment($newTag->id, $newProduct);
            }
        }
        ProductTagsLevelManager::priorityFour($newProduct, $newTagsId);
        return true;
    }

    private function mappedTagsAssignment($tag_id, $product)
    {
        $tag = Tag::find($tag_id);
        if ($tag) {
            $mappedStandardTags = $tag->standardTags_()->get();
            if ($mappedStandardTags->count() > 0) {
                foreach ($mappedStandardTags as $standardTag) {
                    $product->standardTags()->syncWithOutDetaching($standardTag->id);
                    if ($standardTag->type == 'attribute') {
                        ProductTagsLevelManager::priorityTwoTags($product);
                    } else if ($standardTag->priority == 4) {
                        $tags[] = $tag->id;
                        ProductTagsLevelManager::priorityFour($product, $tags);
                    }
                }
            } else {
                $standardTag = StandardTag::where('name', $tag->name)->first();
                if ($standardTag) {
                    $tag->update([
                        'priority' => $standardTag->priority,
                        'is_show' => \true
                    ]);
                    $tag->standardTags_()->syncWithoutDetaching($standardTag->id);
                    $product->standardTags()->syncWithOutDetaching($standardTag->id);
                    if ($standardTag->type == 'attribute') {
                        ProductTagsLevelManager::priorityTwoTags($product);
                    }
                } else {
                    $tags[] = $tag->id;
                    ProductTagsLevelManager::priorityFour($product, $tags);
                }
            }
        }
    }

    private function checkProductHierarchy($product, $moduleTag, $allTag)
    {
        $standardTagIds = StandardTag::whereIn('name', $product->tags)->pluck('id')->toArray();
        $chekLevelTwo = TagHierarchy::where('L1', $moduleTag->id)->whereIn('L2', $standardTagIds)->pluck('L2')->toArray();
        $checkLevelThree = TagHierarchy::where('L1', $moduleTag->id)->whereIn('L2', $chekLevelTwo)->whereIn('L3', $standardTagIds)->whereHas('standardTags', function ($query) use ($allTag) {
            $query->where('id', $allTag->id);
        })->get();
        if ($checkLevelThree->count() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
