<?php

namespace App\Modules\LeaveApplications\Requests;

use App\Modules\Departments\Models\Department;
use App\Modules\LeaveApplications\Models\LeaveApplication;
use App\Modules\Leaves\Models\Leave;
use Illuminate\Foundation\Http\FormRequest;

class LeaveApplicationRequest extends FormRequest
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
        return LeaveApplication::rules();
    }
}
