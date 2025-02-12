<?php

namespace App\Modules\Stores\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\States\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;
//    use HasFactory;

    protected $table = 'stores';

    protected $fillable = [
        'name',
        'description'
    ];

    public static function rules($storeId = null)
    {
        return [
            'name' => 'required|unique:stores,name,' . $storeId . ',id', // Make sure the name is unique except for the current store
        ];
    }
}
