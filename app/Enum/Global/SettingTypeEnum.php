<?php

namespace App\Enum\Global;

use App\Trait\Global\EnumMethods;

enum SettingTypeEnum: string
{
    use EnumMethods;

    case Text = 'text';
    case ImageUploader = 'imageUploader';
    case File = 'file';
}
