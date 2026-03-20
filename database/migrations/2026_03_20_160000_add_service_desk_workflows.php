<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_products', static function (Blueprint $table): void {
            $table->unsignedInteger('first_response_target_minutes')->nullable();
            $table->unsignedInteger('next_response_target_minutes')->nullable();
            $table->unsignedInteger('resolve_target_minutes')->nullable();
            $table->foreignId('auto_create_issue_for_ticket_type_id')->nullable()->index();
            $table->foreignUuid('auto_create_issue_project_id')->nullable()->index();
        });

        Schema::table('tickets', static function (Blueprint $table): void {
            $table->timestamp('first_response_due_at')->nullable()->index();
            $table->timestamp('first_responded_at')->nullable();
            $table->timestamp('next_response_due_at')->nullable()->index();
            $table->timestamp('last_customer_reply_at')->nullable();
            $table->timestamp('last_staff_reply_at')->nullable();
            $table->timestamp('resolve_due_at')->nullable()->index();
            $table->timestamp('resolved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', static function (Blueprint $table): void {
            $table->dropColumn([
                'first_response_due_at',
                'first_responded_at',
                'next_response_due_at',
                'last_customer_reply_at',
                'last_staff_reply_at',
                'resolve_due_at',
                'resolved_at',
            ]);
        });

        Schema::table('service_products', static function (Blueprint $table): void {
            $table->dropColumn([
                'first_response_target_minutes',
                'next_response_target_minutes',
                'resolve_target_minutes',
                'auto_create_issue_for_ticket_type_id',
                'auto_create_issue_project_id',
            ]);
        });
    }
};
