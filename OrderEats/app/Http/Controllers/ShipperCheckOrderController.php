<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Menus;
use App\Models\User;  

class ShipperCheckOrderController extends Controller
{
    public function index()
    {
        $orders = Orders::with('users', 'menus')->get();

        $filteredOrders = $orders->filter(function ($order) {
            return $order->order_status === 'Đang xử lý' || $order->order_status === 'Đã giao';
        });

        $transformedOrders = $filteredOrders->map(function ($order) {
            $menu = $order->menus; // Lấy menu liên quan đến đơn hàng
            // $totalPrice = $menu->price * $order->quantity; // Tính tổng giá trị đơn hàng
            
            return [
                'id' => $order->id,
                'fullname' => $order->users->fullname,
                'phone' => $order->users->phone,
                'longitude' => $order->users->longitude,
                'latitude' => $order->users->latitude,
                'item_name' => $menu->item_name,
                'description' => $menu->description,
                'price' => $menu->price,
                'quantity' => $order->quantity,
                'total_price' => $order->total_price, 
                // 'total_price' => $totalPrice, // Đưa tổng giá trị vào mảng kết quả
                'order_status' => $order->order_status,
            ];
        });
        
        return response()->json($transformedOrders); // Trả về mảng chứa thông tin đã biến đổi
    }

    public function show(Request $request, $id)
    {
        // Tìm đơn hàng dựa trên id
        $order = Orders::with('users', 'menus')->find($id);
    
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
       
        // Biến đổi dữ liệu đơn hàng
        $transformedOrder = [
            'id' => $order->id,
            'fullname' => $order->users->fullname,
            'phone' => $order->users->phone,
            'longitude' => $order->users->longitude,
            'latitude' => $order->users->latitude,
            'item_name' => $order->menus->item_name,
            'description' => $order->menus->description,
            'price' => $order->menus->price,
            'quantity' => $order->quantity,
            'total_price' => $order->total_price,
            'order_status' => $order->order_status,
        ];
    
        return response()->json($transformedOrder);
    }

    public function update(Request $request, $id){
        // Tìm đối tượng Orders dựa trên $id
        $orders = Orders::find($id);
    
        if (!$orders) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        // Kiểm tra xem request có chứa các trường không được phép sửa không
        $nonEditableFields = ['fullname', 'phone', 'longitude', 'latitude',
                                'item_name','description','price','quantity','total_price'];

        foreach ($nonEditableFields as $field) {
            if ($request->has($field)) {
                return response()->json(['error' => "Bạn không có quyền sửa '$field' này.",
                'message' => "Bạn chỉ được phép xác nhận đơn hàng"], 403);
            }
        }

        // Kiểm tra và cập nhật trường 'order_status' nếu nó được cung cấp trong request
        if ($request->has('order_status') && $request->input('order_status') === 'Đang xử lý') {
            $orders->order_status = $request->input('order_status');
        }else       
            return response()->json(['error' => 'Bạn không có quyền cập nhật trạng thái đơn hàng Đang giao.'], 403);
    
        // Lưu thay đổi vào cơ sở dữ liệu
        $orders->save();
    
        return response()->json($orders);
    }   
 
}

