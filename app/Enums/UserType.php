<?php

namespace App\Enums;

enum UserType: string
{
    case REGULAR = 'regular';
    case SUPER_ADMIN = 'super_admin';
}
