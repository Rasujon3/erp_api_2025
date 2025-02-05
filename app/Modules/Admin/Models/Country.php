<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'countries';

    protected $fillable = [
        'code',
        'name',
        'is_default',
        'draft',
        'drafted_at',
        'is_active',
        'flag',       // Newly added field
    ];

    public static function rules($currencyId = null)
    {
        return [
            'code' => 'required|string|max:45|unique:countries,code,' . $currencyId,
            'name' => 'required|string|max:191',
            'drafted_at' => 'nullable|date',
            'is_default' => 'nullable',
        ];
    }
}
