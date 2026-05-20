<?php

namespace App\Http\Requests\Api\V1\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class AdminMergeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'target_post_id' => ['required', 'ulid', 'exists:feedback_posts,id'],
        ];
    }
}
