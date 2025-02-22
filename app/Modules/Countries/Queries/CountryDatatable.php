<?php

namespace App\Modules\Countries\Queries;

use App\Modules\Countries\Models\Country;
use App\Modules\Countries\Repositories\CountryRepository;



class CountryDatatable
{
    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }


    public static function getDataForDatatable($request)
    {
        // Total count without any filters
        $totalCount = Country::count();

        // Build the query with filters
        $query = Country::select(['id', 'code', 'name', 'is_active', 'draft', 'is_default', 'flag']);

        // Global search
        if (!empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('code', 'like', "%{$searchValue}%");
            });
        }

        // Column-specific search
        foreach ($request->input('columns', []) as $column) {
            if (!empty($column['search']['value'])) {
                $query->where($column['data'], 'like', "%{$column['search']['value']}%");
            }
        }

        // Filtered count
        $filteredCount = $query->count();

        // Sorting
        foreach ($request->input('order', []) as $order) {
            $columnName = $request->input("columns.{$order['column']}.data");
            $query->orderBy($columnName, $order['dir']);
        }

        // Pagination using offset and limit
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $data = $query->offset($start)->limit($length)->get();

        // Return in DataTables format
        return [
            'draw' => $request->input('draw', 1),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ];
    }
}
