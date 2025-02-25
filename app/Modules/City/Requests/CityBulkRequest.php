<?php

namespace App\Modules\City\Requests;

use App\Modules\City\Models\City;
use Illuminate\Foundation\Http\FormRequest;

class CityBulkRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return City::bulkRules();
    }
}
