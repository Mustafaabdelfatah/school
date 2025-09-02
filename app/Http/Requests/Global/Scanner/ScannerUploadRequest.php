<?php

namespace App\Http\Requests\Global\Scanner;

use Illuminate\Foundation\Http\FormRequest;

class ScannerUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'chunk_file' => [
                'required',
                'file',
                'max:10240', // 10MB max per chunk
            ],
            'file_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>:"/\\|?*]+$/', // Prevent dangerous characters
            ],
            'chunk_number' => [
                'required',
                'integer',
                'min:1',
            ],
            'is_final' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'chunk_file.required' => 'File chunk is required',
            'chunk_file.file' => 'Invalid file format',
            'chunk_file.max' => 'File chunk size cannot exceed 10MB',
            'file_name.required' => 'File name is required',
            'file_name.regex' => 'File name contains invalid characters',
            'chunk_number.required' => 'Chunk number is required',
            'chunk_number.min' => 'Chunk number must be at least 1',
        ];
    }

    /**
     * Get the validated data from the request.
     */
    public function getValidatedData(): array
    {
        return $this->validated();
    }

    /**
     * Check if this is the final chunk.
     */
    public function isFinalChunk(): bool
    {
        return (bool) $this->input('is_final', false);
    }
}
