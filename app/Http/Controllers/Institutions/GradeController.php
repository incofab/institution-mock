<?php
namespace App\Http\Controllers\Institutions;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Institution;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    function index(Institution $institution)
    {
        return view('institutions.grades.index', [
            'allRecords' => paginateFromRequest(Grade::query()),
        ]);
    }

    function create(Institution $institution)
    {
        return view('institutions.grades.create', ['edit' => null]);
    }

    function store(Institution $institution, Request $request)
    {
        $data = $request->validate(Grade::ruleCreate());
        Grade::query()->firstOrCreate(
            $request->only('institution_id', 'title'),
            $data,
        );
        return redirect(instRoute('grades.index'))->with(
            'message',
            'Data recorded successfully',
        );
    }

    function edit(Institution $institution, Grade $grade)
    {
        return view('institutions.grades.create', ['edit' => $grade]);
    }

    function update(Request $request, Institution $institution, Grade $grade)
    {
        $data = $request->validate(Grade::ruleCreate());
        $grade->fill($data)->save();

        return redirect(instRoute('grades.index'))->with(
            'message',
            'Record updated',
        );
    }

    function destroy(Institution $institution, Grade $grade)
    {
        abort_if(
            $grade->students()->exists(),
            401,
            'There are students in this class',
        );
        $grade->delete();
        return redirect(instRoute('grades.index'))->with(
            'message',
            'Record deleted',
        );
    }
}
