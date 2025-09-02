<?php

namespace App\Http\Requests\Global\Help;

use Illuminate\Foundation\Http\FormRequest;

class HelpEnumRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'enums' => ['sometimes', 'required', 'array'],
            'enums.*.name' => ['sometimes', 'required', 'string'],
            'enums.*.module' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
