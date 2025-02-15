<?php

namespace App\Modules\Groups\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;
//    use HasFactory;

    protected $table = 'groups';

    protected $fillable = [
        'name',
        'description',
    ];

    public static function rules($groupId = null)
    {
        return [
            'name' => 'required|unique:groups,name,' . $groupId . ',id',
            'description' => 'nullable',
        ];
    }
}
