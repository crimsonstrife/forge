<?php

namespace App\Http\Requests\Api\V1\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class AdminConvertIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'uuid', 'exists:projects,id'],
            'issue_type_id' => ['nullable', 'integer', 'exists:issue_types,id'],
            'priority_id' => ['nullable', 'integer', 'exists:issue_priorities,id'],
            'mark_planned' => ['sometimes', 'boolean'],
        ];
    }
}
