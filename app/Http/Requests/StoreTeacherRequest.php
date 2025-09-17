<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'      => ['required','string','max:100'],
            'last_name'       => ['required','string','max:100'],
            'email'           => ['nullable','email','max:150','unique:teachers,email'],
            'phone'           => ['nullable','string','max:20'],
            'primary_subject' => ['nullable','string','max:100'],
            'department'      => ['nullable','string','max:100'],
            'homeroom'        => ['nullable','string','max:20'],
            'hire_date'       => ['nullable','date'],
            'bio'             => ['nullable','string'],
            'status'          => ['required','in:active,inactive'],
            'photo'           => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ];
    }
}
