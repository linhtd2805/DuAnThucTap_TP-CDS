<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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

    public function calculateDistance()
    {
        // Giá trị cố định cho lat1 và lon1
        $lat1 = 10.019299; // Thay bằng giá trị latitude mong muốn
        $lon1 = 105.7697339; // Thay bằng giá trị longitude mong muốn

        // Lấy danh sách các User có role là 'shipper' từ Model User
        $shippers = User::where('role_id', 3)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'latitude', 'longitude']);

        // Nếu không có User nào có role là 'shipper', trả về thông báo lỗi
        if ($shippers->isEmpty()) {
            return response()->json(['error' => 'Không có thông tin Shipper.'], 400);
        }

        $minDistance = PHP_INT_MAX; // Khởi tạo khoảng cách nhỏ nhất là giá trị lớn nhất có thể
        $closestShipperId = null;

        foreach ($shippers as $shipper) {
            // Tính khoảng cách giữa lat1, lon1 và vị trí của từng User có role là 'shipper'
            $distance = $this->calcDistance($lat1, $lon1, $shipper->latitude, $shipper->longitude);

            // Cập nhật khoảng cách nhỏ nhất và ID của User (Shipper) gần nhất
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestShipperId = $shipper->id;
            }
        }

        // Tìm thông tin chi tiết của Shipper gần nhất
        $closestShipper = User::find($closestShipperId);

        // Xử lý khoảng cách và trả về kết quả
        if ($minDistance < 1) {
            // Chuyển đổi khoảng cách thành mét nếu dưới 1 km
            $distanceInMeters = $minDistance * 1000;
            return response()->json([
                'Khoảng cách gần nhất của Shipper (ID: ' . $closestShipper->id . ') đến vị trí cửa hàng là: ' => round($distanceInMeters) . ' m'
            ]);
        } else {
            return response()->json([
                'Khoảng cách gần nhất của Shipper (ID: ' . $closestShipper->id . ') đến vị trí cửa hàng là: ' => round($minDistance, 2) . ' km'
            ]);
        }
    }

    // Hàm tính khoảng cách giữa hai điểm trên trái đất
    // Hàm tính khoảng cách giữa hai điểm trên trái đất
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