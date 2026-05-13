<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tables = Table::query()->with('area');
        if($request->filled('search')){
            $tables->where('name', 'like', '%'.$request->search.'%');
        }
        if($request->filled('status')){
            $tables->where('status', $request->status);
        }
        if($request->filled('area_id')){
            $tables->where('area_id', $request->area_id);
        }
        $tables = $tables->paginate(10)->withQueryString();
        $areas = Area::where('is_active', true)->get();
        return view('admin.tables.index', compact('tables', 'areas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $areas = Area::where('is_active', true)->get();
        return view('admin.tables.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|unique:tables,name',
            'capacity' => 'required|integer|min:1',
        ]);
        $data['status'] = 'active';
        Table::create($data);
        return redirect()->route('admin.tables.index')->with('success', 'Thêm bàn ăn thành công');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Table $table)
    {
        $areas = Area::where('is_active', true)->get();
        return view('admin.tables.edit', compact('table', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Table $table)
    {
        $data = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|unique:tables,name,'. $table->id,
            'capacity' => 'required|integer|min:1',
            'status'   => 'required|in:active,inactive',
        ]);
        $table->update($data);
        return redirect()->route('admin.tables.index')->with('success', 'Cập nhật bàn ăn thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        if ($table->bookings()->whereIn('status', ['pending', 'confirmed'])->exists()) {
            return redirect()
                ->route('admin.tables.index')
                ->with('error', 'Không thể xoá — bàn đang có booking active');
        }
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Xóa bàn ăn thành công');
    }
}
