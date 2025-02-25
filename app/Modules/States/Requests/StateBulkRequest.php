<?php

namespace App\Modules\States\Requests;

use App\Modules\States\Models\State;
use Illuminate\Foundation\Http\FormRequest;

class StateBulkRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return State::bulkRules();
    }
}
