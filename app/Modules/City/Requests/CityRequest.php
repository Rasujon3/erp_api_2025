<?php

namespace App\Modules\City\Requests;

use App\Modules\City\Models\City;
use Illuminate\Foundation\Http\FormRequest;

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
        // Get the route name and apply null-safe operator
        $routeName = $this->route()?->getName();

        if ($routeName === 'cities.import') {
            //return City::importRules();
        }

        if ($routeName === 'cities.bulkUpdate') {
            return City::bulkRules();
        }

        if ($routeName === 'cities.list') {
            return City::listRules();
        }
        $cityId = $this->route('city') ?: null;
        return City::rules($cityId);
    }
}
