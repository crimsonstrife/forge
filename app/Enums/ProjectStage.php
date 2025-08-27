<?php

namespace App\Enums;

enum ProjectStage: string
{
    case Planning = 'planning';
    case Active   = 'active';
    case OnHold   = 'on_hold';
    case Done     = 'done';

    public function label(): string
    {
        return match ($this) {
            self::OnHold => 'On hold',
            default      => ucfirst($this->value),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active   => 'primary',
            self::OnHold   => 'warning',
            self::Done     => 'success',
            self::Planning => 'gray',
        };
    }
}
