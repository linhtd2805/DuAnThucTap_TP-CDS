<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Bán kính trái đất trong kilômét
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c; // Khoảng cách giữa hai điểm trong kilômét
    
    return $distance;
}

// Ví dụ về sử dụng hàm để tính khoảng cách giữa khách hàng và shipper
$customerLatitude = 40.7128; // Vĩ độ của khách hàng
$customerLongitude = -74.0060; // Kinh độ của khách hàng
$shipperLatitude = 34.0522; // Vĩ độ của shipper
$shipperLongitude = -118.2437; // Kinh độ của shipper

$distance = calculateDistance($customerLatitude, $customerLongitude, $shipperLatitude, $shipperLongitude);
echo "Khoảng cách giữa khách hàng và shipper là: " . number_format($distance, 2) . " km";
