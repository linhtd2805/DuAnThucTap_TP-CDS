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

public function create(Request $request){

    $data = $request->all();

    $user = User::create($data);

    return response()->json($user);
}

public function update(Request $request, $id){
    $user = User::find($id);

    //Error
    if(!$user) 
    return response()->json(['message' => "Product not found!!"]);

    $data = $request->all();

    $user->fill($data);

    $user->save();

    return response()->json($user);
}

public function destroy($id){
    $user = User::find($id);

    //Error
    if(!$user) 
    return response()->json(['message' => "Product not found!!"]);

    $user->delete();

    return response()->json(['message' => 'Product success delete'], 201);
}
}