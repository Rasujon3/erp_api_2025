<?php

namespace App\Modules\Countries\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Countries\Models\Country;

class CountryBulkRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return Country::bulkRules();
    }
}
