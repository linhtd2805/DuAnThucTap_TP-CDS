<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function index(Request $request){
    $user = User::all();
        
        return response()->json($user);
}

public function show($id)
{
    $user = User::find($id);
    
    if(!$user) 
    return response()->json(['message' => "Product not found!!"]);

    return response()->json($user);
}

public function update(Request $request, $id){
  
      //all it to the database
      $user = User::find($id);
      

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