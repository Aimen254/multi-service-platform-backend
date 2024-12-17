<?php

namespace App\Models;

use App\Enums\CalendarEventStatus;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Database\Eloquent\Model;
use Modules\Events\Entities\CalendarEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module_id',
        'model_id',
        'model_type',
        'type'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'model_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function model()
    {
        return $this->morphTo();
    }

    public function addToliked($params)
    {
        if ($params->type == 'product') {
            Product::where('id', $params->{$params->type . '_id'})->with('standardTags', function ($query) {
                $query->where('type', 'module');
            })->firstOrFail();
        }

        StandardTag::where('id', request()->input('module_id'))->first();

        $item = $this->where('user_id', $params->user()->id)
            ->where('model_id', $params->{$params->type . '_id'})
            ->when($params->type == 'user', function ($query) use ($params) {
                $query->where('module_id', $params->module_id);
            })->first();

        if ($item) {
            $item->delete();
            return ['message' => 'The like has been removed.', 'flag' => false];
        }

        $this->create([
            'user_id' => $params->user()->id,
            'module_id' => $params?->module_id ?? null,
            'model_id' => $params->{$params->type . '_id'},
            'model_type' => $params->type == 'tag' ? 'App\Models\StandardTag' : 'App\Models\\' . ucfirst($params->type)
        ]);

        return ['message' => 'You have like this post', 'flag' => true];
    }

    public function standardTags()
    {
        return $this->belongsTo(StandardTag::class, 'model_id');
    }
}
