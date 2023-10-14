<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Roles;

class RoleController extends Controller
{
    public function index(Request $request){
        $role = Roles::all();
            
            return response()->json($role);
    }

    public function show($id)
    {
        $role = Roles::find($id);
        
        if(!$role) 
        return response()->json(['message' => "Product not found!!"]);

        return response()->json($role);
    }

    public function create(Request $request){

        // $this->variables($request,[
        //     'name_role' => 'required|string'
        // ]);

        $data = $request->all();

        $role = Roles::create($data);

        return response()->json($role);
    }

    public function update(Request $request, $id){
        $role = Roles::find($id);

        //Error
        if(!$role) 
        return response()->json(['message' => "Product not found!!"]);

        $data = $request->all();


      

        $role->fill($data);

        $role->save();

        return response()->json($role);
    }

    public function destroy($id){
        $role = Roles::find($id);

        //Error
        if(!$role) 
        return response()->json(['message' => "Product not found!!"]);

        $role->delete();

        return response()->json(['message' => 'Product success delete'], 201);
    }
}