<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{

    //trường hợp trên 1km
    //  $lat1 = $request->input('lat1'); //10.019299, 105.7697339, 10.0190568, 105.7472808
    // $lon1 = $request->input('lon1');
    // $lat2 = $request->input('lat2');
    // $lon2 = $request->input('lon2');

    //trường hợp dưới 1km chuyển về mét
    // $lat1 = 10.019299;
    // $lon1 = 105.7697339;
    // $lat2 = 10.019305;
    // $lon2 = 105.7697338;

    public function calculateDistance(Request $request)
    {
        // Kiểm tra xem các giá trị lat1, lon1, lat2, lon2 có tồn tại trong yêu cầu không
        if (!$request->has(['lat1', 'lon1', 'lat2', 'lon2'])) {
            return response()->json(['error' => 'Thiếu thông tin tọa độ.'], 400);
        }

        // Lấy giá trị lat1, lon1, lat2, lon2 từ yêu cầu
        $lat1 = $request->input('lat1');
        $lon1 = $request->input('lon1');
        $lat2 = $request->input('lat2');
        $lon2 = $request->input('lon2');

        // Kiểm tra xem các giá trị lat1, lon1, lat2, lon2 có đúng định dạng số không
        if (!is_numeric($lat1) || !is_numeric($lon1) || !is_numeric($lat2) || !is_numeric($lon2)) {
            return response()->json(['error' => 'Các giá trị tọa độ không hợp lệ.'], 400);
        }

        // Tính khoảng cách
        $distance = $this->calcDistance($lat1, $lon1, $lat2, $lon2);

        // Kiểm tra xem tính toán khoảng cách có thành công không
        if ($distance === false) {
            return response()->json(['error' => 'Không thể tính khoảng cách.'], 500);
        }

        // Xử lý khoảng cách và trả về kết quả
        if ($distance < 1) {
            // Chuyển đổi khoảng cách thành mét nếu dưới 1 km
            $distanceInMeters = $distance * 1000;
            return response()->json(['Khoảng cách giữa shipper và khách hàng là: ' => round($distanceInMeters) . ' m']);
        } else {
            return response()->json(['Khoảng cách giữa shipper và khách hàng là: ' => round($distance, 2) . ' km']);
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