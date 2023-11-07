<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menus;
use App\Models\Orders;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function adminIndex()
    {
        // Kiểm tra quyền của người dùng, ví dụ: chỉ cho phép admin truy cập
        if (auth()->user()->isAdmin()) {
            $activity_log = ActivityLog::all();
            return response()->json($activity_log);
        } else {
            return response()->json(['message' => 'Access denied.'], 403);
        }
    }
    // Lấy lịch sử đơn hàng của người dùng hiện tại
    public function userIndex()
    {
        $user = Auth::user(); // Lấy người dùng đã đăng nhập

        // Kiểm tra xem người dùng có tồn tại không
        if (!$user) {
            return response()->json(['message' => 'Người dùng không tồn tại'], 404);
        }

        // Lấy lịch sử đơn hàng của người dùng
        $activity_log = ActivityLog::where('user_id', $user->id)->get();

        // Trả về kết quả dưới dạng JSON
        return response()->json($activity_log);
    }

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
