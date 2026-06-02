<?php

namespace App\Http\Requests\Api\V1\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class AuthVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'display_name' => ['nullable', 'string', 'min:2', 'max:80'],
        ];
    }
}
