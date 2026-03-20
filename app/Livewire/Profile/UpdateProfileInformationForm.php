<?php

namespace App\Livewire\Profile;

use App\Livewire\Concerns\InteractsWithIssueNotificationPreferences;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm as JetstreamUpdateProfileInformationForm;

class UpdateProfileInformationForm extends JetstreamUpdateProfileInformationForm
{
    use InteractsWithIssueNotificationPreferences;

    public function mount(): void
    {
        parent::mount();

        $this->loadIssueNotificationPreferences(Auth::user());
    }

    public function updateProfileInformation(UpdatesUserProfileInformation $updater)
    {
        $response = parent::updateProfileInformation($updater);

        $this->saveIssueNotificationPreferences(Auth::user());

        return $response;
    }
}
