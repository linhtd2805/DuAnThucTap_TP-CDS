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
    }

    // Cập Nhật
    public function update(Request $request, $id)
    {
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
    }

    // Xóa theo id
    public function destroy($id)
    {
      $reviews = Review::where('review_id', $id)->first();
      $reviews->delete();
      return response()->json("Xóa thành công !");
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
