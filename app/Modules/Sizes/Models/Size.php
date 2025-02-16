<?php

namespace App\Modules\Sizes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Size extends Model
{
    use HasFactory, SoftDeletes;
//    use HasFactory;

    protected $table = 'sizes';

    protected $fillable = [
        'name',
        'description',
    ];

    public static function rules($sizeId = null)
    {
        return [
            'name' => 'required|unique:sizes,name,' . $sizeId . ',id',
            'description' => 'nullable',
        ];
    }
}
