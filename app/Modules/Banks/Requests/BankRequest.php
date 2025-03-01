<?php

namespace App\Modules\Banks\Requests;

use App\Modules\Banks\Models\Bank;
use Illuminate\Foundation\Http\FormRequest;

class BankRequest extends FormRequest
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

        if ($routeName === 'banks.import') {
            //return Bank::importRules();
        }

        if ($routeName === 'banks.bulkUpdate') {
            return Bank::bulkRules();
        }

        if ($routeName === 'banks.list') {
            return Bank::listRules();
        }

        $bankId = $this->route('bank') ?: null;
        return Bank::rules($bankId);
    }
}
