<?php

namespace App\Modules\Customers\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Customers\Repositories\CustomerRepository;
use App\Modules\Customers\Requests\CustomerRequest;

class CustomerController extends AppBaseController
{
    protected CustomerRepository $customerRepository;
    public function __construct(CustomerRepository $customerRepo)
    {
        $this->customerRepository = $customerRepo;
    }
    // Fetch all data
    public function index(CustomerRequest $request)
    {
        $countries = $this->customerRepository->all($request);
        return $this->sendResponse($countries, 'Customers retrieved successfully.');
    }
    // Get single details
    public function show($customer)
    {
        $data = $this->customerRepository->find($customer);
        if (!$data) {
            return $this->sendError('Customer not found');
        }
        return $this->sendResponse($data, 'Customer retrieved successfully.');
    }
    // store data
    public function store(CustomerRequest $request)
    {
        $customer = $this->customerRepository->store($request->all());
        if (!$customer) {
            return $this->sendError('Something went wrong!!! [CCRS-01]', 500);
        }
        return $this->sendResponse($customer, 'Customer created successfully!');
    }
    // Update data
    public function update(CustomerRequest $request, $customer)
    {
        $data = $this->customerRepository->find($customer);
        if (!$data) {
            return $this->sendError('Customer not found');
        }
        $updated = $this->customerRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [CCRS-02]', 500);
        }
        return $this->sendResponse($customer, 'Customer updated successfully!');
    }
    // bulk update
    public function bulkUpdate(CustomerRequest $request)
    {
        $bulkUpdate = $this->customerRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [CCRS-03]', 500);
        }
        return $this->sendResponse([],'Customer Bulk updated successfully!');
    }
}
