<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait CustomerStreetAddress
{
    /**
     * To make street address
     *
     * @param $address
     */

    public static function streetAddress($address)
    {
        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $address->latitude . "," . $address->longitude . "&key=" . env('GOOGLE_MAPS_KEY'));
        $response['results'];
        $results =  $response->json();
        $results = $results['results'];
        $street_address = '';
        if (count($results)) {
            foreach ($results[0]['address_components'] as $result) {
                if ($result['types'][0] == 'street_number') {
                    $street_address .= $result['long_name'];
                } else if ($result['types'][0] == 'route') {
                    $street_address .= ' ' . $result['long_name'];
                }
            }
            $address->update([
                'street_address' => $street_address
            ]);
        }
    }
}
