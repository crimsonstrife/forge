<?php

namespace App\Http\Requests\Api\V1\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class AdminStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'status_slug' => ['required', 'string', 'max:255'],
        ];
    }
}
