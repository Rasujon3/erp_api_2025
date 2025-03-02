<?php

namespace App\Modules\Allowances\Requests;

use App\Modules\Allowances\Models\Allowance;
use Illuminate\Foundation\Http\FormRequest;

class AllowanceRequest extends FormRequest
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

        if ($routeName === 'allowances.import') {
            //return Allowance::importRules();
        }

        if ($routeName === 'allowances.bulkUpdate') {
            return Allowance::bulkRules();
        }

        if ($routeName === 'allowances.list') {
            return Allowance::listRules();
        }
        // $cityId = $this->route('city') ?: null;
        return Allowance::rules();
    }
}
