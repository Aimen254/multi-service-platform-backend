<?php

namespace Modules\Retail\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use App\Enums\Business\Settings\DeliveryType;

class PanelController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id = null)
    {
        $users = User::whereUserType('customer');
        $businessOwners = User::whereUserType('business_owner');
        $totalUsers = [
            [
                'name' => $users->count() > 0 ? $users->first()->user_type : 'customer',
                'count' => $users->active()->count()
            ],
            [
                'name' => $businessOwners->count() > 0 ? $businessOwners->first()->user_type : 'business_owner',
                'count' => $businessOwners->active()->count()
            ],
        ];

        $data = [ 
            'totalUsers' => $totalUsers,
        ];

        if(auth()->user()->role->name == "business_owner") {
            $deliveryZoneErrors = auth()->user()->businesses()->where('uuid' , $id)->select('name')
                ->whereHas('deliveryZone' , function($query) {
                    $query->where('delivery_type' , DeliveryType::PlatformDelivery)
                        ->whereNull('platform_delivery_type');
                })->first();
            if ($deliveryZoneErrors)
                $data['deliveryZoneErrors'] = $deliveryZoneErrors->name;
        }
        return Inertia::render('Retail::Dashboard', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('retail::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('retail::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('retail::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
