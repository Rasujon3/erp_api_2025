<?php

namespace App\Modules\SubCategory\Requests;

use App\Modules\SubCategory\Models\SubCategory;
use Illuminate\Foundation\Http\FormRequest;

class SubCategoryRequest extends FormRequest
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
        $subCategoryId = $this->route('subCategory') ?: null;
        return SubCategory::rules($subCategoryId);
    }
}
