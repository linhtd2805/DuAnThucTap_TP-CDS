<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Roles;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $role = Roles::paginate(2); // phân trang

        return response()->json($role);
    }

    public function show($id)
    {
        // GET(id)
        // show each product by its ID from database
        $roles = Roles::find($id);
        return response()->json($roles);
    }

    public function create(Request $request)
    {


        $roles = new Roles();
        // text data
        $roles->name_role = $request->input('name_role');

        $roles->save();
        return response()->json($roles);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name_role' => 'required'

        ]);

        $roles = Roles::find($id);


        // text data
        $roles->name_role = $request->input('name_role');

        $data = $request->all();
        $roles->fill($data);
        $roles->save();


        return response()->json($roles);
    }

    public function destroy($id)
    {
        $role = Roles::find($id);

        //Error
        if (!$role)
            return response()->json(['message' => "Product not found!!"]);

        $role->delete();

        return response()->json(['message' => 'Product success delete'], 201);
    }
}