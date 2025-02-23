<?php

namespace App\Modules\City\Models;

use App\Modules\Countries\Models\Country;
use App\Modules\States\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cities';

    protected $fillable = [
        'name',
        'country_id',
        'state_id',
        'description'
    ];

    public static function rules()
    {
        return [
            'name' => 'required',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id'
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
}
