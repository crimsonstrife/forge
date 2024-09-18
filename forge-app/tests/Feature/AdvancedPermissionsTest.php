<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
//use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Auth\PermissionSet;
use App\Models\Auth\PermissionGroup;
use Tests\TestCase;

class AdvancedPermissionsTest extends TestCase
{
    use RefreshDatabase;  // Ensures a fresh DB for each test

    #[Test]
    public function setUp(): void
    {
        parent::setUp();

        // Reset permissions cache before every test
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Seed the database with test data
        if (!Permission::where('name', 'edit posts')->exists()) {
            Permission::create(['name' => 'edit posts']);
        }
        if (!Permission::where('name', 'delete posts')->exists()) {
            Permission::create(['name' => 'delete posts']);
        }
        if (!Permission::where('name', 'delete users')->exists()) {
            Permission::create(['name' => 'delete users']);
        }

        // Assert that the permissions were created
        $this->assertTrue(Permission::where('name', 'edit posts')->exists());
        $this->assertTrue(Permission::where('name', 'delete posts')->exists());
        $this->assertTrue(Permission::where('name', 'delete users')->exists());
    }

    #[Test]
    public function user_inherits_permissions_from_role()
    {
        // If the permission doesn't exist, create it
        if (!Permission::where('name', 'edit posts')->exists()) {
            Permission::create(['name' => 'edit posts']);
        }

        // Declare a role placeholder
        $role = null;

        // Check if the role exists, and create it if it doesn't
        if (!Role::where('name', 'admin')->exists()) {
            $role = Role::create(['name' => 'admin']);
        } else {
            $role = Role::where('name', 'admin')->first();
        }

        // Verify permission exists in the database
        $this->assertDatabaseHas('permissions', ['name' => 'edit posts']);

        // Get the permission
        $permission = Permission::where('name', 'edit posts')->first();

        // Give the role a permission
        $role->givePermissionTo('edit posts');

        // Verify role-permission relationship exists in the database
        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);

        // Save the role to the database
        $role->save();

        // Create a user and assign the role
        $user = User::factory()->create();
        $user->assignRole($role->name);

        // Save the user to the database
        $user->save();

