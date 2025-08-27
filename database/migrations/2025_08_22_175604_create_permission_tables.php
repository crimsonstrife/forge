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
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        throw_if(empty($tableNames), new Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.'));
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), new Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.'));

        Schema::create($tableNames['permissions'], static function (Blueprint $table) {
            // $table->engine('InnoDB');
            $table->uuid('id')->primary(); // permission id
            $table->string('name');       // For MyISAM use string('name', 225); // (or 166 for InnoDB with Redundant/Compact row format)
            $table->string('guard_name'); // For MyISAM use string('guard_name', 25);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
            // $table->engine('InnoDB');
            $table->uuid('id')->primary(); // role id
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->foreignUuid($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');       // For MyISAM use string('name', 225); // (or 166 for InnoDB with Redundant/Compact row format)
            $table->string('guard_name'); // For MyISAM use string('guard_name', 25);
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->uuid($pivotPermission);

            $table->string('model_type');
            $table->uuid($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            if ($teams) {
                $table->foreignUuid($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }

        });

        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->uuid($pivotRole);

            $table->string('model_type');
            $table->foreignUuid($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            if ($teams) {
                $table->foreignUuid($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->index([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->uuid($pivotPermission);
            $table->uuid($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        Schema::create('permission_sets', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->mediumText('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // Groups of sets
        Schema::create('permission_set_groups', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->mediumText('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // Pivot: groups <-> sets
        Schema::create('group_permission_sets', static function (Blueprint $table) {
            $table->uuid('permission_set_group_id');
            $table->uuid('permission_set_id');
            $table->primary(['permission_set_group_id', 'permission_set_id'], 'group_permission_sets_primary');

            $table->foreign('permission_set_group_id')
                ->references('id')->on('permission_set_groups')->cascadeOnDelete();

            $table->foreign('permission_set_id')
                ->references('id')->on('permission_sets')->cascadeOnDelete();
        });

        // Pivot: sets <-> permissions (grants)
        Schema::create('permission_set_permissions', static function (Blueprint $table) {
            $table->uuid('permission_set_id');
            $table->uuid('permission_id');
            $table->primary(['permission_set_id', 'permission_id'], 'permission_set_permissions_primary');

            $table->foreign('permission_set_id')
                ->references('id')->on('permission_sets')->cascadeOnDelete();

            $table->foreign('permission_id')
                ->references('id')->on(config('permission.table_names.permissions'))->cascadeOnDelete();
        });

        // Pivot: sets <-> permissions (mutes/denies)
        Schema::create('permission_set_mutes', static function (Blueprint $table) {
            $table->uuid('permission_set_id');
            $table->uuid('permission_id');
            $table->primary(['permission_set_id', 'permission_id'], 'permission_set_mutes_primary');

            $table->foreign('permission_set_id')
                ->references('id')->on('permission_sets')->cascadeOnDelete();

            $table->foreign('permission_id')
                ->references('id')->on(config('permission.table_names.permissions'))->cascadeOnDelete();
        });

        // Pivot: users <-> sets
        Schema::create('user_permission_sets', static function (Blueprint $table) {
            // If your users use ULIDs: switch to foreignUlid('user_id')
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();

            $table->uuid('permission_set_id');
            $table->primary(['user_id', 'permission_set_id'], 'user_permission_sets_primary');

            $table->foreign('permission_set_id')
                ->references('id')->on('permission_sets')->cascadeOnDelete();
        });

        // Pivot: roles <-> sets (attach sets to roles too)
        Schema::create('role_permission_sets', static function (Blueprint $table) {
            $table->uuid(config('permission.column_names.role_pivot_key', 'role_id'));
            $table->uuid('permission_set_id');

            $table->primary([
                config('permission.column_names.role_pivot_key', 'role_id'),
                'permission_set_id'
            ], 'role_permission_sets_primary');

            $table->foreign(config('permission.column_names.role_pivot_key', 'role_id'))
                ->references('id')->on(config('permission.table_names.roles'))->cascadeOnDelete();

            $table->foreign('permission_set_id')
                ->references('id')->on('permission_sets')->cascadeOnDelete();
        });

        app('cache')
            ->store(config('permission.cache.store') !== 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \RuntimeException('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::dropIfExists('role_permission_sets');
        Schema::dropIfExists('user_permission_sets');
        Schema::dropIfExists('permission_set_mutes');
        Schema::dropIfExists('permission_set_permissions');
        Schema::dropIfExists('group_permission_sets');
        Schema::dropIfExists('permission_set_groups');
        Schema::dropIfExists('permission_sets');
        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
