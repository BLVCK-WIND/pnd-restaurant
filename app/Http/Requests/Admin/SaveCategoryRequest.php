<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SaveCategoryRequest extends FormRequest
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
        $category = $this->route('category');
        return [
            'name' => [
                'required',
                $category
                    ? Rule::unique('categories', 'name')->ignore($category->id)
                    : Rule::unique('categories', 'name'),
            ],
            'description' => 'nullable',
            'sort_order'  => 'nullable|integer|min:1',
            'is_active'   => 'nullable|boolean',
            'image'       => 'nullable|image|max:2048',
            'option_groups'   => 'nullable|array',
            'option_groups.*' => 'exists:option_groups,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Vui lòng nhập tên danh mục',
            'name.unique'          => 'Tên danh mục đã tồn tại',
            'image.image'          => 'File phải là hình ảnh',
            'image.max'            => 'Hình ảnh tối đa 2MB',
            'sort_order.min'       => 'STT phải lớn hơn 0',
        ];
    }
}
