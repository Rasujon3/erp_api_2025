<?php

namespace App\Modules\AdminGroups\Models;

use App\Modules\Countries\Models\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class GroupCountry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'group_countries';

    protected $fillable = [
        'admin_group_id',
        'country_id',
    ];

    public static function rules()
    {
        return [
            'admin_group_id' => [
                'required',
                Rule::exists('admin_groups', 'id')->whereNull('deleted_at')
            ],
            'country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public function country() : belongsTo
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function adminGroup() : belongsTo
    {
        return $this->belongsTo(AdminGroup::class,'admin_groups');
    }
}
