<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reviews;
use App\Models\Orders;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    //All Data
    public function index()
    {
        $reviews = Reviews::with('orders')->get();
        return response()->json($reviews);
    }

    // Hiển thị theo id
    public function show($id)
    {
        $reviews = Reviews::with('orders')->find($id);    
        if (!$reviews) {
            return response()->json(['message' => 'Không tìm thấy đánh giá, nhập lại id'], 404);
        }
        return response()->json($reviews);
    }

    // Thêm 
    public function store(Request $request)
    {   
        try {
            // Xác thực inputs
            if (($errors = $this->doValidate($request)) && count($errors) > 0) {
                return response()->json(['message' => 'Không bỏ trống !', 'errors' => $errors], 500);
            }

            $user = auth()->user();
            $deliveredOrders = Orders::where('user_id', $user->id)
                ->where('order_status', 'Đã giao')
                ->pluck('id');

            if ($deliveredOrders->contains($request->get("order_id"))) {
                $reviews = new Reviews();
                $reviews->order_id = $request->get("order_id");
                $reviews->rating = $request->get('rating');
                $reviews->comment = $request->get('comment');
                $reviews->date = date('Y-m-d', time());

                $reviews->save();
                return response()->json(['message' => 'Thêm Đánh giá thành công !', 'Đánh Giá:' => $reviews]);
            } else {
                return response()->json(['message' => 'Đơn hàng không hợp lệ hoặc chưa được giao, không thể thêm đánh giá!'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Thêm Đánh giá thất bại !'], 400); 
        }    
    }

    // Cập Nhật
    public function update(Request $request, $id)
    {   
        try {
            // Xác thực inputs
            if (($errors = $this->doUpdateValidation($request)) && count($errors) > 0) {
                return response()->json(['message' => 'Không bỏ trống !', 'errors' => $errors], 500);
            }
            // Sửa vào CSDL theo id
            $reviews = Reviews::find($id);
            if (!$reviews) {
                return response()->json(['message' => 'Không tìm thấy đánh giá'], 404);
            }else{
                $reviews-> rating = $request->get('rating');
                $reviews-> comment = $request->get('comment');
                $reviews-> date = date('Y-m-d', time());
                $reviews->save();
            }
            return response()->json(['message' => 'Cập nhật thành công !']); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'Cập nhật thất bại !'], 409); 
        }  
    }

    // Xóa theo id
    public function destroy($id)
    {
        try {
            $reviews = Reviews::where('review_id', $id)->first();
            if (!$reviews) {
                return response()->json(['message' => 'Không tìm thấy '], 404);
            }else{
                $reviews->delete();
                return response()->json("Xóa thành công !");
            }       
        } catch (\Exception $e) {
            return response()->json(['message' => 'Xóa thất bại !'], 409); 
        }
    }

    // Tìm kiếm theo data
    public function search($keyword) {
        
        $results = Reviews::where('id', 'like', '%' . $keyword . '%')
        ->orWhere('order_id', 'like', '%' . $keyword . '%')
        ->orWhere('rating', 'like', '%' . $keyword . '%')
        ->orWhere('comment', 'like', '%' . $keyword . '%')
        //->orWhere('date', 'like', '%' . $keyword . '%')
        ->get();

        return response()->json($results);
    }

    //Delete mềm
    public function softDelete($id) {
        $post = Reviews::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Không tìm được data !',
            ], 404);
        }

        $post->delete();

        return response()->json([
            'message' => 'Deleted successfully !',
        ], 200);
    }

    //Hoàn tac' delete mềm //withTrashed()
    public function reverse($id) {
        $post = Reviews::withTrashed()->where('id', $id)->first();
        
        if (!$post) {
            return response()->json([
                'message' => 'Không tìm được data !',
            ], 404);
        }

        $post->restore();

        return response()->json([
            'message' => 'Reverse successfully !',
        ], 200);
    }

    public function doValidate($request) {
        $data = [
            "order_id" => "required",
            "rating" => "required|numeric|max:5|min:1",
            "comment" => "required|string",
        ];

        $validator = Validator::make($request->all(), $data);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return [];
    }

    protected function doUpdateValidation($request) {
        $data = [
            "rating" => "required|numeric|max:5|min:1",
            "comment" => "required|string",
        ];
    
        $validator = Validator::make($request->all(), $data);
    
        if ($validator->fails()) {
            return $validator->errors();
        }
    
        return [];
    }
}
