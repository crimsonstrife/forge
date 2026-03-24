<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Livewire\Livewire;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();
        Livewire::component('profile.update-profile-information-form', \App\Livewire\Profile\UpdateProfileInformationForm::class);
        Livewire::component('api.api-token-manager', \App\Livewire\ApiTokenManager::class);

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        // Defaults applied to new tokens (if you don’t check anything):
        Jetstream::defaultApiTokenPermissions([
            'issues:read',
            'projects:read',
        ]);

        Jetstream::permissions([
            'projects:read',
            'issues:read',
            'issues:write',
            'comments:write',
            'attachments:write',
            'time:write',
        ]);

        Jetstream::role('admin', 'Administrator', [
            'create', 'read', 'update', 'delete',
        ])->description('Administrator users can perform any action.');

        Jetstream::role('editor', 'Editor', [
            'read', 'create', 'update',
        ])->description('Editor users have the ability to read, create, and update.');

        Jetstream::role('collaborator', 'Collaborator', [
            'read', 'create', 'update',
        ])->description('Collaborators can work with shared records without managing the team itself.');
    }
}
