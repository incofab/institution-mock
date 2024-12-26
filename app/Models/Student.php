<?php

namespace App\Models;

use App\Traits\QueryInstitution;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends BaseModel
{
    use HasFactory, QueryInstitution;

    protected $appends = ['name'];
    protected $casts = [
        'institution_id' => 'integer',
        // 'user_id' => 'integer',
        'grade_id' => 'integer',
    ];
    static function ruleCreate($prefix = '')
    {
        return [
            $prefix . 'grade_id' => ['nullable', 'integer'],
            $prefix . 'institution_id' => ['nullable', 'integer'],
            $prefix . 'firstname' => ['required', 'string'],
            $prefix . 'lastname' => ['required', 'string'],
            // 'reference' => ['required', 'unique:students,reference'],
        ];
    }

    static function generateCode()
    {
        $prefix = 'S-';
        $key = $prefix . rand(1000000, 9999999);
        while (Student::where('code', '=', $key)->first()) {
            $key = $prefix . rand(1000000, 9999999);
        }
        return $key;
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->firstname} {$this->lastname}",
        );
    }

    function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
