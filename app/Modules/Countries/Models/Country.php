<?php

namespace App\Modules\Countries\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'countries';

    protected $fillable = [
        'code',
        'name',
        'name_in_bangla',
        'name_in_arabic',
        'is_default',
        'draft',
        'drafted_at',
        'is_active',
        'flag',
    ];

    public static function rules($countryId = null)
    {
        $uniqueCodeRule = Rule::unique('countries', 'code')
            ->whereNull('deleted_at');

        if ($countryId) {
            $uniqueCodeRule->ignore($countryId);
        }
        return [
            'code' => ['required', 'string', 'max:45', $uniqueCodeRule],
            'name' => 'required|string|max:191||regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u', // regex for English characters with spaces
            'name_in_bangla' => 'required|string|max:191|regex:/^[\p{Bengali}\s]+$/u', // regex for Bangla characters with spaces
            'name_in_arabic' => 'required|string|max:191|regex:/^[\p{Arabic}\s]+$/u', // regex for Arabic characters with spaces
            'is_default' => 'boolean',
            'draft' => 'boolean',
            'drafted_at' => 'nullable|date',
            'is_active' => 'boolean',
            'flag' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
