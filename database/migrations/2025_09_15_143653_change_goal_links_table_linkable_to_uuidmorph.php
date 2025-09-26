<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::table('goal_links', static function (Blueprint $table): void {
            // Drop the composite unique that references the morph columns
            // Default name pattern: {table}_{cols}_unique
            $table->dropUnique('goal_links_goal_id_linkable_type_linkable_id_unique');

            // Remove old bigint morphs
            $table->dropMorphs('linkable');

            // Re-add as UUID morphs
            $table->uuidMorphs('linkable');

            // Restore the composite unique on the new columns
            $table->unique(
                ['goal_id', 'linkable_type', 'linkable_id'],
                'goal_links_goal_id_linkable_type_linkable_id_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('goal_links', static function (Blueprint $table): void {
            // Drop unique before column changes
            $table->dropUnique('goal_links_goal_id_linkable_type_linkable_id_unique');

            // Switch back to bigint morphs
            $table->dropMorphs('linkable');
            $table->morphs('linkable');

            // Recreate the original composite unique
            $table->unique(
                ['goal_id', 'linkable_type', 'linkable_id'],
                'goal_links_goal_id_linkable_type_linkable_id_unique'
            );
        });
    }
};
