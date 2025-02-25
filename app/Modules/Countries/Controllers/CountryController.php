<?php

namespace App\Modules\Countries\Controllers;

use App\Modules\Countries\Requests\CountryBulkRequest;
use Exception;
use Illuminate\Http\Request;
use App\Modules\Countries\Repositories\CountryRepository;
use App\Modules\Countries\Requests\CountryRequest;
use App\Modules\Countries\Queries\CountryDatatable;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        if (!$country) {
            return $this->sendError('Something went wrong!!! [CCS-01]', 500);
        }
        return $this->sendResponse($country, 'Country created successfully!');
    }

    // Update data
    public function update(CountryRequest $request, $country)
    {
        $data = $this->countryRepository->find($country);
        if (!$data) {
            return $this->sendError('Country not found');
        }
        $updated = $this->countryRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [CCU-01]', 500);
        }
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
            return $this->sendError('Something went wrong!!! [CC-01]',500);
        }
    }
    /**
     * Export a single country as PDF.
     */
    public function generateSinglePdf($country)
    {
        try {
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
        } catch (Exception $e) {
            Log::error('Error exporting countries as Single PDF: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('Something went wrong!!! [CC-02]', 500);
        }
    }
    /**
     * Export all countries as an Excel file.
     */
    public function generateExcel()
    {
        try {
            // Fetch all countries
            $countries = $this->countryRepository->getDataForExcel();

            // Prepare Excel data
            $data = [];
            $data[] = ['SL', 'Name', 'Code', 'Created At']; // Header row

            foreach ($countries as $index => $country) {
                $data[] = [
                    $index + 1,
                    $country->name,
                    $country->code,
                    $country->created_at
                ];
            }

            // Generate and return Excel file
            return $this->generateExcelFile($data, 'countries_export');
        } catch (Exception $e) {
            Log::error('Error exporting countries as Excel: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('Something went wrong!!! [CC-03]', 500);
        }
    }

    /**
     * Export a single country as an Excel file.
     */
    public function generateSingleExcel($id)
    {
        try {
            // Fetch country by ID
            $country = $this->countryRepository->getDataForSingleExcel($id);

            if (!$country) {
                return $this->sendError('Country not found');
            }

            // Prepare Excel data
            $data = [
                ['SL', 'Name', 'Code', 'Created At'], // Header row
                [1, $country->name, $country->code, $country->created_at]
            ];

            // Generate and return Excel file
            return $this->generateExcelFile($data, 'country_' . $id);
        } catch (Exception $e) {
            Log::error('Error exporting countries as Single Excel: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('Something went wrong!!! [CC-04]', 500);
        }
    }

    /**
     * Generate an Excel file and return it as a response.
     */
    private function generateExcelFile($data, $filename)
    {
        try {
            // Create a new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Populate the sheet with data
            foreach ($data as $rowIndex => $row) {
                foreach ($row as $colIndex => $cellValue) {
                    $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $cellValue);
                }
            }

            // Prepare response
            return new StreamedResponse(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.xlsx"',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        } catch (Exception $e) {
            Log::error('Error exporting countries as Generate Excel: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('Something went wrong!!! [CC-04]', 500);
        }
    }
    // bulk update
    public function bulkUpdate(CountryBulkRequest $request)
    {
        $bulkUpdate = $this->countryRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [CCBU-05]', 500);
        }
        return $this->sendResponse([],'Country Bulk updated successfully!');
    }
}
