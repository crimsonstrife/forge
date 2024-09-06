<?php

namespace App\Utilities;

trait CommonUtilities
{
    /**
     * Check if a model matches itself
     * @param self $model
     * @return bool
     */
    public function matches(self $model): bool
    {
        return $this->id() === $model->id();
    }

    /**
     * Check if a model matches a given ID
     * @param int $id
     * @return bool
     */
    public function matchesId(int $id): bool
    {
        return $this->id() === $id;
    }

    /**
     * Get the ID of the model
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }
}
