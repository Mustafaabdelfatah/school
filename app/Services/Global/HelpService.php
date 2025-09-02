<?php

namespace App\Services\Global;

use App\Http\Requests\Global\Help\HelpEnumRequest;
use App\Http\Requests\Global\Help\HelpModelRequest;
use App\Models\Role;
use App\Models\User;
use Error;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HelpService
{
    /**
     * @param HelpModelRequest $request
     * @return array
     */
    public function getModels(HelpModelRequest $request): array
    {
        $result = [];

        foreach ($request->tables as $table) {
            try {
                when(!$model = resolveModel($table['name'], @$table['module']), fn() => throw new \RuntimeException());

                $select = $this->determineSelectFields($table);

                $this->applyScopes($model, @$table['scopes'], @$table['values']);

                $result[$table['name']] = $model->select($select)
                    ->get()
                    ->transform(fn($record) => $this->transformRecord($record, $select))
                    ->toArray();

            } catch (Exception|Error $e) {
                logError($e);
                $result[$table['name']] = [];
            }
        }

        return $result;
    }

    /**
     * @param HelpEnumRequest $request
     * @return array
     */
    public function getEnums(HelpEnumRequest $request): array
    {
        if (!$request->enums) {
            return array_merge($this->getDefaultEnums(), $this->getDefaultModuleEnums());
        }

        return collect($request->enums)->mapWithKeys(function ($enum) {
            $name = implode("\\", array_map(fn($i) => ucfirst(Str::camel($i)), explode('.', $enum['name'])));

            $enumPath = !empty($enum['module'])
                ? "Modules\\" . ucfirst(Str::camel($enum['module'])) . "\\App\\Enum\\{$name}Enum"
                : "App\\Enum\\{$name}Enum";

            try {
                return [$enum['name'] => $enumPath::getList()];
            } catch (\Exception|Error) {
                return [$enum['name'] => []];
            }
        })->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */
    private function getDefaultEnums(): array
    {
        $result = [];
        $this->resolveFilesFromDir(app_path('Enum'), $result);

        collect($result)->each(function ($enum, $name) use (&$result) {
            $result[$name] = $enum::getList();
        });

        return $result;
    }

    private function getDefaultModuleEnums(): array
    {
        $result = [];
        if (is_dir(base_path('Modules'))) {
            collect(glob(base_path('Modules') . '/*', GLOB_ONLYDIR))
                ->filter(fn($path) => is_dir("$path\\App\\Enum"))
                ->each(function ($path) use (&$result) {
                    $this->resolveFilesFromDir("$path\\App\\Enum", $result);

                    collect($result)->each(function ($enum, $name) use (&$result) {
                        $result[$name] = $enum::getList();
                    });
                });
        }

        return $result;
    }

    private function resolveFilesFromDir(string $dir, array &$result): void
    {
        collect(scandir($dir))
            ->filter(fn($file) => !Str::startsWith($file, '.'))
            ->each(function ($file) use ($dir, &$result) {
                $filePath = "$dir\\$file";

                if (is_dir($filePath)) {
                    $this->resolveFilesFromDir($filePath, $result);
                } elseif (is_file($filePath)) {
                    $result[$this->resolveEnumKey($filePath)] = ucfirst(str_replace([base_path() . "\\", '.php'], ['', ''], $filePath));
                }
            });
    }

    private function resolveEnumKey(string $filePath): string
    {
        return Str::of($filePath)
            ->after('Enum\\')
            ->replace(['\\', '.php'], ['.', ''])
            ->snake()
            ->replaceLast('_enum', '')
            ->replace('._', '.')
            ->toString();
    }

    private function applyScopes(&$model, $scopes = null, $values = null): void
    {
        if ($model instanceof User || $model instanceof Role) {
            $model = $model->excludeRoot();
        }
        foreach (Arr::wrap($scopes) as $key => $scope) {
            try {
                $model = !isset($values[$key])
                    ? $model->$scope()
                    : $model->$scope(...Arr::wrap(@$values[$key]));
            } catch (Exception|Error $e) {
                logError($e);
            }
        }
    }

    private function determineSelectFields(array $table): array
    {
        $select = match (true) {
            Schema::hasColumn($table['name'], 'display_name') => ['id', 'display_name'],
            default => ['id', 'name'],
        };

        if (!empty($table['extra'])) {
            $select = array_merge($select, Arr::wrap($table['extra']));
        }

        return $select;
    }

    private function transformRecord($record, array $select): array
    {
        $item = [];
        foreach ($select as $r) {
            $item[$r] = $record->{$r};
        }

        if (array_key_exists('display_name', $record->getAttributes())) {
            $item['name'] = $record->display_name;
            unset($item['display_name']);
        }

        return $item;
    }
}
