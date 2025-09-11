<?php

namespace App\Exceptions;

use Exception;
use RuntimeException;

class CannotDeleteWithOpenChildren extends RuntimeException
{
    public static function forProject(string $projectKeyOrName): self
    {
        return new self("Cannot delete project '{$projectKeyOrName}' because it has open issues.");
    }

    public static function forIssue(string $issueKeyOrSummary): self
    {
        return new self("Cannot delete issue '{$issueKeyOrSummary}' because it has open child issues.");
    }
}
