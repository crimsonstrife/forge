<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('issue')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->normalizeIssueKeys($this->all()));
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'summary'           => ['sometimes', 'string', 'max:255'],
            'description'       => ['sometimes', 'nullable', 'string'],

            'issue_type_id'     => ['sometimes', 'integer', 'exists:issue_types,id'],
            'issue_priority_id' => ['sometimes', 'integer', 'exists:issue_priorities,id'],
            'issue_status_id'   => ['sometimes', 'integer', 'exists:issue_statuses,id'],

            'assignee_id'       => ['sometimes', 'nullable', 'uuid', 'exists:users,id'],
            'parent_id'         => ['sometimes', 'nullable', 'uuid', 'exists:issues,id'],
            'tags'              => ['sometimes', 'array'],
            'tags.*'            => ['string'],
        ];
    }

    /**
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    private function normalizeIssueKeys(array $data): array
    {
        $map = [
            'type_id'     => 'issue_type_id',
            'status_id'   => 'issue_status_id',
            'priority_id' => 'issue_priority_id',
        ];

        foreach ($map as $friendly => $dbKey) {
            if (array_key_exists($friendly, $data) && ! array_key_exists($dbKey, $data)) {
                $data[$dbKey] = $data[$friendly];
            }
        }

        return $data;
    }
}
