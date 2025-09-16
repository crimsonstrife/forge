<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_statuses', static function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_done')->default(false);
            $table->timestamps();
        });

        Schema::create('ticket_priorities', static function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedTinyInteger('weight')->default(50);
            $table->timestamps();
        });

        Schema::create('ticket_types', static function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        DB::table('ticket_statuses')->insert([
            ['name' => 'New', 'is_done' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Open', 'is_done' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Waiting on Customer', 'is_done' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Waiting on Support',  'is_done' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Resolved', 'is_done' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Closed',   'is_done' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('ticket_priorities')->insert([
            ['name' => 'Low',    'weight' => 25, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medium', 'weight' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'High',   'weight' => 75, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Urgent', 'weight' => 90, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('ticket_types')->insert([
            ['name' => 'Bug', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Question', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Feature Request', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
        Schema::dropIfExists('ticket_priorities');
        Schema::dropIfExists('ticket_statuses');
    }
};
