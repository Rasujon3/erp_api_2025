<?php

namespace App\Modules\Colors\Models;

use App\Modules\Divisions\Models\Division;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{
    use HasFactory, SoftDeletes;
//    use HasFactory;

    protected $table = 'colors';

    protected $fillable = [
        'name',
        'description',
    ];

    public static function rules($colorId = null)
    {
        return [
            'name' => 'required|unique:colors,name,' . $colorId . ',id',
            'description' => 'nullable',
        ];
    }
}
