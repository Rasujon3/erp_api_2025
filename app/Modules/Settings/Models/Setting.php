<?php

namespace App\Modules\Settings\Models;

use App\Modules\Admin\Models\Country;
use App\Modules\City\Models\City;
use App\Modules\Currencies\Models\Currency;
use App\Modules\States\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const PATH = 'settings';

    public const FAVICON = 'settings/favicon';

    const GROUP_GENERAL = 1;

    const COMPANY_INFORMATION = 2;

    const NOTE = 3;

    const GROUP_ARRAY = [
        'general' => self::GROUP_GENERAL,
        'company_information' => self::COMPANY_INFORMATION,
        'note' => self::NOTE,
    ];

    const CURRENCIES = [
        'eur' => 'Euro (EUR)',
        'aud' => 'Australia Dollar (AUD)',
        'inr' => 'India Rupee (INR)',
        'usd' => 'USA Dollar (USD)',
        'jpy' => 'Japanese Yen (JPY)',
        'gbp' => 'British Pound (GBP)',
        'cad' => 'Canadian Dollar (CAD)',
    ];
//    use HasFactory, SoftDeletes;
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function rules()
    {
        return [
            'group' => 'required|integer',
        ];
    }
}
