<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menus;

class MenusController extends Controller
{   

    public function index()
    {
        // Kiểm tra xem người dùng đã đăng nhập không
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        // Kiểm tra quyền của người dùng, chỉ cho phép admin truy cập
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        // Get All products
        // get All Products From Database
        // $menus = Menus::all();
        $menus = Menus::paginate(2); // Phân trang với 2 phần tử trên mỗi trang
        return response()->json($menus);

    }


    public function store(Request $request)
    {
        // Kiểm tra xem người dùng đã đăng nhập không
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        // Kiểm tra quyền của người dùng, chỉ cho phép admin truy cập
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        //POST(request)
        // Store all information of Products to Database
        //in_array()

        $menus = new Menus();

        $this->validate($request, [
            'item_name' => 'required',
            'description' => 'required',
            'category_id' => 'required|integer',
            'price' => 'required|numeric|min:0.01|max:9999999.99',
            'quantity' => 'required|numeric|integer|min:1|max:100'
         ]);


        // text data
        $menus->item_name = $request->input('item_name');
        $menus->description = $request->input('description');
        $menus->category_id = $request->input('category_id');
        $menus->price = $request->input('price');
        $menus->quantity = $request->input('quantity');
        $menus->save();
        return response()->json($menus);


    }


    public function show($id)
    {   
        // Kiểm tra xem người dùng đã đăng nhập không
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        // Kiểm tra quyền của người dùng, chỉ cho phép admin truy cập
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        // GET(id)
        // show each product by its ID from database
        $menus = Menus::find($id);
        return response()->json($menus);
    }


    public function update(Request $request, $id)
    {   
        // Kiểm tra xem người dùng đã đăng nhập không
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        // Kiểm tra quyền của người dùng, chỉ cho phép admin truy cập
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        // PUT(id)
        // Update Info by Id

        $this->validate($request, [
            'item_name' => 'required',
            'description' => 'required',
            'category_id' => 'required|integer',
            'price' => 'required|numeric|min:0.01|max:9999999.99',
            'quantity' => 'required|numeric|integer|min:1|max:100'
         ]);

        $menus = Menus::find($id);


        
        // text data
        $menus->item_name = $request->input('item_name');
        $menus->description = $request->input('description');
        $menus->category_id = $request->input('category_id');
        $menus->price = $request->input('price');
        $menus->quantity = $request->input('quantity');
        $menus->save();
        return response()->json($menus);

    }


    public function destroy($id)
    {
        // Kiểm tra xem người dùng đã đăng nhập không
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        // Kiểm tra quyền của người dùng, chỉ cho phép admin truy cập
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        // DELETE(id)
        // Delete by Id
        $menus = Menus::find($id);
        $menus->delete();
        return response()->json('Item_Menu Deleted Successfully');

    }
}