<?php

namespace App\Modules\Allowances\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Allowance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'allowances';

    protected $fillable = [
        'employee_id',
        'allowance_type_id',
        'amount',
        'description',
        'date',
        'posted'
    ];

    public static function rules()
    {
        return [
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:191',
            'posted' => 'nullable|boolean',
            'date'=>"required|date",
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')->whereNull('deleted_at')
            ],
            'allowance_type_id' => [
                'required',
                Rule::exists('allowance_types', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public static function bulkRules()
    {
        return [
            'allowances' => 'required|array|min:1',
            'allowances.*.id' => [
                'required',
                Rule::exists('allowances', 'id')->whereNull('deleted_at')
            ],
            'allowances.*.amount' => 'required|numeric|min:0',
            'allowances.*.description' => 'nullable|string|max:191',
            'allowances.*.posted' => 'nullable|boolean',
            'allowances.*.date'=>"required|date",
            'allowances.*.employee_id' => [
                'required',
                Rule::exists('employees', 'id')->whereNull('deleted_at')
            ],
            'allowances.*.allowance_type_id' => [
                'required',
                Rule::exists('allowance_types', 'id')->whereNull('deleted_at')
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
