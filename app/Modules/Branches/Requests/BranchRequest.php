<?php

namespace App\Modules\Branches\Requests;

use App\Http\Controllers\AppBaseController;
use App\Modules\Areas\Models\Area;
use App\Modules\Branches\Models\Branch;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Admin\Models\Country;
use Illuminate\Support\Facades\Log;

// Import the Currency model

class BranchRequest extends FormRequest
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
        $branchId = (int) $this->route('branch') ?: null;
//        $branchId = $this->route('branch') ?: null;
        $name = $this->input('name');
        Log::info('$branchId ID from route:', ['areaId' => $branchId]); // Log the value
        Log::info('$branchId ID from name:', ['areaId' => $name]); // Log the value
        return Branch::rules($branchId);
//        return Area::rules($name);
    }
}
