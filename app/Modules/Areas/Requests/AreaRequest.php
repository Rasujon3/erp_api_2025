<?php

namespace App\Modules\Areas\Requests;

use App\Http\Controllers\AppBaseController;
use App\Modules\Areas\Models\Area;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Admin\Models\Country;
use Illuminate\Support\Facades\Log;

// Import the Currency model

class AreaRequest extends FormRequest
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
        $areaId = (int) $this->route('area') ?: null;
        $name = $this->input('name');
        Log::info('Area ID from route:', ['areaId' => $areaId]); // Log the value
        Log::info('Area ID from name:', ['areaId' => $name]); // Log the value
        return Area::rules($areaId);
//        return Area::rules($name);
    }
}
