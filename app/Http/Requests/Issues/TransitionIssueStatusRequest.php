<?php

namespace App\Http\Requests\Issues;

use App\Models\Issue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransitionIssueStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Issue|null $issue */
        $issue = $this->route('issue');

        return $issue !== null && $this->user()?->can('update', $issue) === true;
    }

    public function rules(): array
    {
        return [
            'to_status_id' => ['required', 'integer', Rule::exists('issue_statuses', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'to_status_id.required' => 'Choose a status to transition to.',
        ];
    }
}
