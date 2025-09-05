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
        Schema::create('project_repositories', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('repository_id')->constrained()->cascadeOnDelete();

            // who authorized the connection; token is used for initial import + webhook validation if needed
            $table->foreignUuid('integrator_user_id')->nullable()->constrained('users')->nullOnDelete();

            // store an installation/app/pat token if using app-level auth (encrypted cast)
            $table->text('token')->nullable();        // cast: encrypted
            $table->string('token_type')->nullable(); // installation|pat|oauth
            $table->timestamp('token_expires_at')->nullable();

            // status
            $table->timestamp('initial_import_started_at')->nullable();
            $table->timestamp('initial_import_finished_at')->nullable();
            $table->string('last_sync_status')->nullable(); // ok|error
            $table->text('last_sync_error')->nullable();

            $table->timestamps();
            $table->unique(['project_id', 'repository_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_repositories');
    }
};
