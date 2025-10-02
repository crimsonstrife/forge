<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('report_sprint_daily_summaries', static function (Blueprint $t): void {
            $t->uuid('id')->primary();
            $t->foreignUuid('project_id')->index();
            $t->foreignUuid('sprint_id')->index();
            $t->date('report_date')->index();

            $t->unsignedInteger('remaining_points')->default(0);
            $t->unsignedInteger('remaining_issues')->default(0);
            $t->timestamps();

            $t->unique(['project_id', 'sprint_id', 'report_date'], 'uniq_project_sprint_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_sprint_daily_summaries');
    }
};
