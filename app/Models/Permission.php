<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'module_name', 'sub_module_name', 'display_name', 'guard_name'];
}
