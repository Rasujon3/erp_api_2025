<?php

namespace App\Modules\Loans\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'loan';

    protected $fillable = [
        'employee_id',
        'permitted_by',
        'description',
        'amount',
        'approved_date',
        'repayment_from',
        'interest_percentage',
        'installment_period',
        'repayment_amount',
        'installment',
        'status',
        'date',
        'posted'
    ];

    public static function rules()
    {
        return [
            'amount' => 'required|numeric|min:0',
            'approved_date' => 'required|date',
            'repayment_from' => 'required|date|after_or_equal:approved_date',
            'interest_percentage' => 'required|numeric|min:0',
            'installment_period' => 'required|integer|min:1',
            'repayment_amount' => 'required|numeric|min:0',
            'installment' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'date'=>"required|date",
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')->whereNull('deleted_at')
            ],
            'permitted_by' => [
                'required',
                Rule::exists('employees', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public static function bulkRules()
    {
        return [
            'loans' => 'required|array|min:1',
            'loans.*.id' => [
                'required',
                Rule::exists('loan', 'id')->whereNull('deleted_at')
            ],
            'loans.*.amount' => 'required|numeric|min:0',
            'loans.*.approved_date' => 'required|date',
            'loans.*.repayment_from' => 'required|date|after_or_equal:approved_date',
            'loans.*.interest_percentage' => 'required|numeric|min:0',
            'loans.*.installment_period' => 'required|integer|min:1',
            'loans.*.repayment_amount' => 'required|numeric|min:0',
            'loans.*.installment' => 'required|numeric|min:0',
            'loans.*.status' => 'required|in:active,inactive',
            'loans.*.date'=>"required|date",
            'loans.*.employee_id' => [
                'required',
                Rule::exists('employees', 'id')->whereNull('deleted_at')
            ],
            'loans.*.permitted_by' => [
                'required',
                Rule::exists('employees', 'id')->whereNull('deleted_at')
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
