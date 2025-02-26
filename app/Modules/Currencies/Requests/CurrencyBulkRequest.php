<?php

namespace App\Modules\Currencies\Requests;

use App\Modules\Currencies\Models\Currency;
use Illuminate\Foundation\Http\FormRequest;

class CurrencyBulkRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return Currency::bulkRules();
    }
}
