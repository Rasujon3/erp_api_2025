<?php

namespace App\Modules\TaxRates\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\States\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRate extends Model
{
//    use HasFactory, SoftDeletes;
    use HasFactory;

    protected $table = 'tax_rates';

    protected $fillable = [
        'name',
        'tax_rate',
    ];

    public static function rules($taxRateId = null)
    {
        return [
            'name' => 'required|unique:tax_rates,name,' . $taxRateId . ',id',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ];
    }
}
