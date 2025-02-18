<?php

namespace App\Modules\Leaves\Requests;

use App\Modules\Departments\Models\Department;
use App\Modules\Leaves\Models\Leave;
use Illuminate\Foundation\Http\FormRequest;

class LeaveRequest extends FormRequest
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
        $leaveId = $this->route('leave') ?: null;
        return Leave::rules($leaveId);
    }
}
