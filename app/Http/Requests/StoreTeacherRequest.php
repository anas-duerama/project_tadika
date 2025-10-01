<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'      => ['nullable','string','max:100'],
            'last_name'       => ['nullable','string','max:100'],
            'email'           => ['nullable','email','max:150','unique:teachers,email'],
            'phone'           => ['nullable','string','max:20'],
            'primary_subject' => ['nullable','string','max:100'],
            'user_id'         => ['nullable','exists:users,id'],
            'department'      => ['nullable','string','max:100'],
            'homeroom'        => ['nullable','string','max:20'],
            'teaching_rooms'  => ['nullable','array'],
            'teaching_rooms.*'=> ['string','max:100'],
            'hire_date'       => ['nullable','date'],
            'bio'             => ['nullable','string'],
            'status'          => ['nullable','in:active,inactive'],
            'photo'           => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ];
    }
}
