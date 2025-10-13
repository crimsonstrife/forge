<?php

namespace App\Enums;

enum AccessLevel: int
{
    case View = 10;
    case Comment = 20;
    case Edit = 30;
    case Delete = 40;
    case Manage = 50;

    public function allows(string $ability): bool
    {
        return match ($ability) {
            'view' => $this->value >= self::View->value,
            'comment' => $this->value >= self::Comment->value,
            'update' => $this->value >= self::Edit->value,
            'delete' => $this->value >= self::Delete->value,
            'manage', 'share' => $this->value >= self::Manage->value,
            default => false,
        };
    }

    public static function max(?self $a, ?self $b): ?self
    {
        if (! $a) {
            return $b;
        }
        if (! $b) {
            return $a;
        }
        return $a->value >= $b->value ? $a : $b;
    }
}
