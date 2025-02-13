<?php

namespace App\Modules\ProductUnits\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\States\Models\State;
use App\Modules\TaxRates\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductUnit extends Model
{
//    use HasFactory, SoftDeletes;
    use HasFactory;

    protected $table = 'product_units';

    protected $fillable = [
        'title',
        'description'
    ];

    public static function rules($productUnitId = null)
    {
        return [
            'title' => 'required|unique:product_units,title,' . $productUnitId . ',id',
        ];
    }
}
