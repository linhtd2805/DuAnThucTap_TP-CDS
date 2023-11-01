<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\Orders;

class ShipperController extends Controller
{
    public function receiveOrder(Request $request)
    {
        // Đọc thông tin đơn hàng và shipper từ request
        $order_id = $request->input('order_id');

        // Kiểm tra sự tồn tại của đơn hàng
        $order = Orders::find($order_id);

        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại'], 404);
        }

        // Kiểm tra xem đơn hàng đã được nhận chưa
        if ($order->order_status !== 'Đang xử lý') {
            return response()->json(['message' => 'Đơn hàng đã được nhận hoặc đã hủy'], 400);
        }

        // Đánh dấu đơn hàng đã được nhận
        $order->shipper_id = auth()->user()->id;
        $order->order_status = 'Đã giao';
        $order->save();

        // Thêm hoạt động log
        $this->createActivityLog(auth()->user()->id, 'Nhận đơn hàng');

        return response()->json(['message' => 'Đã nhận đơn hàng thành công']);
    }

    private function createActivityLog($userId, $activityType)
    {
        DB::table('activity_logs')->insert([
            'user_id' => $userId,
            'activityType' => $activityType,
        ]);
    }
}
