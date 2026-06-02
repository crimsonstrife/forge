<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('issue_external_refs', static function (Blueprint $table): void {
            $table->foreignUuid('repository_id')->nullable()->change();
            $table->string('external_issue_id')->nullable()->change();
            $table->unsignedBigInteger('number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('issue_external_refs', static function (Blueprint $table): void {
            $table->foreignUuid('repository_id')->nullable(false)->change();
            $table->string('external_issue_id')->nullable(false)->change();
            $table->unsignedBigInteger('number')->nullable(false)->change();
        });
    }
};
