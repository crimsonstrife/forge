<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('report_cfd_snapshots', static function (Blueprint $t): void {
            $t->uuid('id')->primary();
            $t->foreignUuid('project_id')->index();
            $t->date('report_date')->index();
            $t->foreignId('issue_status_id')->index();
            $t->unsignedInteger('count')->default(0);
            $t->timestamps();

            $t->unique(['project_id', 'report_date', 'issue_status_id'], 'uniq_project_date_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_cfd_snapshots');
    }
};
