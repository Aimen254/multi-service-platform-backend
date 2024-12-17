<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait BusinessStreetAddress
{
    /**
     * To make street address
     *
     * @param $business
     */
    public static function streetAddress($business)
    {
        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $business->latitude . "," . $business->longitude . "&key=" . env('GOOGLE_MAPS_KEY'));
        $address = $response['results'];
        $results =  $response->json();
        $results = $results['results'];
        if (\count($results) > 0) {
            $street_address = '';
            foreach($results[0]['address_components'] as $result) {
                if($result['types'][0] == 'street_number') {
                    $street_address .=$result['long_name'];
                } else if($result['types'][0] == 'route') {
                    $street_address .= ' '.$result['long_name'];
                }
            }
            $business->update([
                'street_address' => $street_address
            ]);
        }
    }
}
