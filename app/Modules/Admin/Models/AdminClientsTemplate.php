<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminClientsTemplate extends Model
{
    protected $table = 'admin_clients_template';

    protected $fillable = [
        'code',
        'name',
    ];
}
