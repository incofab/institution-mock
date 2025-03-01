<?php

namespace App\Http\Requests;

use App\Actions\Sheet\ConvertSheetToArray;
use App\Models\Question;
use App\Rules\ExcelRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadSessionQuestionsRequest extends FormRequest
{
  protected function prepareForValidation()
  {
    if ($this->file) {
      $columnKeyMapping = [
        'A' => 'question_no',
        'B' => 'question',
        'C' => 'option_a',
        'D' => 'option_b',
        'E' => 'option_c',
        'F' => 'option_d',
        'G' => 'option_e',
        'H' => 'answer',
      ];
      $this->merge([
        'questions' => (new ConvertSheetToArray(
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
      'questions' => ['required', 'array', 'min:1'],
      ...Question::createRule(null, 'questions.*.'),
    ];
  }
}
