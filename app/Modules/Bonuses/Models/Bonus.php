<?php

namespace App\Modules\Bonuses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Bonus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bonuses';

    protected $fillable = [
        'employee_id',
        'amount',
        'bonus_type_id',
        'description',
        'date',
        'posted'
    ];

    public static function rules()
    {
        return [
            'amount' => 'required|string|max:191',
            'date'=> "required|date",
            'description'=> "nullable|string",
            'posted'=> "nullable|boolean",
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')->whereNull('deleted_at')
            ],
            'bonus_type_id' => [
                'required',
                Rule::exists('bonus_types', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public static function bulkRules()
    {
        return [
            'bonuses' => 'required|array|min:1',
            'bonuses.*.id' => [
                'required',
                Rule::exists('bonuses', 'id')->whereNull('deleted_at')
            ],
            'bonuses.*.amount' => 'required|string|max:191',
            'bonuses.*.date'=> "required|date",
            'bonuses.*.description'=> "nullable|string",
            'bonuses.*.posted'=> "nullable|boolean",
            'bonuses.*.employee_id' => [
                'required',
                Rule::exists('employees', 'id')->whereNull('deleted_at')
            ],
            'bonuses.*.bonus_type_id' => [
                'required',
                Rule::exists('bonus_types', 'id')->whereNull('deleted_at')
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
