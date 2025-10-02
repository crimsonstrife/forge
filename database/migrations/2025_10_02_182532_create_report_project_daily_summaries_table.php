<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('report_project_daily_summaries', static function (Blueprint $t): void {
            $t->uuid('id')->primary();
            $t->foreignUuid('project_id')->index();
            $t->date('report_date')->index();

            $t->unsignedInteger('open_count')->default(0);
            $t->unsignedInteger('wip_count')->default(0);
            $t->unsignedInteger('done_count')->default(0);
            $t->unsignedInteger('throughput_count')->default(0);

            $t->unsignedInteger('median_cycle_time_minutes')->default(0);
            $t->unsignedInteger('p75_cycle_time_minutes')->default(0);

            $t->timestamps();
            $t->unique(['project_id', 'report_date'], 'uniq_project_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_project_daily_summaries');
    }
};
