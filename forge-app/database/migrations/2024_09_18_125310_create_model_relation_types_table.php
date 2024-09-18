<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the model_relation_types table.
 * This table will store the types of relations between models.
 * i.e. "relates to", "duplicates", "is duplicated by", "blocks", "is blocked by", "clones", "is cloned by", "is subtask of", "relies on", etc.
 * This can be referenced by other relation tables to define many-to-many relationships between models, such as how an issue can be related to multiple other issues.
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('model_relation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // i.e. "relates to", "duplicates", "blocks", "clones", "is subtask of", etc.
            $table->string('description')->nullable();
            $table->string('inverse_name')->nullable()->unique(); // should be the name of the inverse relation type, i.e. "is duplicated by" for "duplicates", can be null if is_symmetric is true
            $table->boolean('is_symmetric')->default(false); // if true, then the inverse relation is the same as the relation, i.e. "relates to" is symmetric, but "duplicates" is not
            $table->boolean('is_transitive')->default(false); // if true, then the relation is transitive, i.e. if A relates to B and B relates to C, then A relates to C
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_relation_types');
    }
};
