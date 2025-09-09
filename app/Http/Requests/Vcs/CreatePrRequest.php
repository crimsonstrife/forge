<?php

namespace App\Http\Requests\Vcs;

use Illuminate\Foundation\Http\FormRequest;

final class CreatePrRequest extends FormRequest
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
            'title' => ['required','string','max:300'],
            'head'  => ['required','string','max:200'], // branch/ref name
            'base'  => ['required','string','max:200'], // usually default branch
            'body'  => ['nullable','string','max:10000'],
        ];
    }
}
