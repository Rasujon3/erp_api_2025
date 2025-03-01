<?php

namespace App\Modules\Suppliers\Requests;

use App\Modules\Customers\Models\Customer;
use App\Modules\Suppliers\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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

        if ($routeName === 'suppliers.import') {
            //return Supplier::importRules();
        }

        if ($routeName === 'suppliers.bulkUpdate') {
            return Supplier::bulkRules();
        }

        if ($routeName === 'suppliers.list') {
            return Supplier::listRules();
        }

        $supplierId = $this->route('supplier') ?: null;
        return Supplier::rules($supplierId);
    }
}
