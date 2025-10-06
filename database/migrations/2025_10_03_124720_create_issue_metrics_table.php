<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('issue_metrics', static function (Blueprint $t): void {
            $t->uuid('issue_id')->primary();              // one row per issue
            $t->foreignUuid('project_id')->index();

            $t->timestamp('first_started_at')->nullable()->index();
            $t->timestamp('first_done_at')->nullable()->index();

            $t->unsignedInteger('lead_time_min')->default(0);   // created_at -> first_done_at
            $t->unsignedInteger('cycle_time_min')->default(0);  // first_started_at -> first_done_at (or now if not done)
            $t->unsignedInteger('age_min')->default(0);         // created_at -> now if not done

            $t->unsignedBigInteger('current_status_id')->nullable()->index();
            $t->boolean('is_done')->default(false)->index();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_metrics');
    }
};
