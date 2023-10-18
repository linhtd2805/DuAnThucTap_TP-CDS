<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $user = User::all();

    return response()->json($user);
  }

  public function show($id)
  {
    $user = User::find($id);

    if (!$user)
      return response()->json(['message' => "Product not found!!"]);

    return response()->json($user);
  }

  public function update(Request $request, $id)
  {

    //all it to the database
    $user = User::find($id);

    //bắt lỗi
    if (!$user) {
      return response()->json(['error' => 'User not found'], 404);
    }

    $validator = Validator::make($request->all(), [
      'username' => 'required|string|max:255',
      'password' => 'required|string|min:6',
      'fullname' => 'required|string|max:255',
      'email' => 'required|email|max:255',
      'phone' => 'required|string|max:20',
      'role_id' => 'required|integer',
      'latitude' => 'required|numeric',
      'longitude' => 'required|numeric',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 400);
    }
    $user->username = $request->input('username');
    $user->password = $request->input('password');
    $user->fullname = $request->input('fullname');
    $user->email = $request->input('email');
    $user->phone = $request->input('phone');
    $user->role_id = $request->input('role_id');
    $user->latitude = $request->input('latitude');
    $user->longitude = $request->input('longitude');

    $user->save();

    return response()->json($user);
  }

  // public function destroy($id){
//     $user = User::find($id);

  //     //Error
//     if(!$user) 
//     return response()->json(['message' => "Product not found!!"]);

  //     $user->delete();

  //     return response()->json(['message' => 'Product success delete'], 201);
// }
}