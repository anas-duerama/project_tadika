<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('student')->id ?? null; // id ของรายการที่แก้

        return [
            'no'           => ['required','integer','min:1'],
            'class_level'  => ['required','string','max:50'],
            'first_name'   => ['required','string','max:100'],
            'last_name'    => ['required','string','max:100'],
            'student_code' => ['required','string','max:50', Rule::unique('students','student_code')->ignore($id)],
            'citizen_id'   => ['required','digits:13',         Rule::unique('students','citizen_id')->ignore($id)],
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
        return [
            'no'=>'เลขที่','class_level'=>'ห้อง','first_name'=>'ชื่อ','last_name'=>'นามสกุล',
            'student_code'=>'เลขประจำตัวนักเรียน','citizen_id'=>'เลขบัตรประชาชน',
            'birthdate'=>'วันเกิด','father_name'=>'ชื่อบิดา','father_job'=>'อาชีพบิดา',
            'mother_name'=>'ชื่อมารดา','mother_job'=>'อาชีพมารดา','phone'=>'เบอร์โทร',
        ];
    }
}
