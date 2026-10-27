<?php

/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados ao interno
|
*/

namespace App\Classes;


class GeocodingMaps
{
    public static function getGeocodeData($address) {
        $address = urlencode($address);
        $googleMapUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyBy6UMyq2qpaN2I5womEm0oQJbjNXKwzPU";
        $geocodeResponseData = file_get_contents($googleMapUrl);
        $responseData = json_decode($geocodeResponseData, true);
        if($responseData['status']=='OK') {
            $latitude = isset($responseData['results'][0]['geometry']['location']['lat']) ? $responseData['results'][0]['geometry']['location']['lat'] : "";
            $longitude = isset($responseData['results'][0]['geometry']['location']['lng']) ? $responseData['results'][0]['geometry']['location']['lng'] : "";
            $formattedAddress = isset($responseData['results'][0]['formatted_address']) ? $responseData['results'][0]['formatted_address'] : "";
            if($latitude && $longitude && $formattedAddress) {
                $geocodeData = array();
                array_push(
                    $geocodeData,
                    $latitude,
                    $longitude,
                    $formattedAddress
                );
                return $geocodeData;
            } else {
                return false;
            }
        } else {
            echo "ERROR: {$responseData['status']}";
            return false;
        }
    }

}