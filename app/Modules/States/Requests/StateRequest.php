<?php

namespace App\Modules\States\Requests;

use App\Modules\States\Models\State;
use Illuminate\Foundation\Http\FormRequest;

class StateRequest extends FormRequest
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

        if ($routeName === 'states.import') {
            //return State::importRules();
        }

        if ($routeName === 'states.bulkUpdate') {
            return State::bulkRules();
        }

        if ($routeName === 'states.list') {
            return State::listRules();
        }
        $stateId = $this->route('state') ?: null;
        return State::rules($stateId);
    }
}
