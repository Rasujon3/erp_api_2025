<?php

namespace App\Modules\Settings\Controllers;

use App\Modules\Admin\Models\Country;
use App\Modules\Areas\Queries\AreaDatatable;
use App\Modules\Areas\Repositories\AreaRepository;
use App\Modules\Areas\Requests\AreaRequest;
use App\Modules\Branches\Queries\BranchDatatable;
use App\Modules\Branches\Repositories\BranchRepository;
use App\Modules\Branches\Requests\BranchRequest;
use App\Modules\City\Queries\CityDatatable;
use App\Modules\City\Repositories\CityRepository;
use App\Modules\City\Requests\CityRequest;
use App\Modules\Settings\Queries\SettingDatatable;
use App\Modules\Settings\Repositories\SettingRepository;
use App\Modules\Settings\Requests\SettingRequest;
use App\Modules\States\Models\State;
use App\Modules\States\Queries\StateDatatable;
use App\Modules\States\Repositories\StateRepository;
use App\Modules\States\Requests\StateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;

class SettingController extends AppBaseController
{
    protected SettingRepository $settingRepository;
    protected SettingDatatable $settingDatatable;

    public function __construct(SettingRepository $settingRepo, SettingDatatable $settingDatatable)
    {
        $this->settingRepository = $settingRepo;
        $this->settingDatatable = $settingDatatable;
    }

    // Fetch all states
    public function index(Request $request)
    {
        $groupName = $request->get('group', 'company_information');
        $settings = $this->settingRepository->getSyncList($groupName);
        $currencies = $this->settingRepository->getCurrencies();
        $dateFormats = $this->settingRepository->getDateFormats();
        $countries = $this->settingRepository->getCountries();
        $states = $this->settingRepository->getStates();
        $data = [
            'settings' => $settings,
            'currencies' => $currencies,
            'dateFormats' => $dateFormats,
            'countries' => $countries,
            'states' => $states
        ];
        return $this->sendResponse($data, 'Company retrieved successfully.');
    }

    // Update country
    public function update(SettingRequest $request)
    {
        Log::info('update', ['$request->all()' => $request->all()]);
        $this->settingRepository->updateSetting($request->all());
        return $this->sendResponse([], 'Company updated successfully!');
    }
}
