<?php

namespace App\Http\Controllers\Admin\Settings;

use Inertia\Inertia;
use App\Models\DeliveryZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\NewsPaperDeliveryZoneRequest;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeliveryZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $newspaper = User::whereUserType('newspaper')->firstOrFail();
        $delivery_zone = $newspaper->deliveryZone()->whereNotNull('platform_delivery_Type');
        if(!count($delivery_zone->get()) > 0){
            flash('Please specify atleast one platform delivery type!', 'error');
        }
        return Inertia::render('Settings/DeliveryZone/Index', [
            'type' => $delivery_zone->get(),
            'deliveryZone' => $delivery_zone->first()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'platform_delivery_type' => 'required| unique:delivery_zones'
        ]);
        try{
            DB::beginTransaction();
            $newspaper = User::whereUserType('newspaper')->first();
            $deliveryZone = $newspaper->deliveryZone()->first();
            if($deliveryZone->platform_delivery_type){
                DeliveryZone::create([
                    'model_type'=>'App\Models\User',
                    'model_id'=>$newspaper->id,
                    'zone_type' => $deliveryZone ? $deliveryZone->zone_type : null ,
                    'latitude' => $deliveryZone ? $deliveryZone->latitude : null ,
                    'longitude' => $deliveryZone ? $deliveryZone->longitude : null ,
                    'radius' => $deliveryZone ? $deliveryZone->radius : null ,
                    'polygon' => $deliveryZone->polygon  ? $deliveryZone->polygon : null ,
                    'address' => $deliveryZone->address ? $deliveryZone->address : null ,
                    'fee_type' => 'Delivery fee by mileage',    
                    'platform_delivery_type' => strtolower(str_replace(' ','_',$request->input('platform_delivery_type'))),
                ]);
            }else {
                $deliveryZone->update([
                    'platform_delivery_type' => strtolower(str_replace(' ','_',$request->input('platform_delivery_type'))),
                ]);
            }
            DB::commit();
            flash('News paper delivey Zone created Sucessfully!', 'success');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $newspaper = User::whereUserType('newspaper')->first();
        $delivery_zone = $newspaper->deliveryZone();
        return Inertia::render('Settings/DeliveryZone/Index', [
            'type' => $delivery_zone->get(),
            'deliveryZone' => $delivery_zone->where('id' , $id)->firstOrFail(),
            'locationInfo' => $delivery_zone->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NewsPaperDeliveryZoneRequest $request, $id)
    {
        try {
            
            DB::beginTransaction();
            $newspaper = User::whereUserType('newspaper')->first();
            if($request->input('location')){
                $center = $request->center;  
                $newspaper->deliveryZone()->update([
                    'zone_type' => $request->input('zone_type') ? $request->input('zone_type') : null ,
                    'latitude' => $center ? $center['lat'] : null ,
                    'longitude' => $center ? $center['lng'] : null ,
                    'radius' => $request->input('radius') ? $request->radius : null ,
                    'polygon' => $request->input('polygon')  ? json_encode($request->polygon) : null ,
                    'address' => $request->input('address') ? $request->input('address') : null ,
                ]);
            }
            else {
                $delivery_zone = $newspaper->deliveryZone()->where('id',$id)->firstOrFail();
                if($request->input('status') == 'inactive'){
                    DeliveryZone::where('model_type' , 'App\Models\Business')->where('platform_delivery_type' , $delivery_zone->platform_delivery_type)->update([
                        'platform_delivery_type' => null
                    ]);
                }
                $delivery_zone->update([
                    'mileage_fee' => $request->input('mileage_fee') ? $request->input('mileage_fee') : $delivery_zone->mileage_fee,
                    'extra_mileage_fee' => $request->input('extra_mileage_fee') ? $request->input('extra_mileage_fee') : $delivery_zone->extra_mileage_fee,
                    'mileage_distance' => $request->input('mileage_distance') ? $request->input('mileage_distance') : $delivery_zone->mileage_distance,
                    'fee_type' => $request->input('fee_type') ? $request->input('fee_type') : $delivery_zone->fee_type,
                    'fixed_amount' => $request->input('fixed_amount') ? $request->input('fixed_amount') : $delivery_zone->fixed_amount,
                    'percentage_amount' => $request->input('percentage_amount') ? $request->input('percentage_amount') : $delivery_zone->percentage_amount,
                    'status' => $request->input('status') ? $request->input('status') : $delivery_zone->status,
                ]);
            }
            DB::commit();
            flash('News paper delivey Zone updated Sucessfully!', 'success');
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    // change status
    public function changeStatus($id)
    {
        try {
            $deliveryZone = DeliveryZone::findOrFail($id);
            $deliveryZone->statusChanger()->save();
            flash('Delivery zone status changed succesfully', 'success');
            return redirect()->back();
        }  catch (ModelNotFoundException $e) {
            flash('Unable to find this delivery zone', 'danger');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return \redirect()->back();
        }
    }
}
