<?php

namespace App\Modules\Departments\Models;

use App\Modules\Category\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'description',
    ];

    public static function rules($departmentId = null)
    {
        return [
            'name' => 'required|unique:departments,name,' . $departmentId . ',id',
            'description' => 'nullable',
        ];
    }
    public function categories() : HasMany
    {
        return $this->hasMany(Category::class, 'department_id');
    }
}
