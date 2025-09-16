<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class IssueTransitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('issue')) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'to_status_id' => ['required', 'integer', 'exists:issue_statuses,id'],
        ];
    }
}
