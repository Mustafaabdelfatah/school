<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Standardized API Response Methods
|--------------------------------------------------------------------------
*/

if (!function_exists('apiResponse')) {
    /**
     * Standard API response format
     */
    function apiResponse(bool $success = true, string $message = '', $data = null, array $errors = null, int $code = 200, array $meta = null): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        if ($meta !== null) {
            $response['meta'] = $meta;
        }

        $response['timestamp'] = now()->toISOString();

        return response()->json($response, $code);
    }
}

if (!function_exists('successResponse')) {
    /**
     * Success response with data
     */
    function successResponse($data = null, string $message = 'Operation completed successfully', int $code = 200, array $meta = null): JsonResponse
    {
        return apiResponse(true, $message, $data, null, $code, $meta);
    }
}

if (!function_exists('errorResponse')) {
    /**
     * Error response with errors
     */
    function errorResponse(string $message = 'Operation failed', $errors = null, int $code = 400): JsonResponse
    {
        return apiResponse(false, $message, null, $errors, $code);
    }
}

if (!function_exists('validationErrorResponse')) {
    /**
     * Validation error response
     */
    function validationErrorResponse($errors, string $message = 'Validation failed'): JsonResponse
    {
        return apiResponse(false, $message, null, $errors, 422);
    }
}

if (!function_exists('notFoundResponse')) {
    /**
     * Not found response
     */
    function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return apiResponse(false, $message, null, null, 404);
    }
}

if (!function_exists('unauthorizedResponse')) {
    /**
     * Unauthorized response
     */
    function unauthorizedResponse(string $message = 'Unauthorized access'): JsonResponse
    {
        return apiResponse(false, $message, null, null, 401);
    }
}

if (!function_exists('forbiddenResponse')) {
    /**
     * Forbidden response
     */
    function forbiddenResponse(string $message = 'Access forbidden'): JsonResponse
    {
        return apiResponse(false, $message, null, null, 403);
    }
}

if (!function_exists('createdResponse')) {
    /**
     * Created response for successful resource creation
     */
    function createdResponse($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return apiResponse(true, $message, $data, null, 201);
    }
}

if (!function_exists('deletedResponse')) {
    /**
     * Response for successful resource deletion
     */
    function deletedResponse(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return apiResponse(true, $message, null, null, 200);
    }
}

if (!function_exists('updatedResponse')) {
    /**
     * Response for successful resource update
     */
    function updatedResponse($data = null, string $message = 'Resource updated successfully'): JsonResponse
    {
        return apiResponse(true, $message, $data, null, 200);
    }
}

if (!function_exists('paginatedResponse')) {
    /**
     * Paginated response with meta data
     */
    function paginatedResponse($paginatedData, string $message = 'Data retrieved successfully'): JsonResponse
    {
        $meta = [
            'current_page' => $paginatedData->currentPage(),
            'total' => $paginatedData->total(),
            'per_page' => $paginatedData->perPage(),
            'last_page' => $paginatedData->lastPage(),
            'from' => $paginatedData->firstItem(),
            'to' => $paginatedData->lastItem(),
            'has_more_pages' => $paginatedData->hasMorePages(),
        ];

        return apiResponse(true, $message, $paginatedData->items(), null, 200, $meta);
    }
}

if (!function_exists('serverErrorResponse')) {
    /**
     * Server error response
     */
    function serverErrorResponse(string $message = 'Internal server error'): JsonResponse
    {
        return apiResponse(false, $message, null, null, 500);
    }
}

if (!function_exists('customErrorResponse')) {
    /**
     * Custom error response with specific code
     */
    function customErrorResponse(string $message, int $code, $errors = null): JsonResponse
    {
        return apiResponse(false, $message, null, $errors, $code);
    }
}

/*
|--------------------------------------------------------------------------
| Responses Methods
|--------------------------------------------------------------------------
*/
if (!function_exists('successResponse')) {
    function successResponse($data = [], $msg = 'success', $code = 200): JsonResponse
    {
        return response()->json(['status' => true, 'code' => $code, 'message' => $msg, 'data' => $data], $code);
    }
}

if (!function_exists('failResponse')) {
    function failResponse($data = [], $msg = 'fail', $code = 400): JsonResponse
    {
        return errorResponse($msg, $data, $code);
    }
}

if (!function_exists('abort403')) {
    function abort403($condition = true): void
    {
        if ($condition) {
            abort(403, trans('api.no_required_permissions'));
        }
    }
}

if (!function_exists('unKnownError')) {
    function unKnownError($message = null): JsonResponse|RedirectResponse
    {
        $message = trans('dashboard.something_error') . '' . (config('debug') ? " : $message" : '');

        return request()?->expectsJson()
            ? response()->json(['message' => $message], 400)
            : redirect()->back()->with(['status' => 'error', 'message' => $message]);
    }
}