        // Verify user-role relationship exists in the database
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => $user->id,
            'model_type' => 'App\Models\User',
        ]);

        // Eager load the user's roles and permissions
        $user->load('roles', 'permissions');

        // Log the user's roles and permissions
        info('User roles: ' . $user->roles);
        info('User permissions: ' . $user->permissions);
        info('The Roles permissions: ' . $user->roles->first()->permissions);

        // Assert that the user has the permission via their role
        $this->assertTrue($user->hasPermissionTo('edit posts'));
    }

    #[Test]
    public function user_inherits_permissions_from_permission_set()
    {
        // Create a PermissionSet and assign a permission to it
        $permissionSet = PermissionSet::create(['name' => 'Content Manager']);
        $permissionSet->permissions()->attach(Permission::where('name', 'delete posts')->first());

        // Create a user and assign the PermissionSet
        $user = User::factory()->create();
        $user->permissionSets()->attach($permissionSet);

        // Assert that the user has the permission via their PermissionSet
        $this->assertTrue($user->hasPermissionTo('delete posts'));
    }

    #[Test]
    public function muted_permissions_override_permissions()
    {
        // Create a PermissionSet with a muted permission
        $permissionSet = PermissionSet::create(['name' => 'Limited Admin']);
        $permissionSet->permissions()->attach(Permission::where('name', 'delete users')->first(), ['muted' => true]);

        // Create a user and assign the PermissionSet
        $user = User::factory()->create();
        $user->permissionSets()->attach($permissionSet);

        // Assert that the muted permission denies access
        $this->assertFalse($user->hasPermissionTo('delete users'));
    }

    #[Test]
    public function user_inherits_permissions_from_permission_group()
    {
        // Create a PermissionGroup and assign a permission to it
        $permissionGroup = PermissionGroup::create(['name' => 'Moderators']);
        $permissionGroup->permissions()->attach(Permission::where('name', 'edit posts')->first());

        // Create a user and assign the PermissionGroup
        $user = User::factory()->create();
        $user->permissionGroups()->attach($permissionGroup);

        // Assert that the user has the permission via their PermissionGroup
        $this->assertTrue($user->hasPermissionTo('edit posts'));
    }

    #[Test]
    public function muted_permission_in_permission_set_overrides_role_permission()
    {
        // Create a role and assign a permission to it
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo('delete users');

        // Create a PermissionSet with a muted permission
        $permissionSet = PermissionSet::create(['name' => 'Limited Admin']);
        $permissionSet->permissions()->attach(Permission::where('name', 'delete users')->first(), ['muted' => true]);

        // Create a user, assign the role and the PermissionSet
        $user = User::factory()->create();
        $user->assignRole($role);
        $user->permissionSets()->attach($permissionSet);

        // Assert that the muted permission denies access, even though the role grants it
        $this->assertFalse($user->hasPermissionTo('delete users'));
    }

    #[Test]
    public function user_with_conflicting_permissions_in_roles_and_sets()
    {
        $role1 = Role::create(['name' => 'Editor']);
        $role1->givePermissionTo('edit posts');

        $role2 = Role::create(['name' => 'Moderator']);
        $role2->givePermissionTo('delete posts');

        $permissionSet = PermissionSet::create(['name' => 'Limited Moderator']);
        $permissionSet->permissions()->attach(Permission::where('name', 'delete posts')->first(), ['muted' => true]);

        $user = User::factory()->create();
        $user->assignRole($role1);
        $user->assignRole($role2);
        $user->permissionSets()->attach($permissionSet);

        $this->assertTrue($user->hasPermissionTo('edit posts'));
        $this->assertFalse($user->hasPermissionTo('delete posts'));
    }

    #[Test]
    public function user_with_multiple_permission_sources_and_one_mutes_permission()
    {
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo('delete users');

        $permissionSet = PermissionSet::create(['name' => 'Limited Admin']);
        $permissionSet->permissions()->attach(Permission::where('name', 'delete users')->first(), ['muted' => true]);

        $permissionGroup = PermissionGroup::create(['name' => 'Moderators']);
        $permissionGroup->permissions()->attach(Permission::where('name', 'delete users')->first());

        $user = User::factory()->create();
        $user->assignRole($role);
        $user->permissionSets()->attach($permissionSet);
        $user->permissionGroups()->attach($permissionGroup);

        $this->assertFalse($user->hasPermissionTo('delete users'));
    }

    #[Test]
    public function permission_revoked_from_permission_set_removes_access()
    {
        $permissionSet = PermissionSet::create(['name' => 'Content Manager']);
        $deletePostPermission = Permission::where('name', 'delete posts')->first();
        $permissionSet->permissions()->attach($deletePostPermission);

        $user = User::factory()->create();
        $user->permissionSets()->attach($permissionSet);

        $this->assertTrue($user->hasPermissionTo('delete posts'));

        $permissionSet->permissions()->detach($deletePostPermission);

        $this->assertFalse($user->hasPermissionTo('delete posts'));
    }

    #[Test]
    public function user_without_any_roles_permission_sets_or_groups_has_no_permissions()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->hasPermissionTo('edit posts'));
        $this->assertFalse($user->hasPermissionTo('delete posts'));
        $this->assertFalse($user->hasPermissionTo('delete users'));
    }

    #[Test]
    public function user_with_multiple_roles_where_one_grants_and_one_denies_permission()
    {
        $role1 = Role::create(['name' => 'Editor']);
        $role1->givePermissionTo('edit posts');

        $permissionSet = PermissionSet::create(['name' => 'Restricted Editor']);
        $permissionSet->permissions()->attach(Permission::where('name', 'edit posts')->first(), ['muted' => true]);

        $user = User::factory()->create();
        $user->assignRole($role1);
        $user->permissionSets()->attach($permissionSet);

        $this->assertFalse($user->hasPermissionTo('edit posts'));
    }
}
