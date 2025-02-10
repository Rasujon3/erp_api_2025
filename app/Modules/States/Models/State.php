<?php

namespace App\Modules\States\Models;

use App\Modules\Admin\Models\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
//    use HasFactory, SoftDeletes;
    use HasFactory;

    protected $table = 'states';

    protected $fillable = [
        'name',
        'country_id',
        'description'
    ];

    public static function rules($stateId = null)
    {
        return [
            'name' => 'required|unique:states,name,' . $stateId . ',id', // Make sure the name is unique except for the current state
            'country_id' => 'required|exists:countries,id'
        ];
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
