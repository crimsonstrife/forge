<?php

namespace App\Http\Requests\Api\V1\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class AdminIdentityBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'blocked' => ['required', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
