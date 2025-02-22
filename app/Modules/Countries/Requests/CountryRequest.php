<?php

namespace App\Modules\Countries\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Countries\Models\Country;

class CountryRequest extends FormRequest
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
        $countryId = $this->route('country') ?: null;
        return Country::rules($countryId);
    }
}
