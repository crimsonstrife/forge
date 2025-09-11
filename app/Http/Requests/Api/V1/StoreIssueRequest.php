<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class StoreIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Issue::class) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'project_id'  => ['required', 'uuid', 'exists:projects,id'],
            'summary'     => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type_id'     => ['required', 'integer', 'exists:issue_types,id'],
            'status_id'   => ['nullable', 'integer', 'exists:issue_statuses,id'],
            'priority_id' => ['nullable', 'integer', 'exists:issue_priorities,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'parent_id'   => ['nullable', 'uuid', 'exists:issues,id'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['string'],
        ];
    }
}
