<?php

namespace App\Enums;

enum FilterResourceType: string
{
    case CALLS = 'calls';
    case ROLES = 'roles';
    case USERS = 'users';
    case QUEUES = 'queues';
    case CONTACTS = 'contacts';
    case DEPARTMENTS = 'departments';
    case ORGANIZATIONS = 'organizations';
}
