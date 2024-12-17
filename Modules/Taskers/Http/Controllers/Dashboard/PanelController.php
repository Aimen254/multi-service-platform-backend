<?php

namespace Modules\Taskers\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;

class PanelController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $module = StandardTag::where('slug', 'taskers')->first();
        $totalTaskers = Product::whereRelation('standardTags', 'id', $module->id)->count();
        return Inertia::render('Taskers::Dashboard', [
            'totalTaskers' => $totalTaskers
        ]);
    }
}
