<?php

namespace App\Enums;

enum Status: string
{
    case InActive = 'inactive';

    case Active = 'active';

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
