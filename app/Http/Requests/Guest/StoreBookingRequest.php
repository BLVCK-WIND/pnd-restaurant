<?php

namespace App\Http\Requests\Guest;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'guest';
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
            'guest_name'  => 'required|string|max:100',
            'guest_phone' => 'required|string|max:20',
            'guest_count' => 'required|integer|min:1',
            'start_time'  => 'required|date|after:now',
            'end_time'    => 'required|date|after:start_time',
            'note'        => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'table_id.required'    => 'Vui lòng chọn bàn',
            'table_id.exists'      => 'Bàn không tồn tại',
            'guest_name.required'  => 'Vui lòng nhập họ tên',
            'guest_phone.required' => 'Vui lòng nhập số điện thoại',
            'guest_count.required' => 'Vui lòng nhập số khách',
            'guest_count.min'      => 'Số khách tối thiểu là 1',
            'start_time.required'  => 'Vui lòng chọn thời gian đến',
            'start_time.after'     => 'Thời gian đến phải sau thời điểm hiện tại',
            'end_time.after'       => 'Thời gian về phải sau thời gian đến',
        ];
    }

    public function withValidator($validator):void
    {
        $validator->after(function ($validator){
            $start = Carbon::parse($this->start_time);
            $end   = Carbon::parse($this->end_time);

            if ($end->diffInHours($start) > 3) {
                $validator->errors()->add(
                    'end_time','thời gian đặt bàn tối đa 3 tiếng'
                );
            }
        });
    }
}
