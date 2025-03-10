<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminClient extends Authenticatable
{
    protected $table = 'admin_clients';

    protected $fillable = [
        'code',
        'name',
        'admin_user_id',
        'type',
        'password',
        'confirm_password',
        'otp_enable',
        'is_active',
        'is_draft',
        'is_delete',
        'address',
        'phone',
        'mobile',
        'email',
        'website',
        'location',
    ];

    protected $casts = [
        'otp_enable' => 'boolean',
        'is_active' => 'boolean',
        'is_draft' => 'boolean',
        'is_delete' => 'boolean',
    ];

    protected $hidden = [
        'password',
        'confirm_password',
    ];

    // Relationship
    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id');
    }

    public function getAuthPassword()
    {
        return $this->password;
    }
}
