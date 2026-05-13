<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\OptionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::with('category');
 
        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
 
        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
 
        // Sắp xếp theo giá
        if ($request->sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('id', 'desc'); // mặc định: mới nhất trước
        }
 
        $menuItems  = $query->paginate(10)->withQueryString(); // withQueryString() giữ lại filter khi chuyển trang
        $categories = Category::orderBy('sort_order')->get();
 
        return view('admin.menuitems.index', compact('menuItems', 'categories'));
    }
    public function create(){
        $categories = Category::all();
        $optionGroups = OptionGroup::with('optionValues')->get();
        return view('admin.menuitems.create', compact('categories', 'optionGroups'));
    }
    public function store(Request $request){
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|unique:menu_items, name',
            'description' => 'nullable',
            'price' => 'required|integer|min:20000',
            'status' => 'required|in:active,out_of_stock,inactive',
            'image' => 'nullable|image|max:2048',
            'option_groups'   => 'nullable|array',
            'option_groups.*' => 'exists:option_groups,id',
        ]);
        $data['slug'] = Str::slug($data['name']);
        if($request->hasFile('image')){
            $data['image'] = $request->file('image')->store('MenuItems', 'public');
        }
        $menuItem = MenuItem::create($data);
        $menuItem->optionGroups()->sync($request->input('option_groups', []));
        return redirect()->route('admin.menuitems.index')->with('success', 'Tạo món ăn thành công');
    }

    public function show(MenuItem $menuitem){
        return view('admin.menuitems.show', compact('menuitem'));
    }

    public function edit(MenuItem $menuitem){
        $menuitem->load('optionGroups', 'category.optionGroups'); // ← thêm dòng này
        $categories   = Category::all();
        $optionGroups = OptionGroup::with('optionValues')->get();
        return view('admin.menuitems.edit', compact('menuitem','categories','optionGroups'));
    }

    public function update(Request $request, MenuItem $menuitem){
        $data = $request->validate(
            [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|unique:menu_items,name,'. $menuitem->id,
                'description' => 'nullable',
                'price' => 'required|integer|min:20000',
                'status' => 'required|in:active,out_of_stock,inactive',
                'image' => 'nullable|image|max:2048',
                'option_groups'   => 'nullable|array',
                'option_groups.*' => 'exists:option_groups,id',
            ]
        );
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('image')) {
            if ($menuitem->image) {
                    \Storage::disk('public')->delete($menuitem->image);
                }
            $data['image'] = $request->file('image')->store('MenuItems', 'public');
        }
        $menuitem->update($data);
        $menuitem->optionGroups()->sync($request->input('option_groups', []));
        return redirect()->route('admin.menuitems.index')->with('success', 'Cập nhật món ăn thành công');
    }

    public function destroy(MenuItem $menuitem){
        if($menuitem->image)
            \Storage::disk('public')->delete($menuitem->image);
        $menuitem->delete();
        return redirect()->route('admin.menuitems.index')->with('success', 'Xóa món ăn thành công');
    }
}
