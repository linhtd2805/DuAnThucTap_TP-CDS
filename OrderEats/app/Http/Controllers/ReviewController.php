<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reviews;
use App\Models\Orders;
use App\Models\User;
use App\Models\BlackList;
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
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
                'rating' => 'required|integer|between:1,5',
                'comment' => 'required|string|max:255',
            ], [
                'order_id.required' => 'Chưa nhập đơn hàng.',
                'order_id.exists' => 'Trường đơn hàng không tồn tại.',
                'rating.required' => 'Chưa nhập đánh giá.',
                'rating.integer' => 'Trường đánh giá phải là số nguyên.',
                'rating.between' => 'Trường đánh giá phải nằm trong khoảng từ 1 đến 5.',
                'comment.required' => 'Chưa nhập bình luận.',
                'comment.string' => 'Trường bình luận phải là một chuỗi.',
                'comment.max' => 'Trường bình luận không được vượt quá 255 kí tự.',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 400);
            }

            $user = auth()->user();
            $orderID = $request->get("order_id");

            // Kiểm tra xem người dùng đã thêm đánh giá cho đơn hàng này chưa
            $existingReview = Reviews::where('order_id', $orderID)
                ->where('user_id', $user->id)
                ->first();

            if ($existingReview) {
                return response()->json(['message' => 'Bạn đã thêm đánh giá cho đơn hàng này rồi.'], 400);
            }

            // Kiểm tra xem đơn hàng của user và trạng thái đã giao chưa
            $deliveredOrders = Orders::where('user_id', $user->id)
                ->where('order_status', 'Đã giao')
                ->pluck('id');

            if ($deliveredOrders->contains($orderID)) {
                // Kiểm tra comment không chứa từ nào trong danh sách BlackList
                $comment = $request->get('comment');
                $blackListWords = BlackList::pluck('word')->toArray();
                $commentLower = strtolower($comment);
                $commentWords = str_word_count($commentLower, 1);
                $blackListWordsLower = array_map('strtolower', $blackListWords);
                $intersection = array_intersect($commentWords, $blackListWordsLower);

                if (!empty($intersection)) {
                    return response()->json(['message' => 'Comment chứa từ nhạy cảm: ' . implode(', ', $intersection)], 400);
                }

                // Nếu comment không chứa từ nhạy cảm và tất cả dữ liệu hợp lệ, thì lưu đánh giá
                $reviews = new Reviews();
                $reviews->order_id = $orderID;
                $reviews->rating = $request->get('rating');
                $reviews->comment = $comment;
                $reviews->date = date('Y-m-d', time());
                $reviews->user_id = $user->id;

                $reviews->save();
                return response()->json(['message' => 'Thêm Đánh giá thành công !', 'Đánh Giá:' => $reviews]);
            } else {
                return response()->json(['message' => 'Đơn hàng không hợp lệ hoặc chưa được giao, không thể thêm đánh giá!'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Thêm Đánh giá thất bại !', 'error' => $e->getMessage()], 400);
        }
    }

    // Cập Nhật
    public function update(Request $request, $id)
    {   
        try {
            // Xác thực inputs
            $validator = Validator::make($request->all(), [
                'rating' => 'integer|between:1,5',
                'comment' => 'string|max:255',
            ], [
                'rating.integer' => 'Trường đánh giá phải là số nguyên.',
                'rating.between' => 'Trường đánh giá phải nằm trong khoảng từ 1 đến 5.',
                'comment.string' => 'Trường bình luận phải là một chuỗi.',
                'comment.max' => 'Trường bình luận không được vượt quá 255 kí tự.',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 400);
            }

            // Sửa vào CSDL theo id
            $reviews = Reviews::find($id);
            if (!$reviews) {
                return response()->json(['message' => 'Không tìm thấy đánh giá'], 404);
            }

            // Kiểm tra nếu rating hoặc comment là chuỗi trắng, trả về lỗi
            if ($request->has('rating') && trim($request->input('rating')) === '') {
                return response()->json(['message' => 'Trường đánh giá không được để trống'], 400);
            }

            if ($request->has('comment') && trim($request->input('comment')) === '') {
                return response()->json(['message' => 'Trường bình luận không được để trống'], 400);
            }

            // Chỉ cập nhật các trường dữ liệu đã được gửi lên
            if ($request->has('rating')) {
                $reviews->rating = $request->input('rating');
            }

            if ($request->has('comment')) {
                $reviews->comment = $request->input('comment');
            }

            $reviews->save();

            return response()->json(['message' => 'Cập nhật thành công !']); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'Cập nhật thất bại !'], 409); 
        }  
    }

    // Xóa theo id
    public function destroy($id)
    {
        try {
            $reviews = Reviews::find($id);
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
