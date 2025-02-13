<?php

namespace App\Modules\Sample\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SampleReceiving extends Model
{
//    use HasFactory, SoftDeletes;
    use HasFactory;

    protected $table = 'sample_receiving';

    protected $fillable = [
        'date',
        'time',
        'section',
        'client_name',
        'client_reference',
        'type_of_sample',
        'required_tests',
        'number_of_sample',
        'delivered_by',
        'received_by'
    ];

    public static function rules($sampleReceivingId = null)
    {
        return [
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
            'section' => 'required|exists:sample_categories,id',
            'client_name' => 'required|string|max:191',
            'client_reference' => 'required|string|max:191|unique:sample_receiving,client_reference,' . $sampleReceivingId . ',id',
            'type_of_sample' => 'required|string|max:191',
            'required_tests' => 'required|string|max:191',
            'number_of_sample' => 'required|string|max:191',
            'delivered_by' => 'required|exists:employees,id',
            'received_by' => 'required|exists:employees,id',
        ];
    }
    public function category(){
        return $this->belongsTo(SampleCategory::class,'section');
    }
}
