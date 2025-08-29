<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\PermissionRegistrar;

use function Laravel\Prompts\{confirm, text, password, select, multiselect, note, info, warning, table};

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Safety check in production
        if (!confirm(label: 'You are in production. Continue creating a user?', default: false) && app()->environment('production')) {
                warning('Aborted.');
                return;
            }

        // Offer interactive setup
        $interactive = confirm(
            label: 'Interactive setup? (No = auto-generate admin user)',
            default: true
        );

        if ($interactive) {
            $this->runInteractive();
        } else {
            $this->runAuto();
        }
    }

    protected function runInteractive(): void
    {
        $Role = app(config('permission.models.role'));

        $name = text(
            label: 'Name',
            default: env('ADMIN_NAME', 'Admin User'),
            required: true
        );

        // Email with validation & uniqueness
        while (true) {
            $email = text(
                label: 'Email',
                default: env('ADMIN_EMAIL', 'admin@example.com'),
                required: true
            );

            $v = Validator::make(['email' => $email], ['email' => 'required|email']);
            if ($v->fails()) {
                warning($v->errors()->first('email'));
                continue;
            }
            break;
        }

        // Choose password or auto-generate
        $gen = confirm('Generate a random secure password?', default: true);
        $plainPassword = $gen
            ? Str::password(length: 20)
            : (static function () {
                while (true) {
                    $pass = password(label: 'Password', required: true);
                    $v = Validator::make(['p' => $pass], ['p' => 'min:8']);
                    if ($v->fails()) {
                        warning('Password must be at least 8 characters.');
                        continue;
                    }
                    return $pass;
                }
            })();

        // Create/update user
        $user = User::query()->firstOrNew(['email' => $email]);
        $isNew = ! $user->exists;

        $user->name = $name;
        $user->password = Hash::make($plainPassword);
        $user->email_verified_at = now();
        $user->save();

        // Create a personal / default team?
        $makeTeam = class_exists(Team::class) && confirm('Create a personal team for this user?', default: true);
        $team = null;
        if ($makeTeam) {
            $defaultTeamName = str($name)->before(' ')->toString() . "'s Team";
            $teamName = text(
                label: 'Team name',
                default: env('ADMIN_TEAM', $defaultTeamName),
                required: true
            );

            $team = $this->getTeam($teamName, $user);
        }

        // Assign roles (team-scoped context if team exists)
        $roleNames = $Role::query()->orderBy('name')->pluck('name')->all();
        $selected = [];

        if (empty($roleNames)) {
            warning('No roles found. Did you run RoleSeeder?');
        } else {
            $selected = multiselect(
                label: 'Select roles to grant',
                options: $roleNames,
                scroll: 10,
                required: false,
            );
        }

        if (! empty($selected)) {
            $registrar = app(PermissionRegistrar::class);
            $origTeamId = $registrar->getPermissionsTeamId();

            // If you want team-scoped roles when a team exists:
            if ($team) {
                $registrar->setPermissionsTeamId($team->id);
            } else {
                $registrar->setPermissionsTeamId(null); // global
            }

            $user->syncRoles($selected);

            // restore context
            $registrar->setPermissionsTeamId($origTeamId);
        }

        $this->printSummary($user, $plainPassword, $team, $selected, $isNew);
    }

    protected function runAuto(): void
    {
        $Role = app(config('permission.models.role'));

        $name  = env('ADMIN_NAME', 'Admin User');
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $plainPassword = env('ADMIN_PASSWORD') ?: Str::password(20);

        $user = User::query()->firstOrNew(['email' => $email]);
        $isNew = ! $user->exists;

        $user->name = $name;
        $user->password = Hash::make($plainPassword);
        $user->email_verified_at = now();
        $user->save();

        $team = null;
        if (class_exists(Team::class)) {
            $teamName = env('ADMIN_TEAM', "Default Team");
            $team = $this->getTeam($teamName, $user);
        }

        // Give SuperAdmin if present
        if ($Role::query()->where('name', 'SuperAdmin')->exists()) {
            $registrar = app(PermissionRegistrar::class);
            $origTeamId = $registrar->getPermissionsTeamId();

            // Assign globally by default; switch to team scope if you prefer:
            $registrar->setPermissionsTeamId($team?->id);
            $user->syncRoles(['SuperAdmin']);
            $registrar->setPermissionsTeamId($origTeamId);
        } else {
            warning('SuperAdmin role not found. Run RoleSeeder first.');
        }

        $this->printSummary($user, $plainPassword, $team, ['SuperAdmin'], $isNew);
    }

    protected function printSummary(User $user, string $plainPassword, ?Team $team, array $roles, bool $isNew): void
    {
        note($isNew ? 'User created:' : 'User updated:');

        $appUrl = rtrim(config('app.url') ?: '', '/');
        $login  = $appUrl ? $appUrl . '/login' : '/login';

        table(
            headers: ['Field', 'Value'],
            rows: [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Password', $plainPassword],
                ['Team', $team?->name ?? '(none)'],
                ['Roles', empty($roles) ? '(none)' : implode(', ', $roles)],
                ['Login URL', $login],
            ]
        );

        info('Keep the password safe. You can change it after logging in.');
    }

    /**
     * @param mixed $teamName
     * @param Model|User $user
     * @return Team|Model
     */
    protected function getTeam(mixed $teamName, Model|User $user): Team|Model
    {
        $team = Team::query()->firstOrCreate([
            'name' => $teamName,
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        if (method_exists($user, 'ownedTeams') && !$user->ownedTeams()->whereKey($team->id)->exists()) {
            $user->ownedTeams()->save($team);
        }
        if (method_exists($user, 'switchTeam')) {
            $user->switchTeam($team);
        } else {
            $user->forceFill(['current_team_id' => $team->id])->save();
        }
        return $team;
    }
}
