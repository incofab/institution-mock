<?php
namespace App\Http\Controllers\CCD;

use App\Http\Controllers\Controller;
use App\Models\ExamContent;
use App\Models\Institution;
use Illuminate\Http\Request;

class ExamContentController extends Controller
{
  public function index(Institution $institution)
  {
    return view('ccd.exam-contents.index', [
      'allRecords' => paginateFromRequest(ExamContent::query()),
    ]);
  }

  public function create(Institution $institution)
  {
    return view('ccd.exam-contents.create');
  }

  public function store(Request $request, Institution $institution)
  {
    $validatedData = $request->validate([
      'exam_name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'fullname' => 'nullable|string:max:255',
    ]);

    $institution->examContents()->create($validatedData);

    return back()->with('message', 'Data recorded successfully');
  }

  public function edit(Institution $institution, ExamContent $examContent)
  {
    return view('ccd.exam-contents.create', ['edit' => $examContent]);
  }

  public function update(
    Request $request,
    Institution $institution,
    ExamContent $examContent,
  ) {
    $validatedData = $request->validate([
      'exam_name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'fullname' => 'nullable|string:max:255',
    ]);

    $examContent->update($validatedData);

    return back()->with('message', 'Record updated successfully');
  }

  public function destroy(Institution $institution, ExamContent $examContent)
  {
    abort_if(
      $examContent->courses()->exists(),
      403,
      'Cannot delete content with subject(s)',
    );
    $examContent->delete();

    return back()->with('message', 'Data deleted successfully');
  }
}
