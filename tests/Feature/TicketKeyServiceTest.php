<?php

namespace Tests\Feature;

use App\Services\Support\TicketKeyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketKeyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_initializes_and_increments_the_sequence_from_existing_ticket_keys(): void
    {
        $this->insertTicket('SD-1007');
        $this->insertTicket('SD-1042');
        $this->insertTicket('OPS-12');

        $service = new TicketKeyService('sd');

        $this->assertSame('SD-1043', $service->nextKey());
        $this->assertSame('SD-1044', $service->nextKey());

        $this->assertDatabaseHas('ticket_key_sequences', [
            'prefix' => 'SD',
            'current_value' => 1044,
        ]);
    }

    private function insertTicket(string $key): void
    {
        $email = Str::slug($key).'@example.test';

        DB::table('tickets')->insert([
            'id' => (string) Str::ulid(),
            'key' => $key,
            'organization_id' => null,
            'service_product_id' => null,
            'project_id' => null,
            'submitter_user_id' => null,
            'submitter_name' => 'Support User',
            'submitter_email' => $email,
            'email_hash' => hash('sha256', $email),
            'subject' => 'Support request',
            'body' => 'Need help with a support request.',
            'status_id' => DB::table('ticket_statuses')->value('id'),
            'priority_id' => DB::table('ticket_priorities')->value('id'),
            'type_id' => DB::table('ticket_types')->value('id'),
            'assigned_to_user_id' => null,
            'access_token' => null,
            'via' => 'portal',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}
