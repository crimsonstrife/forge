<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('issue_notification_preferences', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users', 'id')->cascadeOnDelete();
            $table->boolean('notify_on_assignment')->default(true);
            $table->boolean('notify_on_comment')->default(true);
            $table->boolean('notify_on_status_change')->default(true);
            $table->boolean('notify_on_link_change')->default(true);
            $table->boolean('notify_on_mention')->default(true);
            $table->boolean('daily_digest_enabled')->default(false);
            $table->timestamp('daily_digest_last_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_notification_preferences');
    }
};
