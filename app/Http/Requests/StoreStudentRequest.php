<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ให้ส่งได้ (ถ้ามี auth/role คุมที่ route แล้ว)
    }

    public function rules(): array
    {
        return [
            'no'           => ['required','integer','min:1'],
            'class_level'  => ['required','string','max:50'],
            'first_name'   => ['required','string','max:100'],
            'last_name'    => ['required','string','max:100'],
            'student_code' => ['required','string','max:50','unique:students,student_code'],
            'citizen_id'   => ['required','digits:13','unique:students,citizen_id'],
            'birthdate'    => ['nullable','date'],
            'father_name'  => ['nullable','string','max:100'],
            'father_job'   => ['nullable','string','max:100'],
            'mother_name'  => ['nullable','string','max:100'],
            'mother_job'   => ['nullable','string','max:100'],
            'phone'        => ['nullable','regex:/^0\d{8,9}$/'],
        ];
    }

    public function attributes(): array
    {
        // ไว้แปลชื่อฟิลด์เป็นภาษาไทยใน error message
        return [
            'no' => 'เลขที่',
            'class_level' => 'ชั้นเรียน',
            'first_name' => 'ชื่อ',
            'last_name' => 'นามสกุล',
            'student_code' => 'เลขประจำตัวนักเรียน',
            'citizen_id' => 'เลขบัตรประชาชน',
            'birthdate' => 'วันเกิด',
            'father_name' => 'ชื่อบิดา',
            'father_job' => 'อาชีพบิดา',
            'mother_name' => 'ชื่อมารดา',
            'mother_job' => 'อาชีพมารดา',
            'phone' => 'เบอร์โทร',
        ];
    }
}
