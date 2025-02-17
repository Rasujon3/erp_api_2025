<?php

namespace App\Modules\TaxRates\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\Items\Models\Item;
use App\Modules\States\Models\State;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRate extends Model
{
    use HasFactory, SoftDeletes;

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
    public function firstItem(): HasMany
    {
        return $this->hasMany(Item::class, 'tax_1_id');
    }
    public function secondItem(): HasMany
    {
        return $this->hasMany(Item::class, 'tax_2_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($taxRate) {
            // Check if any item is using this TaxRate in either tax_1_id or tax_2_id
            if ($taxRate->firstItem()->exists() || $taxRate->secondItem()->exists()) {
                throw new Exception("This tax rate is used in items and cannot be deleted.");
            }
        });
    }
}
