<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Admin\Repositories\AdminUserRepository;
use App\Modules\Admin\Repositories\AdminUsersLogRepository;
use App\Modules\Admin\Requests\AdminUserRequest;
use App\Modules\Admin\Requests\AdminUsersLogRequest;

class AdminUsersLogController extends AppBaseController
{
    protected AdminUsersLogRepository $adminUsersLogRepository;
    public function __construct(AdminUsersLogRepository $adminUsersLogRepo)
    {
        $this->adminUsersLogRepository = $adminUsersLogRepo;
    }

    // Store data
    public function verifyOtp(AdminUsersLogRequest $request)
    {
        // Extract email and OTP from the request
        $email = $request->input('email');
        $otp = $request->input('otp');

        // Find the log entry for the given email
        $logEntry = $this->adminUsersLogRepository->findByEmail($email);

        if (!$logEntry) {
            return $this->sendError('No log entry found for the provided email.');
        }
        $verifyOtp = $this->adminUsersLogRepository->verifyOtp($logEntry->id, $otp);
        if (!$verifyOtp) {
            return $this->sendError('OTP not matched', 500);
        }
        $update = $this->adminUsersLogRepository->updateLog($verifyOtp->id);
        if (!$update) {
            return $this->sendError('Somethings went wrong!!.', 500);
        }
        return $this->sendResponse([], 'Admin User logged in successfully!');
    }
    // Store data
    public function logout(AdminUsersLogRequest $request)
    {
        $email = $request->input('email');
        $update = $this->adminUsersLogRepository->updateLogOut($email);
        if (!$update) {
            return $this->sendError('Somethings went wrong!!.', 500);
        }
        return $this->sendResponse([], 'Admin User logged out successfully!');
    }
}
