<?php

namespace Modules\Classifieds\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\StandardTag;
use Illuminate\Http\Request;
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
        $module = StandardTag::where('slug', Product::MODULE_MARKETPLACE)->first();
        $totalClassifieds = Product::whereRelation('standardTags', 'id', $module->id)->count();
        return Inertia::render('Classifieds::Dashboard', [
            'totalClassifieds' => $totalClassifieds
        ]);
    }
}
