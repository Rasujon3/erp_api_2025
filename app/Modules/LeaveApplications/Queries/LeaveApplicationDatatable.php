<?php

namespace App\Modules\LeaveApplications\Queries;

use App\Modules\LeaveApplications\Models\LeaveApplication;
use App\Modules\Leaves\Models\Leave;
use App\Modules\TaxRates\Repositories\TaxRateRepository;

class LeaveApplicationDatatable
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
        $query = LeaveApplication::leftJoin('leaves', 'leaves.id', '=', 'leave_applications.leave_id')
            ->leftJoin('employees', 'employees.id', '=', 'leave_applications.employee_id')
            ->select('leave_applications.*', 'leaves.name as leave_name', 'employees.name as employee_name');

        // Check if global search is enabled
        if (!empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            // Perform global search on name and code
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%");
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
