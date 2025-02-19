<?php

namespace App\Modules\Leaves\Models;

use App\Modules\LeaveApplications\Models\LeaveApplication;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leaves';

    protected $fillable = [
        'name',
        'description',
    ];

    public static function rules($leaveId = null)
    {
        return [
            'name' => 'required|unique:leaves,name,' . $leaveId . ',id',
            'description' => 'nullable',
        ];
    }

    public function leaveApplications() : HasMany
    {
        return $this->hasMany(LeaveApplication::class, 'leave_id');
    }
}
