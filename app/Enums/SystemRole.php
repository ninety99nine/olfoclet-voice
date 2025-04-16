<?php

namespace App\Enums;

enum SystemRole: string
{
    case REGULAR = 'regular';
    case SUPER_ADMIN = 'super admin';
}
