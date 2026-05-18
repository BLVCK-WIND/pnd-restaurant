<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCategoryRequest;
use App\Models\Category;
use App\Models\OptionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('sort_order')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->paginate(5)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $optionGroups = OptionGroup::with('optionValues')->get();
        return view('admin.categories.create', compact('optionGroups'));
    }

    public function store(SaveCategoryRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['sort_order'])) {
            // Đẩy tất cả item có sort_order >= vị trí mới xuống 1
            Category::where('sort_order', '>=', $data['sort_order'])->increment('sort_order');
        } else {
            // Không chọn thứ tự → xếp cuối
            $data['sort_order'] = Category::max('sort_order') + 1;
        }

        if ($request->hasFile('image'))
            $data['image'] = $request->file('image')->store('categories', 'public');

        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');

        $category = Category::create($data);
            // Gắn option groups
        $category->optionGroups()->sync($request->input('option_groups', []));

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công');
    }

    public function edit(Category $category)
    {
        $optionGroups = OptionGroup::with('optionValues')->get();
        return view('admin.categories.edit', compact('category', 'optionGroups'));
    }

    public function update(SaveCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        $newOrder = $data['sort_order'] ?? $category->sort_order;
        $oldOrder = $category->sort_order;

        if ($newOrder !== $oldOrder) {
            if ($newOrder > $oldOrder) {
                // Kéo xuống → các item ở giữa dịch lên 1
                Category::whereBetween('sort_order', [$oldOrder + 1, $newOrder])
                        ->where('id', '!=', $category->id)
                        ->decrement('sort_order');
            } else {
                // Kéo lên → các item ở giữa dịch xuống 1
                Category::whereBetween('sort_order', [$newOrder, $oldOrder - 1])
                        ->where('id', '!=', $category->id)
                        ->increment('sort_order');
            }
        }

        if ($request->hasFile('image')) {
            if ($category->image)
                \Storage::disk('public')->delete($category->image);
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $data['slug']       = Str::slug($data['name']);
        $data['is_active']  = $request->has('is_active');
        $data['sort_order'] = $newOrder;

        $category->update($data);

        // Cập nhật option groups
        $category->optionGroups()->sync($request->input('option_groups', []));

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công');
    }

    public function destroy(Category $category)
    {
        if ($category->image)
            \Storage::disk('public')->delete($category->image);

        $deletedOrder = $category->sort_order;
        $category->delete();

        // Kéo các item phía sau lên 1 để không bị lỗ hổng
        Category::where('sort_order', '>', $deletedOrder)->decrement('sort_order');

        return redirect()->route('admin.categories.index')->with('success', 'Xoá danh mục thành công');
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items'              => 'required|array',
            'items.*.id'         => 'required|integer|exists:categories,id',
            'items.*.sort_order' => 'required|integer|min:1',
        ]);

        // Hoán vị sort_order thực tế của các item trong trang
        // → không đụng đến sort_order của các trang khác
        foreach ($data['items'] as $item) {
            Category::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
            ]);
        }

        return response()->json(['message' => 'Cập nhật thứ tự thành công']);
    }
}