<?php

namespace App\Models;

use App\Traits\QueryInstitution;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends BaseModel
{
    use HasFactory, QueryInstitution;
    protected $casts = [
        'institution_id' => 'integer',
        'exam_content_id' => 'integer',
    ];

    public function canDelete()
    {
        return $this->sessions()->count();
    }

    function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    function courseSessions()
    {
        return $this->hasMany(CourseSession::class);
    }
}
