<?php
namespace App\Transformers;

use stdClass;
use App\Transformers\Transformer;
use App\Transformers\UserTransformer;
use App\Transformers\ProductTransformer;
use App\Transformers\StandardTagTransformer;

class VehicleTransformer extends Transformer
{
    public function transform($vehicle, $options = null)
    {
        $data = [
            'id' => (int) $vehicle->id,
            'type' => (string) $vehicle->type,
            'year' => $vehicle->year,
            'trim' => (string) $vehicle->trim,
            'mileage' => (string) $vehicle->mileage,
            'vin' => (string) $vehicle->vin,
            'mpg' => (string) $vehicle->mpg,
            'stock_no' => (string) $vehicle->stock_no,
            'sellers_notes' => (string) $vehicle->sellers_notes,
            'engine' => (string) $vehicle->engine,
            'transmission' => (string) $vehicle->transmission,
            'drivetrain' => (string) $vehicle->drivetrain,
            'fuel_type' => (string) $vehicle->fuel_type,
            'model' => $vehicle->relationLoaded('model') && $vehicle->model ? (new StandardTagTransformer)->transform($vehicle->model) : [],
            'maker' => $vehicle->relationLoaded('maker') && $vehicle->maker ? (new StandardTagTransformer)->transform($vehicle->maker) : [],
            'created_at'=> timeFormat($vehicle->created_at)
        ];
        return $data;
    }
}
