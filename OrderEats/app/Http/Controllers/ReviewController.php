<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    //All Data
    public function index()
    {
        $reviews = Review::all();
        return response()->json($reviews);
    }

    // Hiển thị theo id
    public function show($id)
    {
        $reviews = Review::find($id);
        if (!$reviews) {
            return response()->json(['message' => 'Item not found'], 404);
        }
        return response()->json($reviews);
    }

    // Thêm 
    public function store(Request $request)
    {   
        // Xác thực inputs
        $this->validate($request, [
            "rating" => "required",
            "comment" => "required"
        ]);

        $reviews = new Review();
        $reviews-> rating = $request->get('rating');
        $reviews-> comment = $request->get('comment');

        $reviews->save();
        return response()->json(['message' => 'Created Successfully !']); 
    }

    // Cập Nhật
    public function update(Request $request, $id)
    {
        // Xác thực inputs
        $this->validate($request, [
            "rating" => "required",
            "comment" => "required"
        ]);

        // Sửa vào CSDL theo id
        $reviews = Review::where('review_id', $id)->first();
        if (!$reviews) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $reviews-> rating = $request->get('rating');
        $reviews-> comment = $request->get('comment');
        $review->save();

        return response()->json(['message' => 'Updated Successfully !']); 
    }

    // Xóa theo id
    public function destroy($id)
    {
      $reviews = Review::where('review_id', $id)->first();
      $reviews->delete();
      return response()->json("Review has been deleted");
    }
}