/*
|--------------------------------------------------------------------------
| App Check Methods (IS)
|--------------------------------------------------------------------------
*/
if (!function_exists('isArrayIndex')) {
    function isArrayIndex($value): bool
    {
        return is_array($value) && count(array_filter(array_keys($value), 'is_string')) === 0;
    }
}

if (!function_exists('iSnake')) {
    function iSnake($value): bool
    {
        // Define the pattern for snake_case
        $pattern = '/^[a-z-A-Z]+(_[a-z]+)*$/';

        // Check if the value matches the pattern
        if (preg_match($pattern, $value)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('isBase64')) {
    function isBase64($data): bool
    {
        $decoded_data = base64_decode($data, true);
        $encoded_data = base64_encode($decoded_data);

        if ($encoded_data !== $data) {
            return false;
        }

        if (!ctype_print($decoded_data)) {
            return false;
        }

        return true;
    }
}

/*
|--------------------------------------------------------------------------
| Resolves Methods
|--------------------------------------------------------------------------
*/
if (!function_exists('resolveTrans')) {
    function resolveTrans($trans = '', $page = 'api', $lang = null, $snaked = true): ?string
    {
        if (empty($trans)) {
            return '---';
        }

        app()->setLocale($lang ?? app()->getLocale());

        $key = $snaked ? Str::snake($trans) : $trans;

        return Str::startsWith(__("$page.$key"), "$page.") ? $trans : __("$page.$key");
    }
}

if (!function_exists('resolveBool')) {
    function resolveBool($item): string
    {
        if ($item === 0) {
            return __('api.no');
        }

        if ($item === 1) {
            return __('api.yes');
        }

        return $item;
    }
}

if (!function_exists('resolvePhoto')) {
    function resolvePhoto($image = null, $type = 'user')
    {
        $result = ($type === 'user'
            ? asset('media/avatar.png')
            : asset('media/blank.png'));

        if (is_null($image)) {
            return $result;
        }

        if (Str::startsWith($image, 'http')) {
            return $image;
        }

        return Storage::exists($image)
            ? Storage::url($image)
            : $result;
    }
}

if (!function_exists('resolveArray')) {
    function resolveArray(string|array $array): array
    {
        return is_array($array) ? $array : explode(',', $array);
    }
}

if (!function_exists('resolveModel')) {
    function resolveModel(string $name, $module = null): ?object
    {
        $modelPath = !empty($module) && $module !== 'none'
            ? "Modules\\" . ucfirst(Str::camel($module)) . "\\App\\Models"
            : "App\\Models";


        $modelClass = $modelPath . "\\" . Str::studly(Str::singular($name));

        return class_exists($modelClass) ? app($modelClass) : null;
    }
}

if (!function_exists('resolveClass')) {
    function resolveClass(string $path): ?object
    {
        return class_exists($path) ? app($path) : null;
    }
}

/*
|--------------------------------------------------------------------------
| App Global Methods
|--------------------------------------------------------------------------
*/
if (!function_exists('dateFormat')) {
    function dateFormat($date, $format = 'j F Y'): string
    {
        return !is_numeric($date)
            ? Jenssegers\Date\Date::parse("2024-01-01")
            : '----';
    }
}

if (!function_exists('timeFormat')) {
    function timeFormat($time): ?string
    {
        if ($time === null) {
            return null;
        }

        return Jenssegers\Date\Date::parse($time)->format('h:i a');
    }
}

if (!function_exists('getModelKey')) {
    function getModelKey(?string $className = null): ?string
    {
        if (!$className) {
            return null;
        }

        $shortName = class_basename($className);

        return strtolower(Str::snake($shortName));
    }
}

if (!function_exists('detectModelPath')) {
    function detectModelPath($type): string
    {
        return "App\\Models\\" . Str::ucfirst(Str::camel(Str::singular($type)));
    }
}

if (!function_exists('fetchData')) {
    function fetchData(Builder $query, string|int|null $pageSize = null, $resource = null, $meta = [])
    {
        if ($pageSize && (int)$pageSize !== -1) {
            $data = $query->paginate($pageSize);

            if ($resource) {
                $data->data = $resource::collection($data);
            }
        } else {
            $data = $resource ? $resource::collection($query->get()) : $query->get();
        }

        if (count($meta)) {
            $data = [
                'data' => $data,
                ...$meta,
            ];
        }

        return $data;
    }
}

if (!function_exists('vImage')) {
    function vImage($ext = null): string
    {
        return ($ext === null) ? 'mimes:jpg,png,jpeg,png,gif,bmp' : 'mimes:' . $ext;
    }
}

if (!function_exists('updateDotEnv')) {
    function updateDotEnv(array $data = []): void
    {
        $path = base_path('.env');

        foreach ($data as $dataKey => $dataValue) {
            if (is_bool($dataValue)) {
                $dataValue = $dataValue ? 'true' : 'false';
            }

            if (str_contains(file_get_contents($path), "\n" . $dataKey . '=')) {
                $contents = array_values(array_filter(explode("\n", file_get_contents($path))));
                foreach ($contents as $content) {
                    if (str_starts_with($content, $dataKey . '=')) {
                        $delim = '';

                        if (str_contains($content, '"') || str_contains($dataValue, ' ') || str_contains($dataValue, '#')) {
                            $delim = '"';
                        }
                        file_put_contents(
                            $path,
                            str_replace(
                                $content,
                                $dataKey . '=' . $delim . $dataValue . $delim,
                                file_get_contents($path)
                            )
                        );
                    }
                }
            } else if (str_contains($dataValue, ' ') || str_contains($dataValue, '#')) {
                File::append($path, $dataKey . '="' . $dataValue . '"' . "\n");
            } else {
                File::append($path, $dataKey . '=' . $dataValue . "\n");
            }
        }
    }
}

if (!function_exists('logError')) {
    function logError($exception): void
    {
        info("Error In Line => " . $exception->getLine() . " in File => {$exception->getFile()} , ErrorDetails => " . $exception->getMessage());
    }
}

if (!function_exists('when')) {
    /**
     * Executes the given closure if the condition is true.
     * The condition is considered true if:
     * - It is a boolean and true
     * - It is a collection and not empty
     * - It is an array and not empty
     * - It is a string and not empty
     *
     * @param mixed $condition
     * @param callable $closure The closure to execute if the condition is pass from check.
     */
    function when(mixed $condition, callable $closure): void
    {
        //Determine if the condition is true based on its type using match
        $isTrue = match (true) {
            is_bool($condition) => $condition,
            $condition instanceof Collection => !$condition->isEmpty(),
            is_array($condition) => !empty($condition),
            is_string($condition) => $condition !== '',
            default => false,
        };

        //If the condition is true, execute the closure
        if ($isTrue) {
            $closure();
        }
    }
}

if (!function_exists('parseKeyValueString')) {
    function parseKeyValueString($data = null, string $page = 'api'): string|null
    {
        if (is_null($data)) {
            return null;
        }

        // Split the string into key-value pairs
        $pairs = explode('|', $data);
        // Extract the first key (the primary identifier or title)
        $firstPair = array_shift($pairs);

        // Return translation directly if there are no additional pairs
        if (empty($pairs)) {
            return __("$page.$firstPair");
        }

        // Parse key-value pairs into an associative array
        $result = array_reduce($pairs, function (array $carry, string $pair) use ($page) {
            if (!str_contains($pair, '=')) {
                return $carry; // Skip invalid pairs
            }

            [$key, $value] = explode('=', $pair, 2);

            if ($key !== 'prefix') {
                $carry[trim($key)] = trim($value);
            }

            return $carry;
        }, []);

        // Return the translation with parameters
        return __("$page.$firstPair", $result);
    }
}


if (!function_exists('rootUsers')) {
    function rootUsers(): array
    {
        return User::whereHas('roles', static fn($q) => $q->where('name', 'root'))
            ->pluck('id')
            ->toArray();
    }
}

if (!function_exists('utf8StrRev')) {
    function utf8StrRev($str = null): ?string
    {
        if ($str) {
            preg_match_all('/./us', $str, $ar);
            return join('', array_reverse($ar[0]));
        }
        return null;
    }
}

if (!function_exists('safeExecute')) {
    function safeExecute($callback, $return = true)
    {
        try {
            $callback();
        } catch (QueryException|Exception|Error|QueryException $e) {
            logError($e);

            if (config('app.env', 'local')) {
                throw $e;  // Re-throw the exception for local environment
            }

            return $return;
        }
    }
}

if (!function_exists('prepareModelType')) {
    function prepareModelType($model): string
    {
        return strtolower(Arr::last(explode('\\', $model)));
    }
}

if (!function_exists('allModelsNames')) {
    function allModelsNames(): Collection
    {
        $modelPath = app_path('Models');
        return collect(File::allFiles($modelPath))
            ->map(function ($file) {
                return str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    $file->getRelativePathname()
                );
            });
    }
}

if (!function_exists('allAttributesFillableModels')) {
    function allAttributesFillableModels(): array
    {
        $modelPath = app_path('Models');
        $models = collect(File::allFiles($modelPath))
            ->map(function ($file) {
                $namespace = 'App\\Models\\';
                $class = $namespace . str_replace(
                        ['/', '.php'],
                        ['\\', ''],
                        $file->getRelativePathname()
                    );
                return new $class;
            });

        $fillableAttributes = [];

        foreach ($models as $model) {
            $fillableAttributes = array_merge($fillableAttributes, $model->getFillable());
        }

        return array_unique($fillableAttributes);
    }
}


function getCurrentGuard(): int|string|null
{
    foreach (array_keys(config('auth.guards')) as $guard) {
        if (auth()->guard($guard)->check()) {
            return $guard;
        }
    }
    return null; // No guard is currently authenticated
}
