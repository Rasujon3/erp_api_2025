<?php

namespace App\Modules\Admin\Requests;

use App\Modules\Admin\Models\AdminUser;
use App\Modules\Admin\Models\AdminUsersLog;
use Illuminate\Foundation\Http\FormRequest;

class AdminUsersLogRequest extends FormRequest
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

        if ($routeName === 'admin_users.logout') {
            return AdminUsersLog::logOutRules();
        }
        return AdminUsersLog::rules();
    }
}
