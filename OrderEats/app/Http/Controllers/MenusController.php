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
            return response()->json(['message' => 'Không được phép truy cập'], 401);
        }

        $user = auth()->user();

        // Kiểm tra quyền của người dùng, chỉ cho phép admin truy cập
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Quyền truy cập bị từ chối'], 403);
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
            return response()->json(['message' => 'Không được phép truy cập'], 401);
        }

        $user = auth()->user();

        // Kiểm tra quyền của người dùng, chỉ cho phép admin truy cập
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Quyền truy cập bị từ chối'], 403);
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
        ],[
            'item_name.required' => 'Vui lòng nhập tên sản phẩm.',
            'description.required' => 'Vui lòng nhập mô tả sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.integer' => 'Danh mục không hợp lệ.',
            'price.required' => 'Vui lòng nhập giá sản phẩm.',
            'price.numeric' => 'Giá sản phẩm phải là số.',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0.01.',
            'price.max' => 'Giá sản phẩm không được lớn hơn 9999999.99.',
            'quantity.required' => 'Vui lòng nhập số lượng sản phẩm.',
            'quantity.numeric' => 'Số lượng phải là số nguyên.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng sản phẩm không được nhỏ hơn 1.',
            'quantity.max' => 'Số lượng sản phẩm không được lớn hơn 100.'
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
            return response()->json(['message' => 'Không được phép truy cập'], 401);
        }

        $user = auth()->user();

        // Kiểm tra quyền của người dùng, chỉ cho phép admin truy cập
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Quyền truy cập bị từ chối'], 403);
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
            return response()->json(['message' => 'Không được phép truy cập'], 401);
        }

        $user = auth()->user();

        // Kiểm tra quyền của người dùng, chỉ cho phép admin truy cập
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Quyền truy cập bị từ chối'], 403);
        }
        // PUT(id)
        // Update Info by Id

        $this->validate($request, [
            'item_name' => 'required',
            'description' => 'required',
            'category_id' => 'required|integer',
            'price' => 'required|numeric|min:0.01|max:9999999.99',
            'quantity' => 'required|numeric|integer|min:1|max:100'
         ],[
            'item_name.required' => 'Vui lòng nhập tên sản phẩm.',
            'description.required' => 'Vui lòng nhập mô tả sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.integer' => 'Danh mục không hợp lệ.',
            'price.required' => 'Vui lòng nhập giá sản phẩm.',
            'price.numeric' => 'Giá sản phẩm phải là số.',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0.01.',
            'price.max' => 'Giá sản phẩm không được lớn hơn 9999999.99.',
            'quantity.required' => 'Vui lòng nhập số lượng sản phẩm.',
            'quantity.numeric' => 'Số lượng phải là số nguyên.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng sản phẩm không được nhỏ hơn 1.',
            'quantity.max' => 'Số lượng sản phẩm không được lớn hơn 100.'
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
            return response()->json(['message' => 'Không được phép truy cập'], 401);
        }

        $user = auth()->user();

        // Kiểm tra quyền của người dùng, chỉ cho phép admin truy cập
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Quyền truy cập bị từ chối'], 403);
        }
        // DELETE(id)
        // Delete by Id
        $menus = Menus::find($id);
        $menus->delete();
        return response()->json('Món đã được xóa khỏi menu!');

    }
}