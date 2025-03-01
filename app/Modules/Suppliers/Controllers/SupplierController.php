<?php

namespace App\Modules\Suppliers\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Customers\Repositories\CustomerRepository;
use App\Modules\Customers\Requests\CustomerRequest;
use App\Modules\Suppliers\Repositories\SupplierRepository;
use App\Modules\Suppliers\Requests\SupplierRequest;

class SupplierController extends AppBaseController
{
    protected SupplierRepository $supplierRepository;
    public function __construct(SupplierRepository $supplierRepo)
    {
        $this->supplierRepository = $supplierRepo;
    }
    // Fetch all data
    public function index(SupplierRequest $request)
    {
        $countries = $this->supplierRepository->all($request);
        return $this->sendResponse($countries, 'Suppliers retrieved successfully.');
    }
    // Get single details
    public function show($supplier)
    {
        $data = $this->supplierRepository->find($supplier);
        if (!$data) {
            return $this->sendError('Supplier not found');
        }
        return $this->sendResponse($data, 'Supplier retrieved successfully.');
    }
    // store data
    public function store(SupplierRequest $request)
    {
        $supplier = $this->supplierRepository->store($request->all());
        if (!$supplier) {
            return $this->sendError('Something went wrong!!! [SC-01]', 500);
        }
        return $this->sendResponse($supplier, 'Supplier created successfully!');
    }
    // Update data
    public function update(SupplierRequest $request, $supplier)
    {
        $data = $this->supplierRepository->find($supplier);
        if (!$data) {
            return $this->sendError('Supplier not found');
        }
        $updated = $this->supplierRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [SC-02]', 500);
        }
        return $this->sendResponse($supplier, 'Supplier updated successfully!');
    }
    // bulk update
    public function bulkUpdate(SupplierRequest $request)
    {
        $bulkUpdate = $this->supplierRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [SC-03]', 500);
        }
        return $this->sendResponse([],'Supplier Bulk updated successfully!');
    }
}
