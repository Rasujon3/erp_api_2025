<?php

namespace App\Modules\AdminGroups\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class AdminGroupTemplate extends Model
{
    use HasFactory;

    protected $table = 'admin_group_templates';

    protected $fillable = [
        'code',
        'english',
        'arabic',
        'bengali',
    ];

    public static function rules($adminGroupId = null)
    {
        return [
            'code' => [
                'required',
                'string',
                'max:191',
                Rule::unique('admin_group_templates', 'code')
                    ->ignore($adminGroupId)
                    ->whereNull('deleted_at'),
            ],
            'english' => 'required|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'bengali' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
        ];
    }
}
