<?php

namespace App\Modules\Settings\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\Branches\Models\Branch;
use App\Modules\City\Models\City;
use App\Modules\Currencies\Models\Currency;
use App\Modules\Settings\Models\Setting;
use App\Modules\States\Models\State;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SettingRepository
{
    public function getDateFormats()
    {
        return  $dateFormats= [
            'd-m-Y' => 'DD-MMM-YYYY',
            'd/m/Y' => 'DD/MM/YYYY',
            'm/d/Y' => 'MM/DD/YYYY',
            'Y-m-d' => 'YYYY-MM-DD',
            'd M, Y' => 'DD MMM, YYYY',
            'M d, Y' => 'MMM DD, YYYY',
            'Y M d' => 'YYYY MMM DD',
        ];
    }
    public function model()
    {
        return Setting::class;
    }
    public function getSyncList($groupName)
    {
        return Setting::pluck('value', 'key')->toArray();
    }
    public function getCurrencies()
    {
        return Currency::pluck('name', 'id');
    }
    public function getCountries()
    {
        return Country::pluck('name', 'id');
    }
    public function getStates()
    {
        return State::get();
    }
    public function updateSetting($input)
    {
        $inputArr = Arr::except($input, ['_token']);
        $inputArr['vat_status']=isset($inputArr['vat_status'])?1:0;

        // dd($input);
        foreach ($inputArr as $key => $value) {

            /** @var Setting $setting */
            $setting = Setting::where('key', $key)->first();

            if (! $setting) {
                continue;
            }

            if (in_array($key, ['logo', 'favicon']) && ! empty($value)) {
                $this->fileUpload($setting, $value);
                continue;
            }

            $setting->update(['value' => $value]);
        }

        return true;
    }
    public function fileUpload($setting, $file)
    {
        Log::info('File Upload', ['file' => $file]);
        Log::info('Settings', ['setting' => $setting]);
        $setting->clearMediaCollection(Setting::PATH);
        $disk = config('app.media_disc', 'public'); // Default to 'public'
        $media = $setting->addMedia($file)->toMediaCollection(Setting::PATH, $disk);

        $setting->update(['value' => $media->getFullUrl()]);

        return $setting;
    }
}
