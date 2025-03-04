<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Validation\Rule;

class AdminUser extends Authenticatable
{
    protected $table = 'admin_users';

    protected $fillable = [
        'code',
        'name',
        'user_type',
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
        'location'
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

    public static function rules()
    {
        return [
            'code' => 'required|string|max:6|unique:admin_users,code',
            'name' => 'required|string|max:40',
            'user_type' => 'required|in:system,super_admin,admin,super_user,user',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
            'email' => 'nullable|email|max:40|unique:admin_users,email',
            'address' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:40',
            'location' => 'nullable|string|max:255',
        ];
    }

    // Relationships
    public function logs()
    {
        return $this->hasMany(AdminUsersLog::class, 'users_id');
    }

    public function histories()
    {
        return $this->hasMany(AdminUsersHistory::class, 'users_id');
    }

    // Override the password field for authentication
    public function getAuthPassword()
    {
        return $this->password;
    }
}
