<?php

namespace App\Modules\Category\Requests;

use App\Modules\Category\Models\Category;
use App\Modules\Departments\Models\Department;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $categoryId = $this->route('category') ?: null;
        return Category::rules($categoryId);
    }
}
