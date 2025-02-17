<?php

namespace App\Modules\Departments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'description',
    ];

    public static function rules($departmentId = null)
    {
        return [
            'name' => 'required|unique:departments,name,' . $departmentId . ',id',
            'description' => 'nullable',
        ];
    }
}
