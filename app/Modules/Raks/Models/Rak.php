<?php

namespace App\Modules\Raks\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Rak extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'raks';

    protected $fillable = [
        'code',
        'name',
        'name_in_bangla',
        'name_in_arabic',
        'is_default',
        'draft',
        'drafted_at',
        'is_active',
    ];

    public static function rules($rakId = null)
    {
        $uniqueCodeRule = Rule::unique('raks', 'code')
            ->whereNull('deleted_at');

        if ($rakId) {
            $uniqueCodeRule->ignore($rakId);
        }
        return [
            'code' => ['required', 'string', 'max:45', $uniqueCodeRule],
            'name' => 'required|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u', // regex for English characters with spaces
            'name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u', // regex for Bangla characters with spaces
            'name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u', // regex for Arabic characters with spaces
            'is_default' => 'required|boolean',
            'draft' => 'required|boolean',
            'drafted_at' => 'nullable|date',
            'is_active' => 'required|boolean',
        ];
    }
    public static function bulkRules()
    {
        return [
            'raks' => 'required|array|min:1',
            'raks.*.id' => [
                'required',
                Rule::exists('raks', 'id')->whereNull('deleted_at')
            ],
            'raks.*.code' => [
                'required',
                'string',
                'max:45',
                function ($attribute, $value, $fail) {
                    $rakId = request()->input(str_replace('.code', '.id', $attribute));
                    $exists = Rak::where('code', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $rakId)
                        ->exists();

                    if ($exists) {
                        $fail('The code "' . $value . '" has already been taken.');
                    }
                },
                // New rule: Check for duplicates within the request
                function ($attribute, $value, $fail) {
                    $bins = request()->input('raks', []); // Get all code
                    $codes = array_column($bins, 'code'); // Get all code

                    // Find all occurrences of this code
                    $matches = array_keys(array_filter($codes, fn($num) => $num === $value)); // Get all indexes
                    if (count($matches) > 1) {
                        // Check if this is not the only occurrence for this id
                        $currentIndex = (int) str_replace('raks.', '', explode('.', $attribute)[1]); // Get current index
                        $otherMatches = array_filter($matches, fn($index) => $index !== $currentIndex); // Get all other indexes
                        if (!empty($otherMatches)) {
                            $fail('The code "' . $value . '" is duplicated within the request.');
                        }
                    }
                },
            ],
            'raks.*.name' => 'required|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'raks.*.name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
            'raks.*.name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'raks.*.is_default' => 'required|boolean',
            'raks.*.draft' => 'required|boolean',
            'raks.*.drafted_at' => 'nullable|date',
            'raks.*.is_active' => 'required|boolean',
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
