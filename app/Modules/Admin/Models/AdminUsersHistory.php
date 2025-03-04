<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUsersHistory extends Model
{
    protected $table = 'admin_users_histories';

    protected $fillable = [
        'users_id',
        'action_date',
        'action_type',
        'export_type',
        'export_pdf',
        'export_xls',
        'export_print',
    ];

    protected $casts = [
        'action_date' => 'datetime',
        'export_pdf' => 'boolean',
        'export_xls' => 'boolean',
        'export_print' => 'boolean',
    ];

    // Relationship
    public function user()
    {
        return $this->belongsTo(AdminUser::class, 'users_id');
    }
}
