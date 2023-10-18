<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;

class indexController extends Controller
{
    public function index()
    {


        // function getDistanceBetweenAddresses($addressFrom, $addressTo, $apiKey) {
        //     $fromLatLng = getLatLngFromAddress($addressFrom, $apiKey);
        //     $toLatLng = getLatLngFromAddress($addressTo, $apiKey);

        //     if ($fromLatLng && $toLatLng) {
        //         $distance = calculateDistance($fromLatLng[0], $fromLatLng[1], $toLatLng[0], $toLatLng[1]);
        //         return $distance;
        //     } else {
        //         return null;
        //     }
        // }

        // function getLatLngFromAddress($address, $apiKey) {
        //     $formattedAddress = urlencode($address);
        //     $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$formattedAddress}&key={$apiKey}";

        //     $response = file_get_contents($url);
        //     $data = json_decode($response);

        //     if ($data->status === "OK") {
        //         $latitude = $data->results[0]->geometry->location->lat;
        //         $longitude = $data->results[0]->geometry->location->lng;
        //         return [$latitude, $longitude];
        //     } else {
        //         return null;
        //     }
        // }

        // function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        //     $earthRadius = 6371; // Bán kính trái đất ở đơn vị kilometer

        //     $dLat = deg2rad($lat2 - $lat1);
        //     $dLon = deg2rad($lon2 - $lon1);

        //     $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        //     $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        //     $distance = $earthRadius * $c; // Khoảng cách ở đơn vị kilometer
        //     return $distance;
        // }

        // // Địa chỉ của shipper và khách hàng
        // $shipperAddress = "Địa chỉ shipper"; // Thay thế bằng địa chỉ thực của shipper
        // $customerAddress = "Địa chỉ khách hàng"; // Thay thế bằng địa chỉ thực của khách hàng
        // $apiKey = "AIzaSyCF7a0qKEjxo8wtDTHV_h8hG1VIJiMNDx0"; // Thay thế bằng API key của bạn

        // // Gọi hàm để lấy khoảng cách
        // $distance = getDistanceBetweenAddresses($shipperAddress, $customerAddress, $apiKey);

        // if ($distance !== null) {
        //     echo "Khoảng cách giữa shipper và khách hàng là: " . $distance . " km";
        // } else {
        //     echo "Không thể lấy thông tin vị trí hoặc tính khoảng cách.";
        // }



        // tính distance
        function haversine($lat1, $lon1, $lat2, $lon2) {
            $earthRadius = 6371; // Bán kính trái đất trong đơn vị kilômét
        
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
        
            $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
            $distance = $earthRadius * $c; // Khoảng cách theo đơn vị kilômét
        
            return $distance;
        }
        
        $shipperLatitude = 10.019299;    //10.019299,105.7697339
        $shipperLongitude = 105.7697339;
        $customerLatitude = 10.0190568;  //10.0190568,105.7472808
        $customerLongitude = 105.7472808;
        
        $distance = haversine($shipperLatitude, $shipperLongitude, $customerLatitude, $customerLongitude);
        echo "Khoảng cách giữa shipper và customer là: " . round($distance, 2) . " km";
        

        return view('index');
    }

}