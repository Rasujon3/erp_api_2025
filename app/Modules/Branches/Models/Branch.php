<?php

namespace App\Modules\Branches\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\City\Models\City;
use App\Modules\Currencies\Models\Currency;
use App\Modules\States\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'branches';

    protected $fillable = [
        'company_name',
        'name',
        'website',
        'vat_number',
        'currency_id',
        'city',
        'state',
        'country_id',
        'zip_code',
        'phone',
        'address'
    ];

    public static function rules($branchId = null)
    {
        return [
            'company_name' => 'required',
            'name' => 'required|unique:branches,name,' . $branchId . ',id', // Make sure the name is unique except for the current area
            'country_id' => 'exists:countries,id',
            'currency_id' => 'exists:currencies,id',
        ];
    }
    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
