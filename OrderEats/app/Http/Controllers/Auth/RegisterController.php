<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;

class RegisterController extends Controller
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
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'message' => 'Please fix these errors', 'errors' => $validator->errors()], 500);
        }

        try {

            $user = new User();
            $user->name = $request->get('username');
            $user->email = $request->get('email');
            $plainPassword = $request->get('password');
            $user->role_id = 3; //User;
            $user->password = app('hash')->make($plainPassword);
            return response()->json($user);
            // $user->save();

            // $token = auth()->tokenById($user->id);

            // return response()->json([
            //     'success' => 1,
            //     'message' => 'User Registration Succesful!',
            //     'access_token' => $token,
            //     'token_type' => 'bearer',
            //     'user' => $user,
            //     'expires_in' => auth()->factory()->getTTL() * 60

            // ]);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['success' => 0, 'message' => 'User Registration Failed!'], 409);
        }
    }
}
