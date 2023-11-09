<?php

namespace App\Http\Controllers;
use App\Http\Controllers\FirebaseController;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Menus;
use Illuminate\Support\Facades\Auth;
use App\Models\User;  
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function index()
    {   
       
        if(auth()->check()) {

        $user = auth()->user();

        if (strtolower($user->roles->name_role) == 'shipper') {
            return response()->json(['error' => 'Người dùng có vai trò shipper không thể thực hiện hành động này'], 403);
        }

        $orders = Orders::with('users', 'menus','shipper')->where('user_id',$user->id)->get();

        // Kiểm tra xem có đơn hàng của người dùng hay không
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'Bạn chưa có đơn hàng nào.'], 200);
        }
    
        $transformedOrders = $orders->map(function ($order) {
            $menu = $order->menus; // Lấy menu liên quan đến đơn hàng
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
                'order_status' => $order->order_status,
                //thong tin shipper
                'nameShipper' => optional($shipper)->fullname,
                'phoneShipper' => optional($shipper)->phone
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

            if ($user->roles->name_role =='SHIPPER') {
                return response()->json(['error' => 'Người dùng có vai trò shipper không thể thực hiện hành động này'], 403);
            }
    
            $order = Orders::with('users', 'menus','shipper')->where('user_id',$user->id)->where('id',$id)->first();
    
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            
                $menu = $order->menus; // Lấy menu liên quan đến đơn hàng
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
                    'order_status' => $order->order_status,
                    //thong tin shipper
                    'nameShipper' => optional($shipper)->fullname,
                    'phoneShipper' => optional($shipper)->phone
                ];
            
            return response()->json($transformedOrders); // Trả về mảng chứa thông tin đã biến đổi
            
        }else {
            return response()->json(['error' => 'Bạn cần đăng nhập để xem đơn hàng'], 401);
        }
    }
    
    public function create(Request $request){
        


        if (!Auth::check()) {
            return response()->json(['error' => 'Người dùng chưa đăng nhập'], 401);
        }

        $user = Auth::user();

        if ($user->roles->name_role =='SHIPPER') {
            return response()->json(['error' => 'Người dùng có vai trò shipper không thể thực hiện hành động này'], 403);
        }
        
        // Validate the user input
        $this->validate($request, [
            // "shipper_id" => "required",
            "menu_id" => "required", 
            "quantity" => "required",
        ]);

       
        $orders = new Orders();

        
        
        $orders->user_id =  $user->id;
        // $orders->shipper_id = $request->input('shipper_id');
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

        

        // đặt giá trị mặt định cho order_status
        $orders->order_status = $request->input('order_status', 'đang xử lý');

        $this->createActivityLog(auth()->user()->id, 'Mua hàng'); 
        // Save the data to the database
        $fcm = new FirebaseController();
        $result = $fcm ->sendNotification($user->id, "Trạng Thái Hàng", "Đặt hàng thành công");
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

    public function update(Request $request, $id){
        if (!Auth::check()) {
            return response()->json(['error' => 'Người dùng chưa đăng nhập'], 401);
        }

        $user = Auth::user();

        if ($user->roles->name_role =='SHIPPER') {
            return response()->json(['error' => 'Người dùng có vai trò shipper không thể thực hiện hành động này'], 403);
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

        if (!$request->has('order_status')) {
            return response()->json(['error' => 'Bạn không có quyền cập nhật trạng thái đơn hàng'], 403);
        }
        
        
        if ($orders->order_status === 'Đang xử lý' && $request->input('order_status') === 'Hủy bỏ' && is_null($orders->shipper_id)) {
           
            $orders->order_status = $request->input('order_status');
            $fcm = new FirebaseController();
            $result = $fcm ->sendNotification($user->id, "Trạng Thái Hàng", "Hủy đơn hàng thành công");

            $this->createActivityLog(auth()->user()->id, 'Hủy bỏ'); 
        }
        elseif (!is_null($orders->shipper_id) && $orders->order_status === 'Đang giao' && $request->input('order_status') === 'Đã giao') {
           
               $orders->order_status = $request->input('order_status');
               $fcm = new FirebaseController();
               $result = $fcm ->sendNotification($user->id, "Trạng Thái Hàng", "Đơn hàng đã nhận thành công");
               $this->createActivityLog(auth()->user()->id, 'Đã giao');
        }else {
            return response()->json(['error' => 'Bạn không được thay đổi trạng thái đơn hàng hiện tại '], 403);
        } 
        
        
        
        $orders->save();
    
        return response()->json($orders);
    }   
    
    public function delete(Request $request, $id) {
        if (!Auth::check()) {
            return response()->json(['error' => 'Người dùng chưa đăng nhập'], 401);
        }

        $user = Auth::user();

        if ($user->roles->name_role =='SHIPPER') {
            return response()->json(['error' => 'Người dùng có vai trò shipper không thể thực hiện hành động này'], 403);
        }
        
        // Tìm đối tượng Orders dựa trên $id
        $order = Orders::find($id);
    
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        // Kiểm tra xem trạng thái order_status đã được đặt thành 'Hủy bỏ' hay chưa
        if ($order->order_status !== 'Hủy bỏ') {
            return response()->json(['error' => 'Đơn hàng vẫn đang xử lý bạn k đc phép xóa '], 400);
        }
    
        // Xóa đơn hàng hoàn toàn
        $order->delete();
    
        return response()->json(['message' => 'Order has been deleted successfully']);
    }
    

}
