<?php

namespace App\Modules\Banks\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'banks';

    protected $fillable = [
        'name',
        'account_number',
        'branch_name',
        'swift_code',
        'description'
    ];

    public static function rules($bankId = null)
    {
        $uniqueAccountNumberRule = Rule::unique('banks', 'account_number')
            ->whereNull('deleted_at');

        if ($bankId) {
            $uniqueAccountNumberRule->ignore($bankId);
        }
        return [
            'name' => 'required|string|max:191',
            'account_number' => ['required', 'string', 'max:50', $uniqueAccountNumberRule],
            'branch_name' => 'nullable|string|max:191',
            'swift_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ];
    }
    public static function bulkRules()
    {
        return [
            'banks' => 'required|array|min:1',
            'banks.*.id' => [
                'required',
                Rule::exists('banks', 'id')->whereNull('deleted_at')
            ],
            'banks.*.account_number' => [
                'required',
                'string',
                'max:50',
                // Check database uniqueness (existing rule)
                function ($attribute, $value, $fail) {
                    $bankId = request()->input(str_replace('.account_number', '.id', $attribute));
                    $exists = Bank::where('account_number', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $bankId)
                        ->exists();

                    if ($exists) {
                        $fail('The account number "' . $value . '" has already been taken.');
                    }
                },
                // New rule: Check for duplicates within the request
                function ($attribute, $value, $fail) {
                    $banks = request()->input('banks', []); // Get all banks
                    $accountNumbers = array_column($banks, 'account_number'); // Get all account numbers

                    // Find all occurrences of this account_number
                    $matches = array_keys(array_filter($accountNumbers, fn($num) => $num === $value)); // Get all indexes
                    if (count($matches) > 1) {
                        // Check if this is not the only occurrence for this id
                        $currentIndex = (int) str_replace('banks.', '', explode('.', $attribute)[1]); // Get current index
                        $otherMatches = array_filter($matches, fn($index) => $index !== $currentIndex); // Get all other indexes
                        if (!empty($otherMatches)) {
                            $fail('The account number "' . $value . '" is duplicated within the request.');
                        }
                    }
                },
            ],
            'banks.*.name' => 'required|string|max:191',
            'banks.*.branch_name' => 'nullable|string|max:191',
            'banks.*.swift_code' => 'nullable|string|max:20',
            'banks.*.description' => 'nullable|string',
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
