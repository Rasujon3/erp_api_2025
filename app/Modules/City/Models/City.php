<?php

namespace App\Modules\City\Models;

use App\Modules\Areas\Models\Area;
use App\Modules\Countries\Models\Country;
use App\Modules\States\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cities';

    protected $fillable = [
        'code',
        'name',
        'name_in_bangla',
        'name_in_arabic',
        'is_default',
        'draft',
        'drafted_at',
        'is_active',
        'country_id',
        'state_id'
    ];

    public static function rules($cityId = null)
    {
        $uniqueCodeRule = Rule::unique('cities', 'code')
            ->whereNull('deleted_at');

        if ($cityId) {
            $uniqueCodeRule->ignore($cityId);
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
            'country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
            'state_id' => [
                'required',
                Rule::exists('states', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public static function bulkRules()
    {
        return [
            'cities' => 'required|array|min:1',
            'cities.*.id' => [
                'required',
                Rule::exists('cities', 'id')->whereNull('deleted_at')
            ],
            'cities.*.code' => [
                'required',
                'string',
                'max:45',
                function ($attribute, $value, $fail) {
                    $cityId = request()->input(str_replace('.code', '.id', $attribute));
                    $exists = City::where('code', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $cityId)
                        ->exists();

                    if ($exists) {
                        $fail('The city code "' . $value . '" has already been taken.');
                    }
                },
            ],
            'cities.*.name' => 'required|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'cities.*.name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
            'cities.*.name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'cities.*.is_default' => 'boolean',
            'cities.*.draft' => 'boolean',
            'cities.*.drafted_at' => 'nullable|date',
            'cities.*.is_active' => 'boolean',
            'cities.*.country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
            'cities.*.state_id' => [
                'required',
                Rule::exists('states', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public function country() : belongsTo
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function state() : belongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function area() : hasMany
    {
        return $this->hasMany(Area::class, 'city_id');
    }
}
