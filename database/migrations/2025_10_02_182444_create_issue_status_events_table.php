<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('issue_status_events', static function (Blueprint $t): void {
            $t->uuid('id')->primary();
            $t->foreignUuid('issue_id')->index();
            $t->foreignId('from_status_id')->nullable()->index();
            $t->foreignId('to_status_id')->index();
            $t->foreignUuid('changed_by_id')->nullable()->index();
            $t->timestamp('changed_at')->index();
            $t->timestamps();

            $t->unique(['issue_id', 'to_status_id', 'changed_at'], 'uniq_issue_to_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_status_events');
    }
};
