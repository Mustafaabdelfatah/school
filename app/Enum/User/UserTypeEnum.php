<?php

namespace App\Enum\User;

use App\Trait\Global\EnumMethods;

enum UserTypeEnum: string
{
    use EnumMethods;

    case ADMIN = 'admin';
    case USER = 'user';
    case TEACHER = 'teacher';
    case STUDENT = 'student';


}
