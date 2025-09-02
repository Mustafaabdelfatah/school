<?php

namespace App\Http\Requests\Global\Report;

use App\Enum\Global\ReportChartTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ReportRequest extends FormRequest
{
    /**
     * @return array<string>
     */
    public function rules(): array
    {
        return [
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
            'page' => 'nullable|string',
            'advanced' => 'nullable|array',
            'config' => 'nullable|array',
            'prefer_chart' => ['nullable','string',new Enum(ReportChartTypeEnum::class)],
        ];
    }
}
