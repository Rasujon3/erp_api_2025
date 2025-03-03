<?php

namespace App\Modules\Designations\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Designation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'designations';

    protected $fillable = [
        'name',
        'description',
        'department_id',
        'sub_department_id'
    ];

    public static function rules()
    {
        return [
            'name' => 'required|string|max:191',
            'description' => 'nullable|string|max:191',
            'department_id' => [
                'required',
                Rule::exists('departments', 'id')->whereNull('deleted_at')
            ],
            'sub_department_id' => [
                'required',
                Rule::exists('sub_departments', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public static function bulkRules()
    {
        return [
            'designations' => 'required|array|min:1',
            'designations.*.id' => [
                'required',
                Rule::exists('designations', 'id')->whereNull('deleted_at')
            ],
            'designations.*.name' => 'required|string|max:191',
            'designations.*.description' => 'nullable|string|max:191',
            'designations.*.department_id' => [
                'required',
                Rule::exists('departments', 'id')->whereNull('deleted_at')
            ],
            'designations.*.sub_department_id' => [
                'required',
                Rule::exists('sub_departments', 'id')->whereNull('deleted_at')
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
    /*
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
    */
}
