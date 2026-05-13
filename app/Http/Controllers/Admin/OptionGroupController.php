<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OptionGroup;
use App\Models\OptionValue;
use Illuminate\Http\Request;

class OptionGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $optionGroups = OptionGroup::with('optionValues')->paginate(10);
        return view('admin.optiongroups.index', compact('optionGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.optiongroups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'name' => 'required',
                'is_required' => 'nullable|boolean',
                'is_multiple'=>'nullable|boolean',
                'values' => 'required|array|min:1',
                'values.*.name' => 'required|string',
                'values.*.extra_price' => 'integer|min:0',
            ]
        );
        $optionGroup = OptionGroup::create([
            'name' => $data['name'],
            'is_required' => $request->has('is_required'),
            'is_multiple' => $request->has('is_multiple'),
        ]);
        foreach($request->values as $value){
            OptionValue::create([
                        'option_group_id' => $optionGroup->id,
                        'name' => $value['name'],
                        'extra_price' => $value['extra_price'] ?? 0,
                    ]);
        }
        
        return redirect()->route('admin.optiongroups.index')->with('success', 'Thêm Group Option thành công');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OptionGroup $optiongroup)
    {
        $optiongroup->load('optionValues');
        return view('admin.optiongroups.edit', compact('optiongroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OptionGroup $optiongroup)
    {
        $request->validate([
            'name'                 => 'required|string|max:100',
            'is_required'          => 'nullable|boolean',
            'is_multiple'          => 'nullable|boolean',
            'values'               => 'required|array|min:1',
            'values.*.name'        => 'required|string|max:100',
            'values.*.extra_price' => 'nullable|integer|min:0',
        ]);

        $optiongroup->update([
            'name'        => $request->name,
            'is_required' => $request->has('is_required'),
            'is_multiple' => $request->has('is_multiple'),
        ]);

        // Xoá values cũ → tạo lại values mới
        $optiongroup->optionValues()->delete();

        foreach ($request->values as $value) {
            OptionValue::create([
                'option_group_id' => $optiongroup->id,
                'name'            => $value['name'],
                'extra_price'     => $value['extra_price'] ?? 0,
            ]);
        }

        return redirect()->route('admin.optiongroups.index')
            ->with('success', 'Cập nhật Option Group thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OptionGroup $optiongroup)
    {
        $optiongroup->delete();
        return redirect()->route('admin.optiongroups.index')->with('success', 'Xóa Group Option thành công');
    }
}
