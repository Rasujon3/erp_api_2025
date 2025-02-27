<?php

namespace App\Modules\Currencies\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'currencies';

    protected $fillable = [
        'code',
        'name',
        'name_in_bangla',
        'name_in_arabic',
        'is_default',
        'draft',
        'drafted_at',
        'is_active',
        'symbol',
        'exchange'
    ];

    public static function rules($currencyId = null)
    {
        $uniqueCodeRule = Rule::unique('currencies', 'code')
            ->whereNull('deleted_at');

        if ($currencyId) {
            $uniqueCodeRule->ignore($currencyId);
        }
        return [
            'code' => ['required', 'string', 'max:45', $uniqueCodeRule],
            'name' => 'required|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u', // regex for English characters with spaces
            'name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u', // regex for Bangla characters with spaces
            'name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u', // regex for Arabic characters with spaces
            'is_default' => 'boolean',
            'draft' => 'boolean',
            'drafted_at' => 'nullable|date',
            'is_active' => 'boolean',
            'symbol' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'exchange' => 'required|numeric|min:0|max:999999.99|regex:/^\d+(\.\d{1,2})?$/',
        ];
    }
    public static function bulkRules()
    {
        return [
            'currencies' => 'required|array|min:1',
            'currencies.*.id' => [
                'required',
                Rule::exists('currencies', 'id')->whereNull('deleted_at')
            ],
            'currencies.*.code' => [
                'required',
                'string',
                'max:45',
                function ($attribute, $value, $fail) {
                    $currencyId = request()->input(str_replace('.code', '.id', $attribute));
                    $exists = Currency::where('code', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $currencyId)
                        ->exists();

                    if ($exists) {
                        $fail('The currency code "' . $value . '" has already been taken.');
                    }
                },
            ],
            'currencies.*.name' => 'required|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'currencies.*.name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
            'currencies.*.name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'currencies.*.is_default' => 'boolean',
            'currencies.*.draft' => 'boolean',
            'currencies.*.drafted_at' => 'nullable|date',
            'currencies.*.is_active' => 'boolean',
            'currencies.*.flag' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'currencies.*.exchange' => 'required|numeric|min:0|max:999999.99|regex:/^\d+(\.\d{1,2})?$/',
        ];
    }
    public static function listRules()
    {
        return [
            'draft' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'is_deleted' => 'nullable|boolean',
            'is_updated' => 'nullable|boolean',
        ];
    }
}
