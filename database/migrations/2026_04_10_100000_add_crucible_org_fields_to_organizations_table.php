<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('crucible_org_id')->nullable()->unique()->after('slug');
            $table->string('crucible_org_slug')->nullable()->after('crucible_org_id');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropUnique(['crucible_org_id']);
            $table->dropColumn(['crucible_org_id', 'crucible_org_slug']);
        });
    }
};
