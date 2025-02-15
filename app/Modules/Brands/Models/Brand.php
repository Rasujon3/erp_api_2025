<?php

namespace App\Modules\Brands\Models;

use App\Modules\Divisions\Models\Division;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;
//    use HasFactory;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'description',
    ];

    public static function rules($brandId = null)
    {
        return [
            'name' => 'required|unique:brands,name,' . $brandId . ',id',
            'description' => 'nullable',
        ];
    }
}
