<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Menus;
use Illuminate\Support\Facades\Auth;
use App\Models\User;  


class OrdersController extends Controller
{
    public function index()
    {
        $orders = Orders::with('users', 'menus')->get();
 
        $transformedOrders = $orders->map(function ($order) {
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
    
public function tesst1(){

    $user = Auth::user();

    $order = Orders::where('user_id', $user->id)->first();
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

    public function show(Request $request, $id)
    {
        // Tìm đơn hàng dựa trên id
        $order = auth()->$user->id->find($id);
    
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
    
    public function create(Request $request){
        // Validate the user input
        $this->validate($request, [
            "shipper_id" => "required",
            "menu_id" => "required", 
            "quantity" => "required",
        ]);

        // Create a new Orders instance
        $orders = new Orders();

        // Set the values from the request
        $user = Auth::user();
        $orders->user_id =  $user->id;
        $orders->shipper_id = $request->input('shipper_id');
        $orders->menu_id = $request->input('menu_id');
        $orders->quantity = $request->input('quantity');

        // tham chiếu giá của bảng menus từ menu_id khóa ngoại của bảng orders
        $menu = Menus::find($orders->menu_id);

        if (!$menu) {
            return response()->json(['error' => 'Menu not found'], 404);
        }
            
        //tính tổng dựa vào giá của bảng menus và số lượng của order
        $total_price = $menu->price * $orders->quantity;
        $orders->total_price = $total_price;

        //$orders->total_price = $menu->price * $orders->quantity;

        // đặt giá trị mặt định cho order_status
        $orders->order_status = $request->input('order_status', 'đang xử lý');

        // Save the data to the database
        $orders->save();

        return response()->json($orders);
        
    }

    // public function destroy($id)
    // {
    //     $orders = Orders::find($id);
    //     $orders ->delete();
    //     return response()->json($orders);
    // }
    public function delete(Request $request, $id) {
        // Tìm đối tượng Orders dựa trên $id
        $orders = Orders::find($id);
    
        if (!$orders) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        // Kiểm tra xem trạng thái order_status đã được đặt thành 'hủy bỏ' hay chưa
        if ($orders->order_status === 'hủy bỏ') {
            return response()->json(['error' => 'This order has already been canceled.'], 400);
        }
    
        // Thay đổi trạng thái 'order_status' thành 'hủy bỏ'
        $orders->order_status = 'hủy bỏ';
    
        // Lưu thay đổi vào cơ sở dữ liệu
        $orders->save();
    
        return response()->json(['message' => 'Order has been canceled successfully']);
    }

}
