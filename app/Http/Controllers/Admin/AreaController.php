<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = Area::withCount('tables')->paginate(5);
        return view('admin.areas.index', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:areas,name',
            'description' => 'nullable',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->has('is_active');
        Area::create($data);
        return redirect()->route('admin.areas.index')->with('success', 'Thêm khu vực thành công');
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area)
    {
        return view('admin.areas.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Area $area)
    {
        $data = $request->validate([
            'name' => 'required|unique:areas,name'. $area->id,
            'description' => 'nullable',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->has('is_active');
        $area->update($data);
        return redirect()->route('admin.areas.index')->with('success', 'Cập nhật khu vực thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        if ($area->tables()->exists()){
            return redirect()->route('admin.areas.index')->with('error','Không thể xóa khu vực này vì còn bàn ở bên trong');
        }
        $area->delete();
        return redirect()->route('admin.areas.index')->with('success', 'Xóa khu vực thành công');
    }
}
