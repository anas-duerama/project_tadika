<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'no','class_level','first_name','last_name','student_code','citizen_id',
        'birthdate','father_name','father_job','mother_name','mother_job','phone',
        'address','photo_path','status','room'
    ];

    public function grades()
{
    return $this->hasMany(Grade::class);
}
    
} 
   