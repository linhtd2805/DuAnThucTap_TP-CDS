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
        $reviews = Reviews::with('orders', 'user')->get();
        $orders = Orders::all();
        // return view('reviews', compact('reviews', 'orders'));
        return response()->json($reviews, $orders);
    }

    // Hiển thị theo id
    public function show($id)
    {
        $reviews = Reviews::where('id', $id)->first();
        if (!$reviews) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }
        // return response()->json($reviews);
        return view('reviews1', compact('reviews'));
    }

    // Thêm 
    public function store(Request $request)
    {   
        try {
            // Xác thực inputs
            if (($errors = $this->doValidate($request)) && count($errors) > 0) {
                return response()->json(['message' => 'Không bỏ trống !', 'errors' => $errors], 500);
            }

            $reviews = new Reviews();
            $reviews-> order_id = $request->get("order_id");
            $reviews-> rating = $request->get('rating');
            $reviews-> comment = $request->get('comment');
            $reviews-> date = date('Y-m-d', time());

            $reviews->save();
            return response()->json(['message' => 'Thêm Đánh giá thành công !']); 

        } catch (\Exception $e) {
            return response()->json(['message' => 'Thêm Đánh giá thất bại !'], 409); 
        }    
    }

    // Cập Nhật
    public function update(Request $request, $id)
    {   
        try {
            // Xác thực inputs
            if (($errors = $this->doValidate($request)) && count($errors) > 0) {
                return response()->json(['message' => 'Không bỏ trống !', 'errors' => $errors], 500);
            }
            // Sửa vào CSDL theo id
            $reviews = Reviews::where('review_id', $id)->first();
            if (!$reviews) {
                return response()->json(['message' => 'Không tìm thấy '], 404);
            }else{
                $reviews-> order_id = $request->get("order_id");
                $reviews-> rating = $request->get('rating');
                $reviews-> comment = $request->get('comment');
                $reviews-> date = $request->get('date');
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

    //Phân trang
    public function wpage()
    {
        $reviews = Reviews::with('orders', 'user')->get();
        $orders = Orders::all();

        $data  = DB::table('reviews')->paginate(8);
        return response()->json(['reviews' => $data]);
    }

    // Tìm kiếm theo data
    public function search($keyword) {
        
        $results = DB::table('reviews')
        ->where('id', 'like', '%' . $keyword . '%')
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
            "rating" => "required|numeric|max:11",
            "comment" => "required",
            "order_id" => "required|string|max:20",
            // "date" => "required|date_format:Y-m-d",
        ];

        $validator = Validator::make($request->all(), $data);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return [];
    }
}
