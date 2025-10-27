<?php

use App\Enums\AccessLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('record_shares', static function (Blueprint $t) {
            $t->uuid('id')->primary();

            $t->uuidMorphs('shareable');              // shareable_type, shareable_id (e.g., Project, Issue)
            $t->uuidMorphs('principal');              // principal_type, principal_id (User|Role|Team|Organization)

            $t->unsignedTinyInteger('access_level');  // AccessLevel enum (backed by int)
            $t->boolean('propagate_to_children')->default(false);
            $t->timestampTz('expires_at')->nullable();

            $t->foreignUuid('grantor_id')->nullable()->constrained('users')->nullOnDelete(); // who shared
            $t->timestamps();

            $t->unique([
                'shareable_type', 'shareable_id',
                'principal_type', 'principal_id',
            ], 'record_shares_unique');

            $t->index(['shareable_type', 'shareable_id', 'access_level']);
            $t->index(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_shares');
    }
};
