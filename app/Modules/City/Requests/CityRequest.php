<?php

namespace App\Modules\City\Requests;

use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Admin\Models\Country; // Import the Currency model

class CityRequest extends FormRequest
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
        return City::rules();
    }
}
