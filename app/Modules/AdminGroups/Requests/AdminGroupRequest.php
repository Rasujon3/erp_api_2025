<?php

namespace App\Modules\AdminGroups\Requests;

use App\Modules\AdminGroups\Models\AdminGroup;
use Illuminate\Foundation\Http\FormRequest;

class AdminGroupRequest extends FormRequest
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

        if ($routeName === 'admin_groups.import') {
            //return AdminGroup::importRules();
        }

        if ($routeName === 'admin_groups.bulkUpdate') {
            return AdminGroup::bulkRules();
        }

        if ($routeName === 'admin_groups.list') {
            return AdminGroup::listRules();
        }

        $adminGroupId = $this->route('adminGroup') ?: null;
        return AdminGroup::rules($adminGroupId);
    }
}
