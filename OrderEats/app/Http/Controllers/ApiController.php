<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function calculateDistance(Request $request)
    {
        $lat1 = $request->input('lat1');
        $lon1 = $request->input('lon1');
        $lat2 = $request->input('lat2');
        $lon2 = $request->input('lon2');

        $distance = $this->calcDistance($lat1, $lon1, $lat2, $lon2);

        return response()->json(['distance' => round($distance, 2) . ' km']);
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
