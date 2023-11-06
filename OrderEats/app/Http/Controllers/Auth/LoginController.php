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
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'message' => 'Điền vào chỗ trống!', 'errors' => $validator->errors()], 500);
        }

        try {

            $token = $this->jwt->attempt($request->only('username', 'password'));
            // return response()->json($token, 500);

            if (!$token) {
                return response()->json(['success' => 0, 'message' => 'Không tìm thấy User!'], 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['success' => 0, 'message' => 'token expired'], 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['success' => 0, 'message' => 'token invalid'], 500);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['success' => 0, 'message' => 'unknown error'], 500);
        }

        // if everything ok
        $user = Auth::user();

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

        return response()->json([
            'user' => $user
        ]);
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
        $user = Auth::user();

        $rules = [
            'fullname' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            // 'username' => 'required|unique:users,username,' . $user->id
        ];

        if ($request->input('password') != "") {
            $rules['password'] = 'required|min:4|confirmed';
        }

        $validator = Validator::make($request->only('fullname', 'email', 'password', 'password_confirmation', 'phone', 'latitude', 'longitude'), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'message' => 'Không bỏ trống!', 'errors' => $validator->errors()], 500);
        }

        try {

            $user->fullname = $request->input('fullname');
            $user->phone = $request->input('phone');
            $user->longitude = $request->input('longitude');
            $user->latitude = $request->input('latitude');
            $user->device_token =  $request->token;

            if ($request->input('password') != "") {
                $plainPassword = $request->input('password');
                $user->password = app('hash')->make($plainPassword);
            }
            try {
                $user->save();
            }catch (\Exception $e) {
                return response()->json($e);
            }
            $token = auth()->tokenById($user->id);

            return response()->json([
                'success' => 1,
                'message' => 'Cập nhật hồ sơ thành công!',
                'access_token' => $token,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['success' => 0, 'message' => 'Cập nhật hồ sơ thất bại!'], 409);
        }
    }
}
