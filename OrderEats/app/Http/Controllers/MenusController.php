<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menus;

class MenusController extends Controller
{

    public function index()
    {
        // Get All products
        // get All Products From Database
        // $menus = Menus::all();
        $menus = Menus::paginate(2); // Phân trang với 2 phần tử trên mỗi trang
        return response()->json($menus);

    }


    public function store(Request $request)
    {
        //POST(request)
        // Store all information of Products to Database
        //in_array()

        $menus = new Menus();

        


        // text data
        $menus->item_name = $request->input('item_name');
        $menus->description = $request->input('description');
        $menus->price = $request->input('price');

        $menus->save();
        return response()->json($menus);


    }


    public function show($id)
    {
        // GET(id)
        // show each product by its ID from database
        $menus = Menus::find($id);
        return response()->json($menus);
    }


    public function update(Request $request, $id)
    {
        // PUT(id)
        // Update Info by Id

        $this->validate($request, [
            'item_name' => 'required',
            'description' => 'required',
            'price' => 'required'
         ]);

        $menus = Menus::find($id);


        
        // text data
        $menus->item_name = $request->input('item_name');
        $menus->description = $request->input('description');
        $menus->price = $request->input('price');

        $menus->save();
        return response()->json($menus);

    }


    public function destroy($id)
    {
        // DELETE(id)
        // Delete by Id
        $menus = Menus::find($id);
        $menus->delete();
        return response()->json('Item_Menu Deleted Successfully');

    }
}