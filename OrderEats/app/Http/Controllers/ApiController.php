<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ApiController extends Controller
{
    public function getNearestShipper(Request $request)
    {
        // Tọa độ cố định của người dùng
        $userLatitude = 10.019299; // Thay thế bằng latitude của người dùng
        $userLongitude = 105.7472808; // Thay thế bằng longitude của người dùng

        // Tìm shipper có khoảng cách gần nhất
        $nearestShipper = User::selectRaw('*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance')
            ->where('role_id', '=', 3) // Thay 'shipper_role_id' bằng ID của vai trò shipper trong CSDL
            ->orderBy('distance', 'asc')
            ->limit(1)
            ->setBindings([$userLatitude, $userLongitude, $userLatitude])
            ->first();

        return response()->json($nearestShipper);
    }

}