<?php

namespace App\Observers;

use App\Models\PermissionSet;
use App\Models\Role;
use App\Services\Auth\RolePermissionSync;
use Illuminate\Support\Facades\DB;

final class PermissionSetObserver
{
    /**
     * Fired when attach() is called on any belongsToMany of PermissionSet.
     * @param  array<int, string>  $pivotIds
     * @param  array<string, mixed> $attributes
     */
    public function pivotAttached(PermissionSet $set, string $relationName, array $pivotIds, array $attributes): void
    {
        $this->handlePivotChange($set, $relationName, $pivotIds);
    }

    /**
     * Fired when detach() is called.
     */
    public function pivotDetached(PermissionSet $set, string $relationName, array $pivotIds, array $attributes): void
    {
        $this->handlePivotChange($set, $relationName, $pivotIds);
    }

    /**
     * Fired when updateExistingPivot() / sync() updates attributes.
     */
    public function pivotUpdated(PermissionSet $set, string $relationName, array $pivotIds, array $attributes): void
    {
        $this->handlePivotChange($set, $relationName, $pivotIds);
    }

    /**
     * Re-sync roles after any pivot change.
     *
     * - If permissions/mutedPermissions changed, re-sync ALL roles that use this set.
     * - If roles changed, re-sync ONLY the roles whose ids are in $pivotIds.
     *
     * @param  array<int, string>  $pivotIds
     */
    private function handlePivotChange(PermissionSet $set, string $relationName, array $pivotIds): void
    {
        DB::afterCommit(static function () use ($set, $relationName, $pivotIds) {
            /** @var RolePermissionSync $sync */
            $sync = app(RolePermissionSync::class);

            if (in_array($relationName, ['permissions', 'mutedPermissions'], true)) {
                // The set's content changed → all roles using this set must be re-synced.
                $roleIds = $set->roles()->pluck('roles.id')->unique()->all();

                foreach ($roleIds as $roleId) {
                    if ($role = Role::find($roleId)) {
                        $sync->syncRole($role);
                    }
                }

                return;
            }

            if ($relationName === 'roles') {
                // Membership changed → only those roles need re-sync.
                foreach ($pivotIds as $roleId) {
                    if ($role = Role::find($roleId)) {
                        $sync->syncRole($role);
                    }
                }
            }
        });
    }
}
