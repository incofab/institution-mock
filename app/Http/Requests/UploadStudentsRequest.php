<?php

namespace App\Http\Requests;

use App\Actions\Sheet\ConvertSheetToArray;
use App\Models\Grade;
use App\Models\Student;
use App\Rules\ExcelRule;
use App\Rules\ValidateExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadStudentsRequest extends FormRequest
{
  protected function prepareForValidation()
  {
    if ($this->file) {
      $columnKeyMapping = [
        'A' => 'firstname',
        'B' => 'lastname',
        'C' => 'phone',
        'D' => 'email',
        // 'E' => new SheetValueHandler(
        //   'grade_title',
        //   fn($val) => empty($val) ? null : $val,
        // ),
        'E' => 'grade_title',
      ];
      $this->merge([
        'students' => (new ConvertSheetToArray(
          $this->file,
          $columnKeyMapping,
        ))->run(),
      ]);
    }
  }

  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
   */
  public function rules(): array
  {
    return [
      'file' => ['required', 'file', new ExcelRule($this->file('file'))],
      'students' => ['required', 'array', 'min:1'],
      ...Student::ruleCreate('students.*.'),
      'students.*.grade_title' => [
        'nullable',
        new ValidateExistsRule(Grade::class, 'title'),
      ],
    ];
  }
}
