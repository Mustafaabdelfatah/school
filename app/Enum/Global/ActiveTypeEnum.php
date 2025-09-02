<?php

namespace App\Enum\Global;

use App\Trait\Global\EnumMethods;

enum ActiveTypeEnum: int
{
    use EnumMethods;

    case Active = 1;
    case InActive = 0;
}
