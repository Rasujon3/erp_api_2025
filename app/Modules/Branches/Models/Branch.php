<?php

namespace App\Modules\Branches\Models;

use App\Modules\Banks\Models\Bank;
use App\Modules\Countries\Models\Country;
use App\Modules\Currencies\Models\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'branches';

    protected $fillable = [
        'code',
        'name',
        'company_name',
        'website',
        'vat_number',
        'city',
        'state',
        'bank_id',
        'country_id',
        'currency_id',
        'zip_code',
        'phone',
        'address',
        'is_default',
        'draft',
        'drafted_at',
        'is_active',
        'is_deleted',
        'status',
        'deleted_at',
    ];

    public static function rules($branchId = null)
    {
        return [
            # 'name' => 'required|unique:branches,name,' . $branchId . ',id', // Make sure the name is unique except for the current area
            'code' => [
                'nullable',
                'string',
                'max:45',
                Rule::unique('branches', 'code')->ignore($branchId),
            ],
            'name' => 'nullable|string|max:191',
            'company_name' => 'nullable|string|max:191',
            'website' => 'nullable|string|max:60|url',
            'vat_number' => 'nullable|string|max:191',
            'city' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:191',
            'bank_id' => [
                'required',
                Rule::exists('banks', 'id')->whereNull('deleted_at'),
            ],
            'country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at'),
            ],
            'currency_id' => [
                'required',
                Rule::exists('currencies', 'id')->whereNull('deleted_at'),
            ],
            'zip_code' => 'nullable|string|max:60',
            'phone' => 'nullable|string|max:60',
            'address' => 'nullable|string',
            'is_default' => 'nullable|boolean',
            'draft' => 'nullable|boolean',
            'drafted_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'is_deleted' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ];
    }
    public static function bulkRules()
    {
        return [
            'branches' => 'required|array|min:1',
            'branches.*.id' => [
                'required',
                Rule::exists('branches', 'id')->whereNull('deleted_at')
            ],
            'branches.*.code' => [
                'nullable',
                'string',
                'max:45',
                // Check database uniqueness (existing rule)
                function ($attribute, $value, $fail) {
                    $branchId = request()->input(str_replace('.code', '.id', $attribute));
                    $exists = Branch::where('code', $value)
                        # ->whereNull('deleted_at')
                        ->where('id', '!=', $branchId)
                        ->exists();

                    if ($exists) {
                        $fail('The code "' . $value . '" has already been taken.');
                    }
                },
                // New rule: Check for duplicates within the request
                function ($attribute, $value, $fail) {
                    $banks = request()->input('branches', []); // Get all branches
                    $accountNumbers = array_column($banks, 'code'); // Get all account numbers

                    // Find all occurrences of this code
                    $matches = array_keys(array_filter($accountNumbers, fn($num) => $num === $value)); // Get all indexes
                    if (count($matches) > 1) {
                        // Check if this is not the only occurrence for this id
                        $currentIndex = (int) str_replace('branches.', '', explode('.', $attribute)[1]); // Get current index
                        $otherMatches = array_filter($matches, fn($index) => $index !== $currentIndex); // Get all other indexes
                        if (!empty($otherMatches)) {
                            $fail('The code "' . $value . '" is duplicated within the request.');
                        }
                    }
                },
            ],
            'branches.*.name' => 'nullable|string|max:191',
            'branches.*.company_name' => 'nullable|string|max:191',
            'branches.*.website' => 'nullable|string|max:60|url',
            'branches.*.vat_number' => 'nullable|string|max:191',
            'branches.*.city' => 'nullable|string|max:191',
            'branches.*.state' => 'nullable|string|max:191',
            'branches.*.zip_code' => 'nullable|string|max:60',
            'branches.*.phone' => 'nullable|string|max:60',
            'branches.*.address' => 'nullable|string',
            'branches.*.is_default' => 'nullable|boolean',
            'branches.*.draft' => 'nullable|boolean',
            'branches.*.drafted_at' => 'nullable|date',
            'branches.*.is_active' => 'nullable|boolean',
            'branches.*.status' => 'nullable|boolean',
            'branches.*.country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
            'branches.*.bank_id' => [
                'required',
                Rule::exists('banks', 'id')->whereNull('deleted_at')
            ],
            'branches.*.currency_id' => [
                'required',
                Rule::exists('currencies', 'id')->whereNull('deleted_at')
            ],
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
    public function bank() : BelongsTo
    {
        return $this->belongsTo(Bank::class,'bank_id', 'id');
    }
    public function country() : BelongsTo
    {
        return $this->belongsTo(Country::class,'country_id', 'id');
    }
    public function currency() : BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
