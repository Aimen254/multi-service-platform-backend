<?php

namespace Modules\Retail\Http\Controllers\Dashboard\Business;

use App\Models\User;
use Inertia\Inertia;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Traits\StripeSubscription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Business;
use Modules\Retail\Entities\DeliveryZone;
use Illuminate\Contracts\Support\Renderable;
use App\Enums\Business\Settings\DeliveryType;
use Modules\Retail\Http\Requests\DeliveryZoneRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeliveryZoneController extends Controller
{
    use StripeSubscription;
    public StripeClient $stripeClient;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($moduleId, $businessUuid)
    {
        $business = Business::with(['deliveryZone', 'banner', 'logo', 'thumbnail'])->where('uuid', $businessUuid)->first();
        $delivery_zone = $business->deliveryZone;
        $polygon = [];
        if ($delivery_zone) {
            $polygon = json_decode($delivery_zone->polygon);
        }
        $newspaper = User::whereUserType('newspaper')->first();
        return Inertia::render('Retail::Business/DeliveryZone/Index', [
            'business' => $business,
            'polygon' => $polygon,
            'platformType' => $newspaper->deliveryZone()->where('status', 'active')->get(),
            'subscriptionDeliveryFlag' => $this->checkDeliveryPermission($business)
        ]);
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
    public function update(DeliveryZoneRequest $request, $moduleId, $businessId, $id)
    {
        try {
            $center = $request->center;
            $polygon = json_encode($request->polygon);
            DB::beginTransaction();
            $delivery_zone = DeliveryZone::findOrFail($id);
            $delivery_zone->update([
                'delivery_type' => $request->delivery_type,
                'zone_type' => $request->input('zone_type')
                    ? $request->zone_type : $delivery_zone->zone_type,
                'mileage_fee' => $request->mileage_fee,
                'extra_mileage_fee' => $request->extra_mileage_fee,
                'mileage_distance' => $request->mileage_distance,
                'fee_type' => $request->fee_type == null && $request->delivery_type == "Platform delivery" ? 'Delivery fee by mileage' : $request->fee_type,
                'fixed_amount' => $request->fixed_amount,
                'percentage_amount' => $request->percentage_amount,
                'latitude' => $center ? $center['lat'] : null,
                'longitude' => $center ? $center['lng'] : null,
                'radius' => $request->radius ? $request->radius : null,
                'polygon' => $polygon ? $polygon : null,
                'platform_delivery_type' => $request->input('platform_delivery_type')
            ]);
            DB::commit();
            flash('Delivey Zone updated Sucessfully!', 'success');
            return \redirect()->back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this delivery zone', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
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

    /**
     * To create settings of deliveryZone.
     *
     * @param $deliveryZone
     */
    private function deliveryTypeBasedSettings(DeliveryZone $deliveryZone)
    {
        $data = [];
        switch (DeliveryType::coerce(str_replace(' ', '', ucwords($deliveryZone->delivery_type)))->value) {
            case 0:
            case 1:
                $data = [
                    'mileage_fee' => NULL,
                    'extra_mileage_fee' => NULL,
                    'mileage_distance' => NULL,
                    'fee_type' => NULL,
                ];
                break;
        }
        $deliveryZone->update($data);
    }
}
