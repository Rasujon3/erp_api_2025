<?php

namespace App\Modules\Sample\Queries;

use App\Modules\City\Models\City;
use App\Modules\Items\Models\ItemGroup;
use App\Modules\Sample\Models\SampleReceiving;
use App\Modules\States\Models\State;
use App\Modules\States\Repositories\StateRepository;
use App\Modules\Stores\Models\Store;
use App\Modules\TaxRates\Models\TaxRate;
use App\Modules\TaxRates\Repositories\TaxRateRepository;

class SampleReceiveDatatable
{
    protected $taxRateRepository;

    public function __construct(TaxRateRepository $taxRateRepository)
    {
        $this->taxRateRepository = $taxRateRepository;
    }
    /**
     * Return data for DataTables
     *
     * @param  Request  $request
     * @return array
     */
    public static function getDataForDatatable($request)
    {
        $query = SampleReceiving::leftJoin('sample_categories', 'sample_receiving.section', '=', 'sample_categories.id')
            ->leftJoin('employees as delivered_by_name', 'sample_receiving.delivered_by', '=', 'delivered_by_name.id')
            ->leftJoin('employees as received_by_name', 'sample_receiving.received_by', '=', 'received_by_name.id')
            ->select('sample_receiving.*', 'delivered_by_name.name as delivered_by_name',
                'received_by_name.name as received_by_name', 'sample_categories.name as sample_category_name');

        // Check if global search is enabled
        if (!empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            // Perform global search on name and code
            $query->where(function ($q) use ($searchValue) {
                $q->where('client_name', 'like', "%{$searchValue}%");
            });
        }

        // Check if column-specific search is enabled
        foreach ($request->input('columns', []) as $column) {
            if (!empty($column['search']['value'])) {
                // Perform search on the specified column
                $query->where($column['data'], 'like', "%{$column['search']['value']}%");
            }
        }

        // Check if sorting is enabled
        foreach ($request->input('order', []) as $order) {
            $columnName = $request->input("columns.{$order['column']}.data");
            // Perform sorting on the specified column
            $query->orderBy($columnName, $order['dir']);
        }

        // Get the data
        $data = $query->paginate(
            $request->input('length', 10), // Number of records to show
            ['*'], // Columns to return
            'start', // Custom pagination parameter
            $request->input('start', 0) / $request->input('length', 10) + 1 // Current page
        );

        return $data;
    }
}
