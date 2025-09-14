<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimeEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        // Accept either dedicated time permission or the existing issues:write ability.
        return $user->tokenCan('time:write') || $user->tokenCan('issues:write');
    }

    public function rules(): array
    {
        return [
            // Option A: just seconds (1..24h)
            'seconds' => ['required_without_all:started_at,ended_at', 'nullable', 'integer', 'min:1', 'max:86400'],

            // Option B: explicit window
            'started_at' => ['required_without:seconds', 'nullable', 'date'],
            'ended_at'   => ['required_without:seconds', 'nullable', 'date', 'after:started_at'],

            // Optional note
            'note' => ['nullable', 'string', 'max:10000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('seconds')) {
            $this->merge(['seconds' => (int) $this->input('seconds')]);
        }

        if ($this->filled('note') && is_string($this->note)) {
            $this->merge(['note' => trim($this->note)]);
        }
    }

    public function messages(): array
    {
        return [
            'seconds.required_without_all' => 'Provide either seconds or both started_at and ended_at.',
            'started_at.required_without'  => 'Provide started_at with ended_at or use seconds.',
            'ended_at.required_without'    => 'Provide ended_at with started_at or use seconds.',
            'ended_at.after'               => 'ended_at must be after started_at.',
        ];
    }
}
