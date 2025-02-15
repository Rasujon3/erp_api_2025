<?php

namespace App\Modules\Divisions\Models;

use App\Modules\Groups\Models\Group;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use HasFactory, SoftDeletes;
//    use HasFactory;

    protected $table = 'divisions';

    protected $fillable = [
        'name',
        'description',
        'group_id',
    ];

    public static function rules($divisionId = null)
    {
        return [
            'name' => 'required|unique:divisions,name,' . $divisionId . ',id',
            'description' => 'nullable',
            'group_id' => 'required|exists:groups,id',
        ];
    }
    // belongsTo
    public function group() : BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
