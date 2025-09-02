<?php

namespace App\Http\Requests\Global\Other;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAllRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required'],
        ];
    }
}
