<?php

namespace App\Modules\Items\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemGroup extends Model
{
    use HasFactory, SoftDeletes;

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
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($itemGroup) {
            if ($itemGroup->items()->exists()) {
                throw new Exception("This item group is used and cannot be deleted.");
            }
        });
    }
}
