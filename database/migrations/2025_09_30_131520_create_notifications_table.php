<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', static function (Blueprint $table): void {
                $table->id();
                $table->string('type');
                $table->uuidMorphs('notifiable'); // notifiable_type, notifiable_id
                $table->text('data'); // json in text keeps sqlite compat; cast in model
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
