<?php
namespace App\Actions;

use App\Models\Institution;
use App\Models\Student;

class CreateStudent
{
    function __construct(private Institution $institution)
    {
    }

    function run(array $post)
    {
        $this->institution
            ->students()
            ->create([...$post, 'code' => Student::generateCode()]);
    }
}
