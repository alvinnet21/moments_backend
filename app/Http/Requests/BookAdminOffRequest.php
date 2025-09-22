<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\TimeSlot;

class BookAdminOffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id,deleted_at,NULL'],
            'date' => ['required', 'integer', 'min:0'],
            'time_slot' => ['required', new Enum(TimeSlot::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'exists' => 'Data not found',
            'required' => ':attribute is required',
        ];
    }
}

