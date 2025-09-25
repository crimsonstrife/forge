<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('projects', static function (Blueprint $table): void {
            $table->boolean('public_tracker_enabled')->default(false)->after('description');
            $table->string('public_slug')->nullable()->unique()->after('public_tracker_enabled');
            $table->boolean('count_private_in_progress')->default(true)->after('public_slug');
            $table->json('embed_domains')->nullable()->after('count_private_in_progress'); // allowlist for frame-ancestors
        });

        Schema::table('issues', static function (Blueprint $table): void {
            $table->boolean('is_public')->default(false)->index()->after('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('issues', static function (Blueprint $table): void {
            $table->dropColumn('is_public');
        });

        Schema::table('projects', static function (Blueprint $table): void {
            $table->dropColumn(['public_tracker_enabled', 'public_slug', 'count_private_in_progress', 'embed_domains']);
        });
    }
};
