<?php

namespace App\Http\Requests\Api\V1\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class AdminPostUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'body' => ['sometimes', 'required', 'string', 'max:10000'],
            'category_id' => ['nullable', 'ulid', 'exists:feedback_categories,id'],
        ];
    }
}
