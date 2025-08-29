<?php

namespace App\Enums;

enum SprintState: string {
    case Planned = 'planned';
    case Active   = 'active';
    case Closed     = 'closed';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Active   => 'primary',
            self::Closed     => 'success',
            self::Planned => 'gray',
        };
    }
}
