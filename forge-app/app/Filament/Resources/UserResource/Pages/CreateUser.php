<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateUser
 *
 * This class extends the CreateRecord class and is responsible for handling the creation of user records
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\UserResource\Pages
 */
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
