<?php

namespace App\Modules\Items\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\States\Models\State;
use App\Modules\TaxRates\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'items';

    protected $fillable = [
        'title',
        'description',
        'rate',
        'tax_1_id',
        'tax_2_id',
        'item_group_id',
    ];

    public static function rules($itemId = null)
    {
        return [
            'title' => 'required|unique:items,title,' . $itemId . ',id',
            'rate' => 'required',
            'description' => 'nullable',
            'tax_1_id' => 'nullable|exists:tax_rates,id',
            'tax_2_id' => 'nullable|exists:tax_rates,id',
            'item_group_id' => 'required|exists:item_groups,id',
        ];
    }
    public function firstTax(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'tax_1_id', 'id');
    }
    public function secondTax(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'tax_2_id', 'id');
    }
    public function group(): BelongsTo
    {
        return $this->belongsTo(ItemGroup::class, 'item_group_id');
    }
}
