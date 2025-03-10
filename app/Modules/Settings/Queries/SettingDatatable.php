<?php

namespace App\Modules\Settings\Queries;

use App\Modules\Areas\Models\Area;
use App\Modules\Branches\Models\Branch;
use App\Modules\Branches\Repositories\BranchRepository;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use App\Modules\States\Repositories\StateRepository;

class SettingDatatable
{
    protected BranchRepository $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }
    /**
     * Return data for DataTables
     *
     * @param  Request  $request
     * @return array
     */
    public static function getDataForDatatable($request)
    {
        $query = Branch::leftJoin('countries', 'branches.country_id', '=', 'countries.id')
                    ->leftJoin('currencies', 'branches.currency_id', '=', 'currencies.id')
                    ->select(['branches.id as id', 'branches.*', 'currencies.name as currency_name', 'countries.name as country_name']);

        // Check if global search is enabled
        if (!empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            // Perform global search on name and code
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('company_name', 'like', "%{$searchValue}%")
                    ->orWhere('website', 'like', "%{$searchValue}%");
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
