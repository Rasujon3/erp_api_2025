<?php

namespace App\Modules\Admin\Repositories;

use App\Modules\Admin\Models\AdminUser;
use App\Modules\Admin\Models\AdminUsersLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Mail;

class AdminUsersLogRepository
{
    public function verifyOtp($id, $otp)
    {
        try {
            $adminUsersLog = AdminUsersLog::where('users_id', $id)->first();
            if ($adminUsersLog->otp === $otp) {
                return $adminUsersLog;
            }
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in verify OTP AdminUser : ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function findByEmail(string $email)
    {
        // only return id
        return AdminUser::where('email', $email)->first('id');
    }
    public function updateLog($id)
    {
        DB::beginTransaction();
        try {
            $adminUsersLog = AdminUsersLog::where('id', $id)->update(['log_in_time' => now()]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error('Error in update log AdminUser : ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function updateLogOut($email)
    {
        DB::beginTransaction();
        try {
            $data = $this->findByEmail($email);
            $adminUsersLog = AdminUsersLog::where('id', $data->id)->update(['log_out_time' => now()]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error('Error in logout AdminUser : ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
