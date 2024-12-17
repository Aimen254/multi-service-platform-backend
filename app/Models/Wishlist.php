<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Business;
use App\Models\StandardTag;
use App\Enums\CalendarEventStatus;
use Illuminate\Database\Eloquent\Model;
use Modules\Events\Entities\CalendarEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module_id',
        'model_id',
        'model_type',
        'type'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();
        /**
         * Handle the wishlist "created" event. and save calendar event
         *
         * @return void
         */
        static::created(function (Wishlist $wishlist) {
            if (request()->type === 'product' && StandardTag::where('id', request()->input('module_id'))->first()?->slug === 'events') {
                $product = $wishlist->model()->with('events')->first();
                CalendarEvent::create([
                    'product_id' => $product?->id,
                    'user_id' => $wishlist?->user_id,
                    'module_id' => $wishlist?->module_id,
                    'title' => $product?->name,
                    'status' => CalendarEventStatus::Going,
                    'date' => Carbon::parse($product?->events?->event_date)->toDateTimeString(),
                ]);
            }
        });

        /**
         * Handle the Wishlist "deleting" event and delete calender event
         *
         * @param  \App\Models\Wishlist  $wishlist
         * @return void
         */

        static::deleted(function (Wishlist $wishlist) {
            if (request()->type === 'product' && StandardTag::where('id', request()->input('module_id'))->first()?->slug === 'events') {
                CalendarEvent::where([
                    'product_id' => $wishlist->model_id,
                    'user_id' => $wishlist->user_id,
                    'module_id' => $wishlist->module_id
                ])?->delete();
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'model_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'model_id');
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoriteUser()
    {
        return $this->belongsTo(User::class, 'model_id');
    }

    public function addToWishList($params)
    {
        $message = '';
        $addMessage = '';
        $deleteMessage = '';
        if ($params->type == 'product') {
            $productModule = Product::where('id', $params->{$params->type . '_id'})->with('standardTags', function ($query) {
                $query->where('type', 'module');
            })->firstOrFail();
            switch ($productModule->standardTags[0]->slug) {
                case 'news':
                    $message = 'News';
                    break;
                case 'posts':
                    $message = 'post';
                    break;
                case 'obituaries':
                    $message = 'obituaries';
                    break;
                case 'blogs':
                    $message = 'Blog';
                    break;
                case 'recipes':
                    $message = 'Recipe';
                    break;
                case 'marketplace':
                    $message = 'Item';
                    break;
                case 'taskers':
                    $message = 'Task';
                    break;
                case 'services':
                    $message = 'Service';
                    break;
                case 'employment':
                    $message = 'Position';
                    break;
                case 'notices':
                    $message = 'Notice';
                    break;
                case 'government':
                    $message = 'post';
                    break;
                case 'real-estate':
                    $message = "Listing";
                    break;
                default:
                    $message = 'Product';
            }
        }

        $module = StandardTag::where('id', request()->input('module_id'))->first();
        $modelType = "App\Models\\". ucfirst($params->type);
        $item = $this->where('user_id', $params->user()->id)
            ->where('model_id', $params->{$params->type . '_id'})->where('model_type', $modelType)
            ->when($params->type == 'user', function ($query) use ($params) {
                $query->where('module_id', $params->module_id);
            })->first();

        if ($item) {
            if ($message == 'obituaries') {
                $deleteMessage = 'Removed from loving memory';
            } elseif ($message == 'post') {
                $deleteMessage = 'Removed from bookmarks.';
            } elseif ($module?->slug == 'government' && $params->type == 'business') {
                $deleteMessage = 'Removed from favorite';
            } else {
                $deleteMessage = $message . ' Removed from bookmarks';
            }
            $item->delete();
            return ['message' => $deleteMessage, 'flag' => false];
        }

        if ($message == 'obituaries') {
            $addMessage = 'Added to loving memory';
        } elseif ($params->tag_type == 'makes') {
            $addMessage = 'Popular make added to bookmarks';
        } elseif ($params->tag_type == 'body_styles') {
            $addMessage = 'Body style added to bookmarks';
        } elseif ($message == 'post') {
            $addMessage = 'Added to bookmarks';
        } elseif ($params->type == 'user' || $params->type == 'business') {
            if ($params->type == 'business') {
                if ($module?->slug == 'government') {
                    $addMessage = 'Added to favorite';
                } else {
                    $addMessage = 'Added to bookmarks';
                }
            } else {
                $addMessage = 'Added to bookmarks';
            }
        } else {
            $addMessage = $message . ' added to bookmarks';
        }

        $this->create([
            'user_id' => $params->user()->id,
            'module_id' => $params?->module_id ?? null,
            'model_id' => $params->{$params->type . '_id'},
            'model_type' => $params->type == 'tag' ? 'App\Models\StandardTag' : 'App\Models\\' . ucfirst($params->type)
        ]);

        return ['message' => $addMessage, 'flag' => true];
    }

    public function standardTags()
    {
        return $this->belongsTo(StandardTag::class, 'model_id');
    }
}
