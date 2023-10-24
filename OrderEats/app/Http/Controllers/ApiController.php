<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function calculateDistance(Request $request)
    {
        //trường hợp trên 1km
        //  $lat1 = $request->input('lat1'); //10.019299, 105.7697339, 10.0190568, 105.7472808
        // $lon1 = $request->input('lon1');
        // $lat2 = $request->input('lat2');
        // $lon2 = $request->input('lon2');
        
        //trường hợp dưới 1km chuyển về mét
        $lat1 = 10.019299;
        $lon1 = 105.7697339;
        $lat2 = 10.019305;
        $lon2 = 105.7697338;
        $distance = $this->calcDistance($lat1, $lon1, $lat2, $lon2);

        if ($distance < 1) {
            // Chuyển đổi khoảng cách thành mét nếu dưới 1 km
            $distanceInMeters = $distance * 1000;
            return response()->json(['distance' => round($distanceInMeters) . ' m']);
        } else {
            return response()->json(['distance' => round($distance, 2) . ' km']);
        }
    }

    private function calcDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Bán kính trái đất trong đơn vị kilômét

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // Khoảng cách theo đơn vị kilômét

        return $distance;
    }

}
