<?php

namespace App\Traits;

use App\Utilities\DynamicModelUtility;

use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\error;

trait DynamicUtilities
{
    /**
     * Get all models with a specified trait.
     * @param mixed $traitName The name of the trait to search for.
     * @return array<string> The names of all models with the specified trait.
     * @example DynamicModelUtils::getModelsByTrait('HasRoles');
     */
    public static function getModelsByTrait(string $traitName): array
    {
        return DynamicModelUtility::getModelsByTrait($traitName);
    }

    /**
     * Check if a class has a specified trait.
     * @param mixed $class The class to check.
     * @param mixed $traitName The name of the trait to search for.
     * @return bool True if the class has the specified trait, false otherwise.
     * @example DynamicModelUtils::classHasTrait('App\Models\User', 'HasRoles');
     */
    public static function HasTrait(string $class, string $traitName): bool
    {
        return DynamicModelUtility::HasTrait($class, $traitName);
    }

    /**
     * Get the namespace of a file.
     * @param mixed $file The file to get the namespace from.
     * @return string The namespace of the file, e.g. 'App\Models\...'
     * @example DynamicModelUtils::getNamespaceFromFile('app/Models/AuthObjects/User.php');
     */
    public static function getNamespaceFromFile(string $file): string
    {
        return DynamicModelUtility::getNamespaceFromFile($file);
    }

    /**
     * Get the Permission Formatted Name for a model.
     * @param mixed $model The model to get the permission formatted name for.
     * @return string The permission formatted name for the model. e.g. 'user' -> 'User', 'user_role' -> 'User Role', 'UserRole' -> 'User Role', etc.
     */
    public static function getNameForPermission(string $model): string
    {
        return DynamicModelUtility::getNameForPermission($model);
    }
}
