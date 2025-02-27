<?php

namespace App\Modules\Currencies\Requests;

use App\Modules\Currencies\Models\Currency;
use Illuminate\Foundation\Http\FormRequest;

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
        // Get the route name and apply null-safe operator
        $routeName = $this->route()?->getName();

        if ($routeName === 'currencies.import') {
            //return Currency::importRules();
        }

        if ($routeName === 'currencies.bulkUpdate') {
            return Currency::bulkRules();
        }

        if ($routeName === 'currencies.list') {
            return Currency::listRules();
        }
        $currencyId = $this->route('currency') ?: null;
        return Currency::rules($currencyId);
    }
}
