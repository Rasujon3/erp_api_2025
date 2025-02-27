<?php

namespace App\Modules\Customers\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';

    const LANGUAGES = [
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'ru' => 'Russian',
        'pt' => 'Portuguese',
        'ar' => 'Arabic',
        'zh' => 'Chinese',
        'tr' => 'Turkish',
    ];

    const CURRENCIES = [
        '0' => 'SAR',
        '1' => 'AED',
        '2' => 'AUD',
        '3' => 'USD',
        '4' => 'EUR',
        '5' => 'JPY',
        '6' => 'GBP',
        '7' => 'CAD',
    ];

    protected $fillable = [
        'company_name',
        'code',
        'phone',
        'zip',
        'website',
        'country',
        'state',
        'currency',
        'mobile',
        'whatsapp',
        'vat_number',
        'default_language',
        'inactive',
        'location_url',
        'customer_logo',
        'fax',
        'address',
        'short_name',
        'vendor_code'
    ];

    public static function rules($customerId = null)
    {
        $uniqueCodeRule = Rule::unique('customers', 'code')
            ->whereNull('deleted_at');

        if ($customerId) {
            $uniqueCodeRule->ignore($customerId);
        }
        $uniqueCompanyNameRule = Rule::unique('customers', 'company_name')
            ->whereNull('deleted_at');

        if ($customerId) {
            $uniqueCompanyNameRule->ignore($customerId);
        }
        $uniquePhoneRule = Rule::unique('customers', 'phone')
            ->whereNull('deleted_at');

        if ($customerId) {
            $uniquePhoneRule->ignore($customerId);
        }
        return [
            'company_name' => ['required', 'string', 'max:191', $uniqueCompanyNameRule],
            'code' => ['required', 'string', 'max:100', $uniqueCodeRule],
            'phone' => ['required', 'string', 'max:191', $uniquePhoneRule],
            'zip' => 'nullable|max:6',
            'website' => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
            'country' => "required",
            'currency' => "required",
            'vat_number' => "required",
            'mobile' => "required",
            'whatsapp' => "required",
            'default_language' => "required",
            'inactive' => "nullable",
            'location_url' => "nullable",
            'customer_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Allow only JPG, JPEG, PNG images
            'fax' => "nullable",
            'address' => "nullable",
            'short_name' => "nullable",
            'vendor_code' => 'nullable',
        ];
    }
    public static function bulkRules()
    {
        return [
            'customers' => 'required|array|min:1',
            'customers.*.id' => [
                'required',
                Rule::exists('customers', 'id')->whereNull('deleted_at')
            ],
            'customers.*.company_name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $customerId = request()->input(str_replace('.company_name', '.id', $attribute));
                    $exists = Customer::where('company_name', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $customerId)
                        ->exists();

                    if ($exists) {
                        $fail('The company name "' . $value . '" has already been taken.');
                    }
                },
            ],
            'customers.*.code' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $customerId = request()->input(str_replace('.code', '.id', $attribute));
                    $exists = Customer::where('code', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $customerId)
                        ->exists();

                    if ($exists) {
                        $fail('The currency code "' . $value . '" has already been taken.');
                    }
                },
            ],
            'customers.*.phone' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $customerId = request()->input(str_replace('.phone', '.id', $attribute));
                    $exists = Customer::where('phone', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $customerId)
                        ->exists();

                    if ($exists) {
                        $fail('The phone "' . $value . '" has already been taken.');
                    }
                },
            ],
            'customers.*.zip' => 'nullable|max:6',
            'customers.*.website' => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
            'customers.*.country' => "required",
            'customers.*.currency' => "required",
            'customers.*.vat_number' => "required",
            'customers.*.mobile' => "required",
            'customers.*.whatsapp' => "required",
            'customers.*.default_language' => "required",
            'customers.*.inactive' => "nullable",
            'customers.*.location_url' => "nullable",
            'customers.*.customer_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Allow only JPG, JPEG, PNG images
            'customers.*.fax' => "nullable",
            'customers.*.address' => "nullable",
            'customers.*.short_name' => "nullable",
            'customers.*.vendor_code' => 'nullable',
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
