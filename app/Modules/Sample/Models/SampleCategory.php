<?php

namespace App\Modules\Sample\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\States\Models\State;
use App\Modules\TaxRates\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SampleCategory extends Model
{
//    use HasFactory, SoftDeletes;
    use HasFactory;

    protected $table = 'sample_categories';

    protected $fillable = [
        'name',
        'description'
    ];

    public static function rules($sampleCategoryId = null)
    {
        return [
            'name' => 'required|unique:sample_categories,name,' . $sampleCategoryId . ',id',
        ];
    }
    public function sampleReceiving() : HasMany
    {
        return $this->hasMany(SampleReceiving::class,'sample');
    }
}
