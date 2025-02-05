<?php

namespace App\Modules\Admin\Queries;

use Yajra\DataTables\Facades\DataTables;
use App\Modules\Admin\Models\Country;
use Carbon\Carbon;
use App\Modules\Admin\Repositories\CountryRepository;



class CountryDatatable
{
    protected $countryRepository;
    protected $countryDatatable;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }


    public static function getDataForDatatable($request)
    {
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

        // Sorting
        foreach ($request->input('order', []) as $order) {
            $columnName = $request->input("columns.{$order['column']}.data");
            $query->orderBy($columnName, $order['dir']);
        }

        // Pagination
        $data = $query->paginate($request->input('length', 10), ['*'], 'start', $request->input('start', 0) / $request->input('length', 10) + 1);

        return $data;
    }
}
