<?php

namespace App\Http\Requests\Global\Chunk;

use Illuminate\Foundation\Http\FormRequest;

class ChunkFileRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'file_name' => 'required|string',
            'chunk_number' => 'required|min:1',
            'chunk_file' => 'required|file',
        ];
    }
}
