<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('issue')) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'summary'     => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'type_id'     => ['sometimes', 'integer', 'exists:issue_types,id'],
            'status_id'   => ['sometimes', 'integer', 'exists:issue_statuses,id'],
            'priority_id' => ['sometimes', 'integer', 'exists:issue_priorities,id'],
            'assignee_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'parent_id'   => ['sometimes', 'nullable', 'uuid', 'exists:issues,id'],
            'tags'        => ['sometimes', 'array'],
            'tags.*'      => ['string'],
        ];
    }
}
