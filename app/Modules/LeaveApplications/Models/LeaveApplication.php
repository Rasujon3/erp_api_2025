<?php

namespace App\Modules\LeaveApplications\Models;

use App\Modules\Leaves\Models\Leave;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leave_applications';

    protected $fillable = [
        'employee_id',
        'leave_id',
        'from_date',
        'end_date',
        'total_days',
        'hard_copy',
        'description',
    ];

    public static function rules()
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'leave_id'    => 'required|exists:leaves,id',
            'from_date'   => 'required|date',
            'end_date'    => 'required|date|after_or_equal:from_date',
            'total_days'  => 'required|integer|min:1',
            'description' => 'nullable|string',
            'hard_copy'   => 'nullable|file|mimes:pdf|max:2048',
        ];
    }
    public function leave(): BelongsTo
    {
        return $this->belongsTo(Leave::class, 'leave_id');
    }
//    public function employees(): BelongsTo
//    {
//        return $this->belongsTo(Employee::class, 'leave_id');
//    }
}
