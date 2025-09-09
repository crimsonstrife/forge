<?php

namespace App\Http\Requests\Vcs;

use Illuminate\Foundation\Http\FormRequest;

final class SearchVcsRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'repository_id' => ['required','uuid','exists:repositories,id'],
            'q' => ['nullable','string','max:200'],
        ];
    }
}
