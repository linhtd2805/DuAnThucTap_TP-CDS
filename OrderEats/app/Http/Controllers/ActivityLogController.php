<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\Menus;
use App\Models\Orders;

class ActivityLogController extends Controller
{
    public function buy(Request $request)
    {
        // Đọc thông tin sản phẩm và số lượng từ request
        $menu_id = $request->input('menu_id');
        // $quantity = $request->input('quantity');

        // Kiểm tra sự tồn tại của sản phẩm và số lượng trong kho hàng
        $menus = Menus::find($menu_id);

        if (!$menus) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        // if ($menus->quantity < $quantity) {
        //     return response()->json(['message' => 'Sản phẩm không đủ số lượng'], 400);
        // }

        // Tạo đơn hàng
        $order = Orders::where('id', 3)->first();
        

        
        // return response()->json($order);
        $order->user_id=auth()->user()->id;
    
        $order->total_price=$order->quantity*$menus->price;
        $order->order_status = 'Đang xử lý';
        $order->save();

        // Giảm số lượng sản phẩm trong kho hàng
        // $product->quantity -= $quantity;
        $menus->save();

        // Thêm hoạt động log
        $this->createActivityLog(auth()->user()->id, 'Mua hàng'); 

        return response()->json(['message' => 'Đã đặt hàng thành công']);
    }

    private function createActivityLog($userId, $activityType)
    {
        DB::table('activity_logs')->insert([
            'user_id' => $userId,
            'activityType' => $activityType,
        ]);
    }
}
