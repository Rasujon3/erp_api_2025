<?php

namespace App\Modules\Customers\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Customers\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CustomerRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $customers = Customer::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $customers->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $customers->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $customers->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $customers->whereNotNull('deleted_at')->count();
        $totalUpdated = $customers->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total Count is without soft-deleted
        $totalCurrencies = $customers->whereNull('deleted_at')->count();

        return [
            'totalCurrencies' => $totalCurrencies,
            'totalDraft' => $totalDraft,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
            'list' => $list,
        ];
    }

    public function list($request)
    {
        $query = Customer::withTrashed(); // Load all records without soft-deleted

        if ($request->has('draft')) {
            $query->where('draft', $request->input('draft'));
        }
        if ($request->has('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }
        if ($request->has('is_default')) {
            $query->where('is_default', $request->input('is_default'));
        }
        if ($request->has('is_deleted')) {
            if ($request->input('is_deleted') == 1) {
                $query->whereNotNull('deleted_at');
            } else {
                $query->whereNull('deleted_at');
            }
        } else {
            $query->whereNull('deleted_at');
        }
        if ($request->has('is_updated')) {
            if ($request->input('is_updated') == 1) {
                $query->whereNotNull('updated_at');
            } else {
                $query->whereNull('updated_at');
            }
        }

        $list = $query->get();
        return $list;
    }

    public function store(array $data): ?Customer
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            /*
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }
            */

            // Handle file upload for 'flag'
            if (isset($data['customer_logo']) && $data['customer_logo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['customer_logo'] = $this->storeFile($data['customer_logo']);
            }

            // Create the country record in the database
            $customer = Customer::create($data);

            // Log activity
            ActivityLogger::log('Customer Add', 'Customer', 'Customer', $customer->id, [
                'name' => $customer->name ?? '',
                'code' => $customer->code ?? ''
            ]);

            DB::commit();

            return $customer;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Customer: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Customer $customer, array $data): ?Customer
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            /*
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }
            */

            // Handle file upload for 'flag'
            if (isset($data['customer_logo']) && $data['customer_logo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['customer_logo'] = $this->updateFile($data['customer_logo'], $customer);
            }

            // Perform the update
            $customer->update($data);

            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($customer);
            } else {
                // Log activity for update
                ActivityLogger::log('Customer Updated', 'Customer', 'Customer', $customer->id, [
                    'name' => $customer->name
                ]);
            }

            DB::commit();
            return $customer;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Customer: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Customer $customer): bool
    {
        try {
            DB::beginTransaction();
            // Attempt to delete flag image if it exists
            $deleteOldFile = $this->deleteOldFile($customer);
            // if delete old file, then update country table on flag column is null
            if ($deleteOldFile) {
                $customer->update(['customer_logo' => null]);
            }
            // Perform soft delete
            $deleted = $customer->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Customer Deleted', 'Customer', 'Customer', $customer->id, [
                'name' => $customer->name ?? '',
                'code' => $customer->code ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Customer: ' , [
                'country_id' => $customer->id,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function find($id)
    {
        return Customer::find($id);
    }
    public function storeFile($file)
    {
        // Define the directory path
        $filePath = 'files/images/customer';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('customer_logo_', true) . '.' . $file->getClientOriginalExtension();

        // Move the file to the destination directory
        $file->move($directory, $fileName);

        // path & file name in the database
        $path = $filePath . '/' . $fileName;
        return $path;
    }
    public function updateFile($file, $data)
    {
        // Define the directory path
        $filePath = 'files/images/customer';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('customer_logo_', true) . '.' . $file->getClientOriginalExtension();

        // Delete the old file if it exists
        $this->deleteOldFile($data);

        // Move the new file to the destination directory
        $file->move($directory, $fileName);

        // Store path & file name in the database
        $path = $filePath . '/' . $fileName;
        return $path;
    }
    public function deleteOldFile($data)
    {
        if (!empty($data->customer_logo)) {
            $oldFilePath = public_path($data->customer_logo); // Use without prepending $filePath
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath); // Delete the old file
                return true;
            } else {
                Log::warning('Old file not found for deletion', ['path' => $oldFilePath]);
                return false;
            }
        }
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->customers as $data) {
                $customer = Customer::find($data['id']);

                if (!$customer) {
                    continue; // Skip if not found
                }

                // Update details
                $customer->update([
                    'company_name' => $data['company_name'] ?? $customer->company_name,
                    'short_name' => $data['short_name'] ?? $customer->short_name,
                    'code' => $data['code'] ?? $customer->code,
                    'vendor_code' => $data['vendor_code'] ?? $customer->vendor_code,
                    'vat_number' => $data['vat_number'] ?? $customer->vat_number,
                    'phone' => $data['phone'] ?? $customer->phone,
                    'fax' => $data['fax'] ?? $customer->fax,
                    'mobile' => $data['mobile'] ?? $customer->mobile,
                    'whatsapp' => $data['whatsapp'] ?? $customer->whatsapp,
                    'email' => $data['email'] ?? $customer->email,
                    'address' => $data['address'] ?? $customer->address,
                    'website' => $data['website'] ?? $customer->website,
                    'currency' => $data['currency'] ?? $customer->currency,
                    'country' => $data['country'] ?? $customer->country,
                    'default_language' => $data['default_language'] ?? $customer->default_language,
                    'inactive' => $data['inactive'] ?? $customer->default_language,
                    'location_url' => $data['location_url'] ?? $customer->location_url,
                ]);

                // Handle flag image upload if provided
                /*
                if (isset($data['flag']) && $request->hasFile("countries.{$data['id']}.flag")) {
                    $flagPath = $request->file("countries.{$data['id']}.flag")->store('flags', 'public');
                    $customer->update(['flag' => $flagPath]);
                }
                */
                // Log activity for update
                ActivityLogger::log('Customer Updated', 'Customer', 'Customer', $customer->id, [
                    'name' => $customer->name ?? '',
                    'code' => $customer->code ?? '',
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error bulk updating country: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
