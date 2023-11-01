<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Menus;
use App\Models\User;  
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class ShipperCheckOrderController extends Controller
{
    public function index()
    {
        if(auth()->check()) {

            $user = auth()->user();

            if ($user->roles->name_role =='USER') {
                return response()->json(['error' => 'Người dùng có vai trò USER không thể thực hiện hành động này'], 403);
            }

        $orders = Orders::with('users', 'menus','shipper')->get();

         // Kiểm tra xem có đơn hàng của người dùng hay không
         if ($orders->isEmpty()) {
            return response()->json(['message' => 'Bạn chưa có đơn hàng nào.'], 200);
        }
    
        $transformedOrders = $orders->map(function ($order) {
            $menu = $order->menus; // Lấy menu liên quan đến đơn hàng
            // $totalPrice = $menu->price * $order->quantity; // Tính tổng giá trị đơn hàng
            $shipper = $order->shipper;


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
                'nameShipper' => optional($shipper)->fullname,
                'phoneShipper' => optional($shipper)->phone,
            ];
        });
        
        return response()->json($transformedOrders); // Trả về mảng chứa thông tin đã biến đổi
    
        }else {
            return response()->json(['error' => 'Bạn cần đăng nhập để xem đơn hàng'], 401);
        }
    }
    public function show(Request $request, $id)
    {
        if(auth()->check()) {

            $user = auth()->user();

            if ($user->roles->name_role =='USER') {
                return response()->json(['error' => 'Người dùng có vai trò USER không thể thực hiện hành động này'], 403);
            }
        // Tìm đơn hàng dựa trên id
        $order = Orders::with('users', 'menus','shipper')->find($id);
        
    
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        $shipper = $order->shipper;
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
            'nameShipper' => optional($shipper)->fullname,
            'phoneShipper' => optional($shipper)->phone,
        ];
    
        return response()->json($transformedOrder);
        }else {
            return response()->json(['error' => 'Bạn cần đăng nhập để xem đơn hàng'], 401);
        }
    }
    public function update(Request $request, $id){

        if (!Auth::check()) {
            return response()->json(['error' => 'Người dùng chưa đăng nhập'], 401);
        }

        $user = Auth::user();

        if ($user->roles->name_role =='USER') {
            return response()->json(['error' => 'Người dùng có vai trò USER không thể thực hiện hành động này'], 403);
        }

        // Tìm đối tượng Orders dựa trên $id
        $orders = Orders::find($id);
        
        if (!$orders) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        // Kiểm tra xem request có chứa các trường không được phép sửa không
        $nonEditableFields = ['id','fullname', 'phone', 'longitude', 'latitude',
                                'item_name','description','price','quantity','total_price','nameShipper','phoneShipper'];

        foreach ($nonEditableFields as $field) {
            if ($request->has($field)) {
                return response()->json(['error' => "Bạn không có quyền sửa '$field' này.",
                'message' => "Bạn chỉ được phép xác nhận đơn hàng"], 403);
            }
        }

       
        $shipper = Orders::findOrFail($id); // Thay $order_id bằng ID của đơn hàng cần cập nhật

        $loggedInShipperId = $user->id;

        if (!$request->has('order_status')) {
            return response()->json(['error' => 'Bạn không có quyền cập nhật trạng thái đơn hàng'], 403);
        }
        
        if (is_null($orders->shipper_id)) {
            
            $orders->shipper_id = $user->id; // id người giao hàng

        } elseif ($shipper->shipper_id !== $loggedInShipperId) {
            return response()->json(['error' => 'Đơn hàng này đã có Shipper nhận bạn không được thay đổi'], 403);
        }
        
        if ($orders->order_status === 'Đang xử lý' && $request->input('order_status') === 'Đang giao') {
            $orders->order_status = $request->input('order_status');
            $this->createActivityLog(auth()->user()->id, 'Đang giao');

        } elseif ($orders->order_status === 'Đang giao' && $request->input('order_status') === 'Hủy bỏ') {
            $orders->order_status = $request->input('order_status');
            $this->createActivityLog(auth()->user()->id, 'Hủy đơn hàng');
            
        } else {
            return response()->json(['error' => 'Bạn chỉ có thể xác nhận đơn hàng hoặc có thể hủy bỏ đơn hàng đang giao'], 403);
        }
        
        $orders->save();

        
       

        return response()->json($orders);
    }   

    private function createActivityLog($userId, $activityType)
    {
        DB::table('activity_logs')->insert([
            'user_id' => $userId,
            'activityType' => $activityType,
        ]);
    }
 
}

