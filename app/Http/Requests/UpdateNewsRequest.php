<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'        => ['required','string','max:200'],
            'category'     => ['required','string','max:50'],
            'excerpt'      => ['nullable','string','max:255'],
            'body'         => ['required','string'],
            'status'       => ['required','in:draft,published'],
            'published_at' => ['nullable','date'],
            'cover'        => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title'=>'หัวข้อ','category'=>'หมวดหมู่','excerpt'=>'คำเกริ่น',
            'body'=>'เนื้อหา','cover'=>'รูปหน้าปก','status'=>'สถานะ','published_at'=>'วันที่เผยแพร่',
        ];
    }
}
