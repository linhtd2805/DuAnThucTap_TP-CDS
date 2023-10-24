<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $user = User::paginate(2);

    return response()->json($user);
  }

  public function show($id)
  {
    $user = User::find($id);

    if (!$user)
      return response()->json(['message' => "Không thể tìm thấy!!"]);

    return response()->json($user);
  }

  public function update(Request $request, $id)
  {
    // Tìm người dùng trong database
    $user = User::find($id);

    // Kiểm tra nếu người dùng không tồn tại
    if (!$user) {
      return response()->json(['error' => 'User not found'], 404);
    }

    // Validate dữ liệu được gửi lên
    $validator = Validator::make($request->all(), [
      'username' => 'sometimes|required|string|max:255',
      'password' => 'sometimes|required|string|min:6',
      'fullname' => 'sometimes|required|string|max:255',
      'email' => 'sometimes|required|email|max:255|unique:users,email,' . $id, 
      'phone' => 'sometimes|required|regex:/^[0-9]{10}$/|unique:users,phone,' . $id,
      'role_id' => 'sometimes|required|integer',
      'latitude' => 'sometimes|required|numeric',
      'longitude' => 'sometimes|required|numeric',
    ]);

    // Nếu validation fails, trả về lỗi
    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 400);
    }

    // Cập nhật chỉ những trường dữ liệu được gửi lên
    $user->fill($request->only([
      'username',
      'password',
      'fullname',
      'email',
      'phone',
      'role_id',
      'latitude',
      'longitude'
    ]));

    // Lưu thay đổi vào database
    $user->save();

    return response()->json($user);
  }



}