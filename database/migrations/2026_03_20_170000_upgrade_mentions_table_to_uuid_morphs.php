<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('mentions') || ! Schema::hasColumn('mentions', 'model_id')) {
            return;
        }

        $modelIdType = Schema::getColumnType('mentions', 'model_id');

        if (in_array($modelIdType, ['char', 'varchar', 'string', 'uuid'], true)) {
            return;
        }

        Schema::create('mentions_uuid_upgrade', static function (Blueprint $table): void {
            $table->increments('id');
            $table->uuidMorphs('model');
            $table->uuidMorphs('recipient');
            $table->timestamps();
        });

        DB::table('mentions')
            ->orderBy('id')
            ->chunkById(200, static function ($rows): void {
                $payload = collect($rows)->map(static function ($row): array {
                    return [
                        'id' => (int) $row->id,
                        'model_type' => (string) $row->model_type,
                        'model_id' => (string) $row->model_id,
                        'recipient_type' => (string) $row->recipient_type,
                        'recipient_id' => (string) $row->recipient_id,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ];
                })->all();

                if ($payload !== []) {
                    DB::table('mentions_uuid_upgrade')->insert($payload);
                }
            });

        Schema::drop('mentions');
        Schema::rename('mentions_uuid_upgrade', 'mentions');
    }

    public function down(): void
    {
        if (! Schema::hasTable('mentions') || ! Schema::hasColumn('mentions', 'model_id')) {
            return;
        }

        $modelIdType = Schema::getColumnType('mentions', 'model_id');

        if (! in_array($modelIdType, ['char', 'varchar', 'string', 'uuid'], true)) {
            return;
        }

        Schema::create('mentions_integer_downgrade', static function (Blueprint $table): void {
            $table->increments('id');
            $table->morphs('model');
            $table->morphs('recipient');
            $table->timestamps();
        });

        Schema::drop('mentions');
        Schema::rename('mentions_integer_downgrade', 'mentions');
    }
};
