<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Ticket */
final class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => (string) $this->getKey(),
            'key'       => $this->key ?? null, // if you have a human key like FORGE-123
            'summary'   => $this->summary,
            'status'    => $this->status->only(['id', 'name']) ?? null,
            'priority'  => $this->priority->only(['id', 'name']) ?? null,
            'type'      => $this->type->only(['id', 'name']) ?? null,
            'product'   => $this->serviceProduct?->only(['id','name']),
            'created_at'=> $this->created_at?->toIso8601String(),
            'links'     => [
                'html' => route('tickets.show', ['ticket' => $this->getKey()], false) ?? null,
            ],
        ];
    }
}
