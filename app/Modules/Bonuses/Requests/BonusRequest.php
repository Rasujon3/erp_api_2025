<?php

namespace App\Modules\Bonuses\Requests;

use App\Modules\Bonuses\Models\Bonus;
use Illuminate\Foundation\Http\FormRequest;

class BonusRequest extends FormRequest
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

        if ($routeName === 'bonuses.import') {
            //return Bonus::importRules();
        }

        if ($routeName === 'bonuses.bulkUpdate') {
            return Bonus::bulkRules();
        }

        if ($routeName === 'bonuses.list') {
            return Bonus::listRules();
        }
        // $cityId = $this->route('city') ?: null;
        return Bonus::rules();
    }
}
