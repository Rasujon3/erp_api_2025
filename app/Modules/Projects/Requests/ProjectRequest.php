<?php

namespace App\Modules\Projects\Requests;

use App\Modules\Brands\Models\Brand;
use App\Modules\City\Models\City;
use App\Modules\Colors\Models\Color;
use App\Modules\Groups\Models\Group;
use App\Modules\Items\Models\ItemGroup;
use App\Modules\ProductUnits\Models\ProductUnit;
use App\Modules\Projects\Models\Project;
use App\Modules\Sample\Models\SampleCategory;
use App\Modules\Sizes\Models\Size;
use App\Modules\States\Models\State;
use App\Modules\Stores\Models\Store;
use App\Modules\Tags\Models\Tag;
use App\Modules\TaxRates\Models\TaxRate;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Admin\Models\Country; // Import the Currency model

class ProjectRequest extends FormRequest
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
        $projectId = $this->route('project') ?: null;
        return Project::rules($projectId);
    }
}
