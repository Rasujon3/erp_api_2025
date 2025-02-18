<?php

namespace App\Modules\Leaves\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
