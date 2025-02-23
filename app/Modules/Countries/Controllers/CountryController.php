<?php

namespace App\Modules\Countries\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Modules\Countries\Repositories\CountryRepository;
use App\Modules\Countries\Requests\CountryRequest;
use App\Modules\Countries\Queries\CountryDatatable;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;

class CountryController extends AppBaseController
{
    protected $countryRepository;
    protected $countryDatatable;

    public function __construct(CountryRepository $countryRepo, CountryDatatable $countryDatatable)
    {
        $this->countryRepository = $countryRepo;
        $this->countryDatatable = $countryDatatable;
    }
    // Fetch all data
    public function index()
    {
        $countries = $this->countryRepository->all();
        return $this->sendResponse($countries, 'Countries retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->countryRepository->getSummaryData();
        return $this->sendResponse($summary, 'Country summary retrieved successfully.');
    }
    // Get DataTable records
    public function getCountriesDataTable(Request $request)
    {
        $data = CountryDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Country DataTable data retrieved successfully.');
    }
    // Get Map Data
    public function getMapData()
    {
        $data = $this->countryRepository->getMapData();
        return $this->sendResponse($data, 'Country map data retrieved successfully.');
    }
    // Get single details
    public function show($country)
    {
        $data = $this->countryRepository->find($country);
        if (!$data) {
            return $this->sendError('Country not found');
        }
        return $this->sendResponse($data, 'Country retrieved successfully.');
    }
    public function store(CountryRequest $request)
    {
        $country = $this->countryRepository->store($request->all());
        return $this->sendResponse($country, 'Country created successfully!');
    }

    // Update data
    public function update(CountryRequest $request, $country)
    {
        $data = $this->countryRepository->find($country);
        if (!$data) {
            return $this->sendError('Country not found');
        }
        $this->countryRepository->update($data, $request->all());
        return $this->sendResponse($country, 'Country updated successfully!');
    }

    // Delete data
    public function destroy($country)
    {
        $data = $this->countryRepository->find($country);
        if (!$data) {
            return $this->sendError('Country not found');
        }
        $this->countryRepository->delete($data);
        return $this->sendSuccess('Country deleted successfully!');
    }
    /**
     * Export all countries as PDF.
     */
    public function generatePdf()
    {
        try {
            $countries = $this->countryRepository->getMapData();

            $html = View::make('countries::pdf.countries', compact('countries'))->render();

            $mpdf = new Mpdf();
            $mpdf->WriteHTML($html);

            $pdfFileName = 'all_countries.pdf';
            return response()->streamDownload(
                fn() => print($mpdf->Output('', 'I')), // I = Inline, D = Download, S = String, F = File
                $pdfFileName
            );
        } catch (Exception $e) {
            Log::error('Error exporting countries as PDF: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    /**
     * Export a single country as PDF.
     */
    public function generateSinglePdf($country)
    {
        $data = $this->countryRepository->find($country);
        if (!$data) {
            return $this->sendError('Country not found');
        }

        $html = View::make('countries::pdf.single_country', compact('data'))->render();

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);

        $pdfFileName = 'country_' . $data->code . '.pdf';
        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'I')), // I = Inline, D = Download, S = String, F = File
            $pdfFileName
        );
    }
}
