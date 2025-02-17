<?php

namespace App\Modules\Departments\Requests;

use App\Modules\Departments\Models\Department;
use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
        $departmentId = $this->route('department') ?: null;
        return Department::rules($departmentId);
    }
}
