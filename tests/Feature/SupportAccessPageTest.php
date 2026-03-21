<?php

namespace Tests\Feature;

use App\Models\SupportIdentity;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportAccessPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_access_page_shows_ticket_list_when_support_cookie_is_present(): void
    {
        $identity = SupportIdentity::query()->create([
            'email_encrypted' => 'jamie@example.com',
            'email_hash' => hash('sha256', 'jamie@example.com'),
            'token' => (string) str()->ulid(),
        ]);

        Ticket::query()->create([
            'organization_id' => null,
            'service_product_id' => null,
            'project_id' => null,
            'submitter_name' => 'Jamie Customer',
            'submitter_email' => 'jamie@example.com',
            'email_hash' => $identity->email_hash,
            'subject' => 'Portal login is failing',
            'body' => 'The customer portal rejects my sign-in every time.',
            'status_id' => TicketStatus::query()->where('name', 'New')->value('id'),
            'priority_id' => TicketPriority::query()->where('name', 'Medium')->value('id'),
            'type_id' => TicketType::query()->where('name', 'Question')->value('id'),
            'access_token' => (string) str()->ulid(),
            'via' => 'public',
        ]);

        $this->withCookie('support_identity', $identity->getKey())
            ->get(route('support.access.request'))
            ->assertOk()
            ->assertSee('My support tickets')
            ->assertSee('Portal login is failing');
    }

    public function test_access_page_shows_guidance_when_support_cookie_is_missing(): void
    {
        $this->get(route('support.access.request'))
            ->assertOk()
            ->assertSee('Access your tickets')
            ->assertSee('Use the magic link we emailed to you')
            ->assertDontSee('My support tickets');
    }
}
