<?php

namespace App\Modules\Designations\Requests;

use App\Modules\Designations\Models\Designation;
use Illuminate\Foundation\Http\FormRequest;

class DesignationRequest extends FormRequest
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

        if ($routeName === 'designations.import') {
            //return Designation::importRules();
        }

        if ($routeName === 'designations.bulkUpdate') {
            return Designation::bulkRules();
        }

        if ($routeName === 'designations.list') {
            return Designation::listRules();
        }
        // $cityId = $this->route('city') ?: null;
        return Designation::rules();
    }
}
