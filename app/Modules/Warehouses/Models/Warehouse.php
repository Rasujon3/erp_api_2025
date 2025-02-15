<?php

namespace App\Modules\Warehouses\Models;

use App\Modules\Divisions\Models\Division;
use App\Modules\Groups\Models\Group;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;
//    use HasFactory;

    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'description',
        'division_id',
    ];

    public static function rules($warehouseId = null)
    {
        return [
            'name' => 'required|unique:warehouses,name,' . $warehouseId . ',id',
            'description' => 'nullable',
            'division_id' => 'required|exists:divisions,id',
        ];
    }
    // belongsTo
    public function division() : BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
}
