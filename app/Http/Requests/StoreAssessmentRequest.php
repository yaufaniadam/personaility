<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers'                   => ['required', 'array', 'min:1'],
            'answers.*.question_id'     => ['required', 'integer', 'exists:questions,id'],
            'answers.*.likert_score'    => ['required', 'integer', 'min:1', 'max:5'],
            'answers.*.note_text'       => ['nullable', 'string', 'max:1000'],
            'consent'                   => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required'                 => 'Please answer all questions.',
            'answers.*.likert_score.between'   => 'Each answer must be between 1 and 5.',
            'answers.*.question_id.exists'     => 'One or more questions are invalid.',
            'consent.accepted'                 => 'You must provide consent before proceeding.',
        ];
    }
}
