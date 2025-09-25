<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('import_export_records', function (Blueprint $t): void {
            $t->id();
            $t->uuid('external_id')->nullable()->unique()->index();
            $t->foreignUuid('project_id')->nullable()->constrained()->nullOnDelete();
            $t->string('direction'); // 'export' | 'import'
            $t->string('status')->default('queued'); // queued|running|success|failed
            $t->string('file_path')->nullable(); // storage path to .forgepkg
            $t->json('options')->nullable();
            $t->json('report')->nullable(); // validation warnings/errors
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_export_records');
    }
};
