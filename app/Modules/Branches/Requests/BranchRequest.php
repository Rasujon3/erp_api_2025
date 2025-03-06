<?php

namespace App\Modules\Branches\Requests;

use App\Http\Controllers\AppBaseController;
use App\Modules\Areas\Models\Area;
use App\Modules\Branches\Models\Branch;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Admin\Models\Country;
use Illuminate\Support\Facades\Log;

// Import the Currency model

class BranchRequest extends FormRequest
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

        if ($routeName === 'branches.import') {
            //return Branch::importRules();
        }

        if ($routeName === 'branches.bulkUpdate') {
            return Branch::bulkRules();
        }

        if ($routeName === 'branches.list') {
            return Branch::listRules();
        }
        $branchId = $this->route('branch') ?: null;
        return Branch::rules($branchId);
    }
}
