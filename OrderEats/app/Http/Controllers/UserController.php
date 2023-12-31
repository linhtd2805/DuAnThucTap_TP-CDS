<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $user = User::paginate(2); // paginate(2) phân trang hiển thị 2 User trên 1 Page

    return response()->json($user);
  }

  public function show($id)
  {
    $user = User::find($id);

    if (!$user) {
      return response()->json(['error' => 'Người dùng không tồn tại'], 404);
    }

    return response()->json(['success' => 'Đã tìm thấy người dùng', 'user' => $user]);
  }


  // public function update(Request $request, $id)
  // {
  //   // Tìm người dùng trong database
  //   $user = User::find($id);

  //   // Kiểm tra nếu người dùng không tồn tại
  //   if (!$user) {
  //     return response()->json(['error' => 'Không tìm thấy người dùng'], 404);
  //   }

  //   // Validate dữ liệu được gửi lên
  //   $validator = Validator::make($request->all(), [
  //     'username' => 'sometimes|required|string|max:255',
  //     'password' => 'sometimes|required|string|min:6',
  //     'fullname' => 'sometimes|required|string|max:255',
  //     'email' => 'sometimes|required|email|max:255|unique:users,email,' . $id,
  //     // mỗi id chỉ duy nhất 1 email
  //     'phone' => 'sometimes|required|regex:/^[0-9]{10}$/|unique:users,phone,' . $id,
  //     // mỗi id chỉ có 1 sdt
  //     'role_id' => 'sometimes|required|integer',
  //     'latitude' => 'sometimes|required|numeric',
  //     'longitude' => 'sometimes|required|numeric',
  //   ]);

  //   // Nếu validation fails, trả về lỗi
  //   if ($validator->fails()) {
  //     return response()->json(['error' => $validator->errors()], 400);
  //   }

  //   // Cập nhật chỉ những trường dữ liệu được gửi lên
  //   $user->fill($request->only([
  //     'username',
  //     'password',
  //     'fullname',
  //     'email',
  //     'phone',
  //     'role_id',
  //     'latitude',
  //     'longitude'
  //   ]));

  //   // Lưu thay đổi vào database
  //   $user->save();

  //   // Trả về thông báo thành công
  //   return response()->json(['success' => 'Cập nhật người dùng thành công', 'user' => $user]);
  // }


  public function update(Request $request, $id)
  {
    // Tìm người dùng trong database
    $user = User::find($id);

    // Kiểm tra nếu người dùng không tồn tại
    if (!$user) {
      return response()->json(['error' => 'Không tìm thấy người dùng'], 404);
    }

    // Kiểm tra role_id của người dùng đăng nhập
    $loggedInUserRoleID = auth()->user()->username;

    // Kiểm tra xem người dùng đăng nhập có quyền cập nhật thông tin người dùng khác không
    if ($loggedInUserRoleID !== $user->username) {
      return response()->json(['error' => 'Bạn không có quyền cập nhật thông tin người dùng này'], 403);
    }

    // Kiểm tra xem các trường dữ liệu được gửi lên có rỗng không
    if (empty($request->all())) {
      return response()->json(['error' => 'Không được để trống'], 400);
    }

    // Define custom error messages in Vietnamese
    $customMessages = [
      'required' => 'Trường :attribute là bắt buộc.',
      'string' => 'Trường :attribute phải là chuỗi ký tự.',
      'max' => [
        'string' => 'Trường :attribute không được vượt quá :max ký tự.',
      ],
      'min' => [
        'string' => 'Trường :attribute phải có ít nhất :min ký tự.',
      ],
      'email' => 'Trường :attribute phải là địa chỉ email hợp lệ.',
      'unique' => 'Trường :attribute đã được sử dụng.',
      // 'regex' => 'Trường :attribute không đúng định dạng.',
      'phone.regex' => 'Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại di động Việt Nam đúng định dạng.',
      'numeric' => 'Trường :attribute phải là số.',
    ];

    // Validate dữ liệu được gửi lên sử dụng ngôn ngữ tiếng Việt
    $validator = Validator::make($request->all(), [
      'username' => 'sometimes|required|string|max:255',
      'password' => 'sometimes|required|string|min:6',
      'fullname' => 'sometimes|required|string|max:255',
      'email' => 'sometimes|required|email|max:255|unique:users,email,' . $id,
      'phone' => ['sometimes', 'required', 'regex:/^((09|03|07|08|05)+([0-9]{8})\b)$/'],
      'latitude' => 'sometimes|required|numeric',
      'longitude' => 'sometimes|required|numeric',
    ], $customMessages);

    // Nếu validation fails, trả về thông báo lỗi tiếng Việt
    if ($validator->fails()) {
      return response()->json(['message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 400);
    }

    // Cập nhật chỉ những trường dữ liệu được gửi lên
    $user->fill($request->only([
      'username',
      'password',
      'fullname',
      'email',
      'phone',
      'latitude',
      'longitude'
    ]));

    // Lưu thay đổi vào database
    $user->save();

    // Trả về thông báo thành công
    return response()->json(['success' => 'Cập nhật người dùng thành công', 'user' => $user]);
  }



  // Tìm kiếm theo data
  public function search($keyword)
  {
    $results = User::where('id', 'like', '%' . $keyword . '%')
      ->orWhere('username', 'like', '%' . $keyword . '%')
      ->orWhere('fullname', 'like', '%' . $keyword . '%')
      ->orWhere('email', 'like', '%' . $keyword . '%')
      ->orWhere('phone', 'like', '%' . $keyword . '%')
      ->get();

    if ($results->isEmpty()) {
      return response()->json(['error' => 'Không tìm thấy kết quả'], 404);
    }

    return response()->json(['success' => 'Đã tìm thấy', 'user' => $results]);
  }



}