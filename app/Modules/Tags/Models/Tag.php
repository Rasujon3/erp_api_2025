<?php

namespace App\Modules\Tags\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
//    use HasFactory, SoftDeletes;
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = [
        'name',
        'description',
    ];

    public static function rules($tagId = null)
    {
        return [
            'name' => 'required|unique:tags,name,' . $tagId . ',id',
            'description' => 'nullable',
        ];
    }
}
