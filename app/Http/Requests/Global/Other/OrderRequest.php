<?php

namespace App\Http\Requests\Global\Other;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'order' => ['required', 'int'],
        ];
    }
}
