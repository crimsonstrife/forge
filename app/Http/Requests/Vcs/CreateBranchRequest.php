<?php

namespace App\Http\Requests\Vcs;

use Illuminate\Foundation\Http\FormRequest;

final class CreateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'repository_id' => ['required','uuid','exists:repositories,id'],
            'name' => ['required','string','max:200'],
            'from_ref' => ['nullable','string','max:200'], // e.g., default branch or any ref name
        ];
    }
}
