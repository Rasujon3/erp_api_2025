<?php

namespace App\Modules\Areas\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Area extends Model
{
//    use HasFactory, SoftDeletes;
    use HasFactory;

    protected $table = 'areas';

    protected $fillable = [
        'name',
        'country_id',
        'city_id',
        'state_id',
        'description'
    ];

    public static function rules($areaId = null)
    {
        Log::info('Area ID from model:', ['areaId' => $areaId]);
        return [
            'name' => 'required|unique:areas,name,' . $areaId . ',id', // Make sure the name is unique except for the current area
            # 'name' => 'required|unique:areas,name,' . $areaId,
            # 'name' => 'required|unique:areas,name,' . $areaId . ',name', // Make sure the name is unique except for the current area
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'state_id' => 'required|exists:states,id',
        ];
    }
    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
