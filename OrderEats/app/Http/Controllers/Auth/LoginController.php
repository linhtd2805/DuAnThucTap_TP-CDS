<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;

class LoginController extends Controller
{
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function login1(){
        return view('login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->only('username', 'password'), [
            'username' => 'required',
            'password' => 'required'
        ], [
            'username.required' => 'Chưa nhập tài khoản.',
            'password.required' => 'Chưa nhập mật khẩu.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'message' => 'Không bỏ trống!', 'errors' => $validator->errors()], 500);
        }

        try {
            $credentials = $request->only('username', 'password');
            $token = $this->jwt->attempt($credentials);

            if (!$token) {
                return response()->json(['success' => 0, 'message' => 'Sai tài khoản hoặc mật khẩu!'], 404);
            }

            // Lấy thông tin người dùng từ token
            $user = Auth::user();

            // Kiểm tra nếu tài khoản không phải là shipper
            if (!$user->is_shipper) {
                return response()->json(['success' => 0, 'message' => 'Tài khoản này chưa được xác nhận hoạt động!'], 403);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['success' => 0, 'message' => 'Token hết hạn'], 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['success' => 0, 'message' => 'Token không hợp lệ'], 500);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['success' => 0, 'message' => 'Lỗi Token', 'error' => $e->getMessage()], 500);
        }

        // Nếu mọi thứ ok
        return response()->json([
            'message' => 'Đăng Nhập thành công',
            'success' => 1,
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    function userDetails()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Người dùng không tồn tại'], 404);
        }

        return response()->json(['success' => 1, 'user' => $user]);
    }


    function logout()
    {
        $token = auth()->tokenById(auth()->user()->id);
        $this->jwt->setToken($token)->invalidate();
        auth()->logout();
        return response()->json([
            'success' => 1,
            'message' => 'Đăng Xuất thành công!'
        ]);
    }

    function checkLogin()
    {
        if (Auth::user()) {
            return response()->json(['success' => 1, 'message'=>"Đang đăng nhập với tài khoản:", 'data'=>Auth::user()]);
        }

        return response()->json(['success' => 0]);
    }

    function updateProfile(Request $request)
    {
        // Kiểm tra xem người dùng có tồn tại hay không
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Người dùng không tồn tại'], 404);
        }

        // Kiểm tra quyền cập nhật hồ sơ của người dùng khác
        if ($user->id !== auth()->user()->id) {
            return response()->json(['success' => 0, 'message' => 'Không có quyền cập nhật hồ sơ của người dùng khác.'], 403);
        }

        // Khai báo các quy tắc kiểm tra dữ liệu đầu vào
        $rules = [
            'fullname' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ];

        $customMessages = [
            'fullname.required' => 'Họ tên không được để trống.',
            'fullname.string' => 'Họ tên phải là một chuỗi.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email phải là địa chỉ email hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',
            'phone.required' => 'Số điện thoại không được để trống.',
            'longitude.required' => 'Vui lòng nhập vĩ độ.',
            'latitude.required' => 'Vui lòng nhập kinh độ.',
        ];

        // Kiểm tra mật khẩu hiện tại và cập nhật mật khẩu mới nếu được cung cấp
        if ($request->input('password') != "") {
            if (!$request->has('current_password')) {
                return response()->json(['success' => 0, 'message' => 'Vui lòng nhập mật khẩu hiện tại'], 400);
            }

            // Kiểm tra mật khẩu hiện tại
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return response()->json(['success' => 0, 'message' => 'Mật khẩu hiện tại không chính xác'], 400);
            }

            // Kiểm tra và cập nhật mật khẩu mới
            $rules['password'] = 'required|min:4|confirmed';
        }

        // Thực hiện kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        try {
            // Cập nhật thông tin người dùng
            $user->fullname = $request->input('fullname');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->longitude = $request->input('longitude');
            $user->latitude = $request->input('latitude');
            $user->device_token =  $request->token;

            // Cập nhật mật khẩu nếu được cung cấp
            if ($request->input('password') != "") {
                $plainPassword = $request->input('password');
                $user->password = app('hash')->make($plainPassword);
            }

            // Lưu thay đổi
            $user->save();

            // Tạo lại token mới
            $token = auth()->tokenById($user->id);

            return response()->json([
                'success' => 1,
                'message' => 'Cập nhật hồ sơ thành công!',
                'access_token' => $token,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            // Log lỗi và trả về thông báo lỗi cụ thể
            Log::error('Lỗi: ' . $e->getMessage());
            return response()->json(['success' => 0, 'message' => $e->getMessage()], 500);
        }
    }


}
