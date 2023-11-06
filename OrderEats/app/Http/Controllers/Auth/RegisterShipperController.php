<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;

class RegisterShipperController extends Controller
{
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function log(){
        return response()->json("Done");
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->only('username', 'email', 'password', 'password_confirmation'), [
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'message' => 'Không bỏ trống!', 'errors' => $validator->errors()], 500);
        }

        try {

            $user = new User();
            $user->username = $request->get('username');
            $user->email = $request->get('email');
            $user->role_id = 3; //shipper;
            $plainPassword = $request->get('password');
            $user->password = app('hash')->make($plainPassword);
            
            $user->fullname = "";
            $user->phone="";
            $user->latitude=0;
            $user->longitude=0;

            $user->save();

            $token = auth()->tokenById($user->id);

            return response()->json([
                'message' => 'Đăng Ký thành công!',
                'success' => 1,
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => $user,
                'expires_in' => auth()->factory()->getTTL() * 60

            ]);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['success' => 0, 'message' => 'Đăng Ký thất bại!'], 409);
        }
    }
}
