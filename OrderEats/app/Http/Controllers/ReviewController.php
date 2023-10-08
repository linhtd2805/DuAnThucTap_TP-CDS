<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    //All Data
    public function index()
    {
        $reviews = Review::all();
        return view('reviews', ['reviews' => $reviews]);
        // return response()->json($reviews);
    }

    // Hiển thị theo id
    public function show($id)
    {
        $reviews = Review::where('review_id', $id)->first();
        if (!$reviews) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
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

            $reviews = new Review();
            $reviews-> order_id = $request->get("order_id");
            $reviews-> user_id = $request->get("user_id");
            $reviews-> shipper_id = $request->get("shipper_id");
            $reviews-> rating = $request->get('rating');
            $reviews-> comment = $request->get('comment');
            $reviews-> date = $request->get('date');

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
            $reviews = Review::where('review_id', $id)->first();
            if (!$reviews) {
                return response()->json(['message' => 'Không tìm thấy '], 404);
            }else{
                $reviews-> order_id = $request->get("order_id");
                $reviews-> user_id = $request->get("user_id");
                $reviews-> shipper_id = $request->get("shipper_id");
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
            $reviews = Review::where('review_id', $id)->first();
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

    public function doValidate($request) {
        $data = [
            "rating" => "required|numeric",
            "comment" => "required",
            "order_id" => "required",
            "user_id" => "required",
            "shipper_id" => "required",
            "date" => "required|date_format:Y-m-d",
        ];

        $validator = Validator::make($request->all(), $data);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return [];
    }
}
