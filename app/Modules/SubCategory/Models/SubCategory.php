<?php

namespace App\Modules\SubCategory\Models;

use App\Modules\Category\Models\Category;
use App\Modules\Departments\Models\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sub_categories';

    protected $fillable = [
        'name',
        'description',
        'category_id',
    ];

    public static function rules($subCategoryId = null)
    {
        return [
            'name' => 'required|unique:sub_categories,name,' . $subCategoryId . ',id',
            'description' => 'nullable',
            'category_id' => 'required|exists:categories,id',
        ];
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
