<?php

namespace App\Modules\Countries\Models;

use App\Modules\AdminGroups\Models\AdminGroup;
use App\Modules\Areas\Models\Area;
use App\Modules\Branches\Models\Branch;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'is_deleted',
        'deleted_at',
    ];

    public static function rules($countryId = null)
    {
        $uniqueCodeRule = Rule::unique('countries', 'code')
            ->whereNull('deleted_at');

        if ($countryId) {
            $uniqueCodeRule->ignore($countryId);
        }
        return [
            'code' => ['nullable', 'string', 'max:45', $uniqueCodeRule],
            'name' => 'nullable|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u', // regex for English characters with spaces
            'name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u', // regex for Bangla characters with spaces
            'name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u', // regex for Arabic characters with spaces
            'is_default' => 'nullable|boolean',
            'draft' => 'nullable|boolean',
            'drafted_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'is_deleted' => 'nullable|boolean',
            'flag' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
    public static function bulkRules()
    {
        return [
            'countries' => 'required|array|min:1',
            'countries.*.id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
            'countries.*.code' => [
                'nullable',
                'string',
                'max:45',
                function ($attribute, $value, $fail) {
                    $countryId = request()->input(str_replace('.code', '.id', $attribute));
                    $exists = Country::where('code', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $countryId)
                        ->exists();

                    if ($exists) {
                        $fail('The country code "' . $value . '" has already been taken.');
                    }

                    $find = Country::find($countryId);

                    if (!$find) {
                        $fail('The "' . $value . '" country not found.');
                    }
                },
            ],
            'countries.*.name' => 'nullable|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'countries.*.name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
            'countries.*.name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'countries.*.is_default' => 'nullable|boolean',
            'countries.*.draft' => 'nullable|boolean',
            'countries.*.drafted_at' => 'nullable|date',
            'countries.*.is_active' => 'nullable|boolean',
            'countries.*.flag' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
    public static function importRules()
    {
        $uniqueCodeRule = Rule::unique('countries', 'code')
            ->whereNull('deleted_at');
        return [
            'countries' => 'required|array|min:1',
            'countries.*.code' => ['nullable', 'string', 'max:45', $uniqueCodeRule],
            'countries.*.name' => 'nullable|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'countries.*.name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
            'countries.*.name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'countries.*.is_default' => 'nullable|boolean',
            'countries.*.draft' => 'nullable|boolean',
            'countries.*.drafted_at' => 'nullable|date',
            'countries.*.is_active' => 'nullable|boolean',
            'countries.*.flag' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

    public function state() : hasMany
    {
        return $this->hasMany(State::class, 'country_id');
    }
    public function city() : hasMany
    {
        return $this->hasMany(City::class, 'country_id');
    }
    public function area() : hasMany
    {
        return $this->hasMany(Area::class, 'country_id');
    }
    public function adminGroups()
    {
        return $this->belongsToMany(
            AdminGroup::class,
            'group_countries',
            'country_id',
            'admin_group_id'
        )
            ->withTimestamps();
    }
    public function branches() : hasMany
    {
        return $this->hasMany(Branch::class, 'country_id', 'id');
    }
}
