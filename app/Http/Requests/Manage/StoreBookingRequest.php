<?php

namespace App\Http\Requests\Manage;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin','staff']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'table_id'    => 'required|exists:tables,id',
            'guest_name'  => 'nullable|string|max:100',
            'guest_phone' => 'nullable|string|max:20',
            'guest_count' => 'required|integer|min:1',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date',
            'note'        => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'table_id.required'    => 'Vui lòng chọn bàn có tại nhà hàng',
            'guest_count.required' => 'Vui lòng nhập số lượng khách',
            'start_time.required'  => 'Phải có thời gian bắt đầu',
            'end_time.required'    => 'Phải có thời gian kết thúc',
            // end_time phải sau start_time
            'end_time.after'       => 'Thời gian kết thúc trước thời gian bắt đầu',
        ];
    }
}
