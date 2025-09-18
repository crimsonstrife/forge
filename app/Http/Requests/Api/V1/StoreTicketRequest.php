<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string $summary
 * @property-read string $description
 * @property-read string|null $email
 * @property-read string|null $player_id
 * @property-read string|null $build
 * @property-read string|null $platform
 * @property-read string|null $severity
 * @property-read array<string,mixed>|null $metadata
 * @property-read string|null $service_product_id
 */
final class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'summary'            => ['required', 'string', 'min:4', 'max:200'],
            'description'        => ['required', 'string', 'min:10', 'max:8000'],
            'service_product_id' => ['required', 'string', 'exists:service_products,id'],

            'email'              => ['nullable', 'email:rfc,dns', 'max:255'],
            'player_id'          => ['nullable', 'string', 'max:255'],
            'build'              => ['nullable', 'string', 'max:64'],
            'platform'           => ['nullable', 'string', 'max:32'], // e.g., pc, xbox, ps, switch, linux, macos
            'severity'           => ['nullable', 'in:trivial,minor,major,critical'],
            'metadata'           => ['nullable', 'array'],

            // Attachments via multipart/form-data:
            'attachments'        => ['nullable', 'array', 'max:5'],
            'attachments.*'      => ['file', 'mimetypes:text/plain,text/markdown,application/zip,application/x-zip-compressed,image/png,image/jpeg,video/mp4', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_product_id.exists' => 'Unknown product.',
        ];
    }
}
