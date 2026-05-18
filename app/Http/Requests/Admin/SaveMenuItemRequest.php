<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SaveMenuItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $menuitem = $this->route('menuitem');

        return [
            'name' => [
                'required',
                $menuitem
                    ? Rule::unique('menu_items', 'name')->ignore($menuitem->id)
                    : Rule::unique('menu_items', 'name'),
            ],
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
            'price'       => 'required|integer|min:20000',
            'status'      => 'required|in:active,out_of_stock,inactive',
            'image'       => 'nullable|image|max:2048',
            'option_groups'   => 'nullable|array',
            'option_groups.*' => 'exists:option_groups,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Vui lòng nhập tên món ăn',
            'name.unique'          => 'Tên món ăn đã tồn tại',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'price.required'       => 'Vui lòng nhập giá',
            'price.min'            => 'Giá tối thiểu là 20,000đ',
            'status.required'      => 'Vui lòng chọn trạng thái',
            'image.image'          => 'File phải là hình ảnh',
            'image.max'            => 'Hình ảnh tối đa 2MB',
        ];
    }
}
