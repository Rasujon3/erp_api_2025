<?php

namespace App\Modules\Currencies\Requests;

use App\Http\Controllers\AppBaseController;
use App\Modules\Areas\Models\Area;
use App\Modules\City\Models\City;
use App\Modules\Currencies\Models\Currency;
use App\Modules\States\Models\State;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Admin\Models\Country;
use Illuminate\Support\Facades\Log;

// Import the Currency model

class CurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // You can add any authorization logic here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
//        $areaId = $this->route('area') ? $this->route('area')->id : null;
        $currencyId = (int) $this->route('currency') ?: null;
        $name = $this->input('name');
        Log::info('Area ID from route:', ['$currencyId' => $currencyId]); // Log the value
        Log::info('Area ID from name:', ['$currencyId' => $currencyId]); // Log the value
        return Currency::rules($currencyId);
//        return Area::rules($name);
    }
}
