<?php

namespace App\Modules\States\Models;

use App\Modules\Areas\Models\Area;
use App\Modules\City\Models\City;
use App\Modules\Countries\Models\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class State extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'states';

    protected $fillable = [
        'name',
        'name_in_bangla',
        'name_in_arabic',
        'is_default',
        'draft',
        'drafted_at',
        'is_active',
        'country_id',
        'description'
    ];

    public static function rules($stateId = null)
    {
        $uniqueNameRule = Rule::unique('states', 'name')
            ->whereNull('deleted_at');

        if ($stateId) {
            $uniqueNameRule->ignore($stateId);
        }
        return [
            'name' => ['required', 'string', 'max:191', 'regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u' , $uniqueNameRule],
            'name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u', // regex for Bangla characters with spaces
            'name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u', // regex for Arabic characters with spaces
            'is_default' => 'boolean',
            'draft' => 'boolean',
            'drafted_at' => 'nullable|date',
            'is_active' => 'boolean',
            'country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at') // Check if country exists & is NOT soft-deleted
            ],
            'description' => 'nullable|string'
        ];
    }
    public static function bulkRules()
    {
        return [
            'states' => 'required|array|min:1',
            'states.*.id' => [
                'required',
                Rule::exists('states', 'id')->whereNull('deleted_at')
            ],
            'states.*.name' => [
                'required',
                'string',
                'max:191',
                'regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
                function ($attribute, $value, $fail) {
                    $stateId = request()->input(str_replace('.name', '.id', $attribute));
                    $exists = State::where('name', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $stateId)
                        ->exists();

                    if ($exists) {
                        $fail('The state name "' . $value . '" has already been taken.');
                    }
                },
            ],
            'states.*.name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
            'states.*.name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'states.*.is_default' => 'boolean',
            'states.*.draft' => 'boolean',
            'states.*.drafted_at' => 'nullable|date',
            'states.*.is_active' => 'boolean',
            'states.*.country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at') // Check if country exists & is NOT soft-deleted
            ],
            'states.*.description' => 'nullable|string',
        ];
    }
    public function country() : belongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function city() : hasMany
    {
        return $this->hasMany(City::class, 'state_id');
    }
    public function area() : hasMany
    {
        return $this->hasMany(Area::class, 'city_id');
    }
}
