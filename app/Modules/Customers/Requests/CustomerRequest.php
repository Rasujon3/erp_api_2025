<?php

namespace App\Modules\Customers\Requests;

use App\Modules\Customers\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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

        if ($routeName === 'customers.import') {
            //return Currency::importRules();
        }

        if ($routeName === 'customers.bulkUpdate') {
            return Customer::bulkRules();
        }

        if ($routeName === 'customers.list') {
            return Customer::listRules();
        }

        $customerId = $this->route('customer') ?: null;
        return Customer::rules($customerId);
    }
}
