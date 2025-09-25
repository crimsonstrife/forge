<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('import_export_records', static function (Blueprint $t): void {
            if (! Schema::hasColumn('import_export_records', 'initiator_id')) {
                $t->foreignUuid('initiator_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('import_export_records', static function (Blueprint $t): void {
            if (Schema::hasColumn('import_export_records', 'initiator_id')) {
                $t->dropConstrainedForeignId('initiator_id');
            }
        });
    }
};
