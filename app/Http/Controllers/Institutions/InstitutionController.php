<?php
namespace App\Http\Controllers\Institutions;

use App\Http\Controllers\Controller;
use App\Models\Institution;

class InstitutionController extends Controller
{
    function index(Institution $institution)
    {
        return view('institutions.index', [
            'students_count' => $institution->students()->count(),
            'events_count' => $institution->events()->count(),
        ]);
    }
}
