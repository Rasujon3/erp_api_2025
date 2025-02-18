<?php

namespace App\Modules\Category\Models;

use App\Modules\Departments\Models\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'department_id',
    ];

    public static function rules($categoryId = null)
    {
        return [
            'name' => 'required|unique:categories,name,' . $categoryId . ',id',
            'description' => 'nullable',
            'department_id' => 'required|exists:departments,id',
        ];
    }

    public function department() : BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
