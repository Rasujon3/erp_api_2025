<?php

namespace App\Modules\Units\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'units';

    protected $fillable = [
        'name',
        'description',
    ];

    public static function rules($unitId = null)
    {
        return [
            'name' => 'required|unique:units,name,' . $unitId . ',id',
            'description' => 'nullable',
        ];
    }
}
