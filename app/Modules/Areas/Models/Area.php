<?php

namespace App\Modules\Areas\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory, SoftDeletes;
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
        return [
            'name' => 'required|unique:areas,name,' . $areaId . ',id', // Make sure the name is unique except for the current area
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'state_id' => 'required|exists:states,id',
        ];
    }
    public function country() : belongsTo
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function state() : belongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function city() : belongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
