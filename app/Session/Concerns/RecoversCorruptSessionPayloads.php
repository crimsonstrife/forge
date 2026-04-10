<?php

namespace App\Session\Concerns;

use Throwable;

trait RecoversCorruptSessionPayloads
{
    /**
     * Read the session payload, dropping corrupted or incompatible payloads.
     */
    protected function readFromHandler()
    {
        try {
            return parent::readFromHandler();
        } catch (Throwable $exception) {
            report($exception);

            try {
                $this->handler->destroy($this->getId());
            } catch (Throwable $destroyException) {
                report($destroyException);
            }

            // Force a fresh session identifier so the broken payload is not reused.
            $this->setId(null);

            return [];
        }
    }
}
