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

    public function getConfirmedShippers()
    {
        // Lấy tất cả các tài khoản shipper đã được xác nhận (is_shipper = true)
        $shippers = User::where('role_id', 3)->where('is_shipper', true)->paginate(3);
        return response()->json(['Danh sách shipper hiện tại:' => $shippers], 200);
    }

    public function getUnconfirmedShippers()
    {
        // Lấy tất cả các tài khoản shipper đã được xác nhận (is_shipper = false)
        $shippers = User::where('role_id', 3)->where('is_shipper', false)->paginate(3);
        return response()->json(['Danh sách shipper chưa được xác nhận' => $shippers], 200);
    }

    // Admin xác nhận thành shipper
    public function confirmShipper($id)
    {
        if (empty($id)) {
            return response()->json(['message' => 'Chưa chọn tài khoản'], 400);
        }

        $shipper = User::where('id', $id)
                        ->where('role_id', 3)
                        ->first();

        if ($shipper) {
            if ($shipper->is_shipper) {
                return response()->json(['message' => 'Tài khoản này đã được xác nhận là shipper.'], 400);
            }

            $shipper->is_shipper = true;
            $shipper->save();

            return response()->json(['message' => 'Tài khoản shipper đã được xác nhận.', 'Thông tin:' => $shipper], 200);
        } else {
            return response()->json(['message' => 'Không tìm thấy tài khoản shipper, nhập lại.'], 404);
        }
    }

    // Admin bỏ xác nhận thành shipper
    public function unconfirmShipper($id)
    {
        if (empty($id)) {
            return response()->json(['message' => 'Chưa chọn tài khoản'], 400);
        }

        $shipper = User::where('id', $id)
                        ->where('role_id', 3)
                        ->first();

        if ($shipper) {
            if ($shipper->is_shipper) {
                $shipper->is_shipper = false;
                $shipper->save();
                return response()->json(['message' => 'Tài khoản shipper sẽ dừng hoạt động.', 'Thông tin:' => $shipper], 200);
            }
            
            return response()->json(['message' => 'Tài khoản này đã không dừng hoạt động, không thể thực hiện nữa.'], 400);
        } else {
            return response()->json(['message' => 'Không tìm thấy tài khoản shipper, nhập lại.'], 404);
        }
    }





}
