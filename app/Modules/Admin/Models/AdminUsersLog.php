<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUsersLog extends Model
{
    protected $table = 'admin_users_log';

    protected $fillable = [
        'users_id',
        'otp',
        'log_in_time',
        'log_out_time',
    ];

    protected $casts = [
        'log_in_time' => 'datetime',
        'log_out_time' => 'datetime',
    ];

    public static function rules()
    {
        return [
            'email' => 'required|email|exists:admin_users,email',
            'otp'   => 'required|string|min:6|max:6',
        ];
    }
    public static function logOutRules()
    {
        return [
            'email' => 'required|email|exists:admin_users,email',
        ];
    }

    // Relationship
    public function user()
    {
        return $this->belongsTo(AdminUser::class, 'users_id');
    }
}
