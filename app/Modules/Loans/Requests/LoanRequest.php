<?php

namespace App\Modules\Loans\Requests;

use App\Modules\Loans\Models\Loan;
use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
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

        if ($routeName === 'loans.import') {
            //return Loan::importRules();
        }

        if ($routeName === 'loans.bulkUpdate') {
            return Loan::bulkRules();
        }

        if ($routeName === 'loans.list') {
            return Loan::listRules();
        }
        // $cityId = $this->route('city') ?: null;
        return Loan::rules();
    }
}
