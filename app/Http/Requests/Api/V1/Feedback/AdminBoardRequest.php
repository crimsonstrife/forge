<?php

namespace App\Http\Requests\Api\V1\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class AdminBoardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'service_product_id' => ['sometimes', 'required', 'ulid', 'exists:service_products,id'],
            'slug' => ['sometimes', 'required', 'string', 'max:255'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'public_url' => ['sometimes', 'required', 'url', 'max:255'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'is_public' => ['sometimes', 'boolean'],
            'allow_anonymous_read' => ['sometimes', 'boolean'],
            'default_sort' => ['sometimes', 'string', 'in:top,new,trending'],
        ];
    }
}
