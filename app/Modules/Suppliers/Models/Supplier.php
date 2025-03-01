<?php

namespace App\Modules\Suppliers\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'suppliers';

    protected $fillable = [
        'company_name',
        'vat_number',
        'phone',
        'website',
        'currency',
        'country',
        'default_language',
        'street',
        'city',
        'state',
        'zip'
    ];

    public static function rules($supplierId = null)
    {
        $uniqueCompanyNameRule = Rule::unique('suppliers', 'company_name')
            ->whereNull('deleted_at');

        if ($supplierId) {
            $uniqueCompanyNameRule->ignore($supplierId);
        }
        $uniquePhoneRule = Rule::unique('suppliers', 'phone')
            ->whereNull('deleted_at');

        if ($supplierId) {
            $uniquePhoneRule->ignore($supplierId);
        }
        return [
            'company_name' => ['required', 'string', 'max:191', $uniqueCompanyNameRule],
            'phone' => ['required', 'string', 'max:191', $uniquePhoneRule],
            'vat_number' => "required|max:191",
            'website' => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
            'currency' => "required|integer|max:11",
            'country' => "required|integer|max:11",
            'default_language' => "required|max:191",
            'street' => "required|max:191",
            'city' => "required|max:191",
            'state' => "required|max:191",
            'zip' => "required|max:191",
        ];
    }
    public static function bulkRules()
    {
        return [
            'suppliers' => 'required|array|min:1',
            'suppliers.*.id' => [
                'required',
                Rule::exists('suppliers', 'id')->whereNull('deleted_at')
            ],
            'suppliers.*.company_name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $customerId = request()->input(str_replace('.company_name', '.id', $attribute));
                    $exists = Supplier::where('company_name', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $customerId)
                        ->exists();

                    if ($exists) {
                        $fail('The company name "' . $value . '" has already been taken.');
                    }
                },
            ],
            'suppliers.*.phone' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $customerId = request()->input(str_replace('.phone', '.id', $attribute));
                    $exists = Supplier::where('phone', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $customerId)
                        ->exists();

                    if ($exists) {
                        $fail('The phone "' . $value . '" has already been taken.');
                    }
                },
            ],
            'suppliers.*.vat_number' => "required|max:191",
            'suppliers.*.website' => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
            'suppliers.*.currency' => "required|integer|max:11",
            'suppliers.*.country' => "required|integer|max:11",
            'suppliers.*.default_language' => "required|max:191",
            'suppliers.*.street' => "required|max:191",
            'suppliers.*.city' => "required|max:191",
            'suppliers.*.state' => "required|max:191",
            'suppliers.*.zip' => "required|max:191",
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
