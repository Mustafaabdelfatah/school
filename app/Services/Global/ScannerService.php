<?php

namespace App\Services\Global;

use App\Jobs\AnalyzeScannerFileJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;

class ScannerService
{
    private const SUPPORTED_EXTENSIONS = [
        'pdf', 'doc', 'docx', 'txt', 'rtf',
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff',
        'xls', 'xlsx', 'csv'
    ];

    private const SCANNER_DIRECTORY = 'scanner';

    /**
     * Process uploaded file for scanner analysis.
     */
    public function processUploadedFile(string $filePath, string $originalFileName): array
    {
        $this->validateFile($originalFileName);

        $userId = $this->getUserId();
        $sanitizedFileName = $this->sanitizeFileName($originalFileName);
        $scannerFilePath = $this->moveToScannerDirectory($filePath, $sanitizedFileName, $userId);

        $this->queueForAnalysis($sanitizedFileName, $userId, $scannerFilePath);

        return $this->buildSuccessResponse($sanitizedFileName, $userId);
    }

    /**
     * Get supported file types.
     */
    public function getSupportedFileTypes(): array
    {
        return self::SUPPORTED_EXTENSIONS;
    }

    /**
     * Check if file type is supported.
     */
    public function isValidFileType(string $fileName): bool
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        return in_array($extension, self::SUPPORTED_EXTENSIONS);
    }

    /**
     * Validate the uploaded file.
     */
    private function validateFile(string $fileName): void
    {
        if (!$this->isValidFileType($fileName)) {
            throw new InvalidArgumentException(
                'Unsupported file type. Supported types: ' . implode(', ', self::SUPPORTED_EXTENSIONS)
            );
        }
    }

    /**
     * Get current user ID.
     */
    private function getUserId(): int
    {
        $userId = auth()->id();

        if (!$userId) {
            throw new InvalidArgumentException('User must be authenticated');
        }

        return $userId;
    }

    /**
     * Sanitize file name to prevent security issues.
     */
    private function sanitizeFileName(string $fileName): string
    {
        $pathInfo = pathinfo($fileName);
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        $basename = $pathInfo['filename'];

        // Remove dangerous characters and normalize
        $sanitizedBasename = preg_replace('/[^\p{L}\p{N}\s\-_]/u', ' ', $basename);
        $sanitizedBasename = preg_replace('/\s+/', ' ', trim($sanitizedBasename));

        return $sanitizedBasename . $extension;
    }

    /**
     * Move file from temporary to scanner directory.
     */
    private function moveToScannerDirectory(string $sourcePath, string $fileName, int $userId): string
    {
        $scannerPath = self::SCANNER_DIRECTORY . "/{$userId}";
        $destinationDir = storage_path("app/public/{$scannerPath}");

        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        $destinationPath = "{$destinationDir}/{$fileName}";
        $sourceFullPath = Storage::path($sourcePath);

        if (!File::exists($sourceFullPath)) {
            throw new InvalidArgumentException('Source file does not exist');
        }

        File::move($sourceFullPath, $destinationPath);

        return "/{$scannerPath}/{$fileName}";
    }

    /**
     * Queue file for AI analysis.
     */
    private function queueForAnalysis(string $fileName, int $userId, string $filePath): void
    {
        AnalyzeScannerFileJob::dispatch($fileName, $userId, $filePath);
    }

    /**
     * Build success response array.
     */
    private function buildSuccessResponse(string $fileName, int $userId): array
    {
        return [
            'uploaded' => true,
            'completed' => true,
            'filename' => $fileName,
            'file_url' => config('app.url') . "/storage/scanner/{$userId}/{$fileName}",
            'analysis_queued' => true,
        ];
    }
}
