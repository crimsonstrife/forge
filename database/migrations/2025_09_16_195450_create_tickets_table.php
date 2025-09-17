<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('key', 32)->unique()->index();
            $table->foreignUuid('organization_id')->index()->nullable();
            $table->foreignUlid('service_product_id')->nullable()->index();
            $table->foreignUuid('project_id')->nullable()->index();

            $table->foreignUuid('submitter_user_id')->nullable()->index();
            $table->string('submitter_name');
            $table->string('submitter_email');
            $table->char('email_hash', 64)->index();

            $table->string('subject', 160);
            $table->longText('body');
            $table->longText('redacted_body')->nullable();

            $table->foreignId('status_id')->constrained('ticket_statuses');
            $table->foreignId('priority_id')->constrained('ticket_priorities');
            $table->foreignId('type_id')->constrained('ticket_types');

            $table->foreignUuid('assigned_to_user_id')->nullable()->index();

            $table->ulid('access_token')->nullable()->unique()->index();
            $table->string('via', 24)->default('portal');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ticket_issue_links', static function (Blueprint $table): void {
            $table->foreignUlid('ticket_id')->index();
            $table->foreignUuid('issue_id')->index();
            $table->unique(['ticket_id', 'issue_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_issue_links');
        Schema::dropIfExists('tickets');
    }
};
