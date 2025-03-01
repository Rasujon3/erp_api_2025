<?php

namespace App\Modules\Raks\Requests;

use App\Modules\Raks\Models\Rak;
use Illuminate\Foundation\Http\FormRequest;

class RakRequest extends FormRequest
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

        if ($routeName === 'raks.import') {
            //return Rak::importRules();
        }

        if ($routeName === 'raks.bulkUpdate') {
            return Rak::bulkRules();
        }

        if ($routeName === 'raks.list') {
            return Rak::listRules();
        }
        $raksId = $this->route('rak') ?: null;
        return Rak::rules($raksId);
    }
}
