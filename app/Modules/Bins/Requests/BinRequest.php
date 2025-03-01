<?php

namespace App\Modules\Bins\Requests;

use App\Modules\Bins\Models\Bin;
use Illuminate\Foundation\Http\FormRequest;

class BinRequest extends FormRequest
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

        if ($routeName === 'bins.import') {
            //return Bin::importRules();
        }

        if ($routeName === 'bins.bulkUpdate') {
            return Bin::bulkRules();
        }

        if ($routeName === 'bins.list') {
            return Bin::listRules();
        }
        $binsId = $this->route('bin') ?: null;
        return Bin::rules($binsId);
    }
}
