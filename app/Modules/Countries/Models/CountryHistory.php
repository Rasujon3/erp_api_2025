<?php

namespace App\Modules\Countries\Models;

use App\Modules\Admin\Models\AdminClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountryHistory extends Model
{
    use HasFactory;

    protected $table = 'country_histories';

    protected $fillable = [
        'id',
        'client_id',
        'action_date',
        'action_by',
        'action_type',
        'export_type',
        'export_pdf',
        'export_xls',
        'export_print',
    ];

    public function client() : belongsTo
    {
        return $this->belongsTo(AdminClient::class, 'client_id', 'id');
    }
}
