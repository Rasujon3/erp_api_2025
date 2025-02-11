<?php

namespace App\Modules\Currencies\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Currency extends Model
{
//    use HasFactory, SoftDeletes;
    use HasFactory;

    protected $table = 'currencies';

    protected $fillable = [
        'name',
        'description'
    ];

    public static function rules($currencyId = null)
    {
        Log::info('$currencyId ID from model:', ['$currencyId' => $currencyId]);
        return [
            'name' => 'required|unique:currencies,name,' . $currencyId . ',id', // Make sure the name is unique except for the current currency
        ];
    }
}
