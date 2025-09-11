<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Issue;
use Illuminate\Foundation\Http\FormRequest;

final class StoreIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Issue::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->normalizeIssueKeys($this->all()));
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'project_id'        => ['required', 'uuid', 'exists:projects,id'],
            'summary'           => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],

            'issue_type_id'     => ['required', 'integer', 'exists:issue_types,id'],
            'issue_priority_id' => ['nullable', 'integer', 'exists:issue_priorities,id'],
            'issue_status_id'   => ['nullable', 'integer', 'exists:issue_statuses,id'],

            'assignee_id'       => ['nullable', 'uuid', 'exists:users,id'],
            'parent_id'         => ['nullable', 'uuid', 'exists:issues,id'],
            'tags'              => ['nullable', 'array'],
            'tags.*'            => ['string'],
        ];
    }

    /**
     * Map friendly API keys to DB columns.
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
