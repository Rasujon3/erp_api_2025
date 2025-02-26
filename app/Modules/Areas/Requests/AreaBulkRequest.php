<?php

namespace App\Modules\Areas\Requests;

use App\Modules\Areas\Models\Area;
use Illuminate\Foundation\Http\FormRequest;

class AreaBulkRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return Area::bulkRules();
    }
}
