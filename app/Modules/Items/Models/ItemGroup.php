<?php

namespace App\Modules\Items\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\States\Models\State;
use App\Modules\TaxRates\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemGroup extends Model
{
//    use HasFactory, SoftDeletes;
    use HasFactory;

    protected $table = 'item_groups';

    protected $fillable = [
        'name',
        'description',
    ];

    public static function rules($itemGroupId = null)
    {
        return [
            'name' => 'required|unique:item_groups,name,' . $itemGroupId . ',id',
        ];
    }
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'item_group_id');
    }
}
