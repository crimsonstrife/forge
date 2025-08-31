<?php

namespace App\Http\Requests\Goals;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('goals.create') ?? false; }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required','string','min:3','max:200'],
            'goal_type' => ['required','in:objective,kpi,smart,initiative'],
            'status' => ['required','in:draft,active,paused,completed,canceled'],
            'description' => ['nullable','string','max:5000'],
            'start_date' => ['nullable','date'],
            'due_date' => ['nullable','date','after_or_equal:start_date'],
            'owner_type' => ['required','string'],
            'owner_id' => ['required','uuid'],
        ];
    }
}
