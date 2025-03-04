<?php

namespace App\Modules\Admin\Repositories;

use App\Modules\Admin\Models\AdminUser;
use App\Modules\Admin\Models\AdminUsersLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Mail;

class AdminUserRepository
{
    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            // Hash passwords before saving
            $data['password'] = Hash::make($data['password']);
            $data['confirm_password'] = Hash::make($data['confirm_password']);

            // Create the admin user
            $adminUser = AdminUser::create($data);

            // Generate OTP
            $otp = sprintf("%06d", mt_rand(100000, 999999)); // 6-digit OTP

            // Store OTP in admin_users_log
            $log = AdminUsersLog::create([
                'users_id' => $adminUser->id,
                'otp' => $otp,
            ]);

            // Send OTP via email
            $this->sendOtpEmail($adminUser->email, $otp);


            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing AdminUser : ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    private function sendOtpEmail($email, $otp)
    {
        try {
            $data = [
                'otp' => $otp,
                'message' => 'Your OTP for registration verification is: ' . $otp
            ];

            Mail::raw("Your OTP for registration verification is: {$otp}", function ($message) use ($email) {
                $message->to($email)
                    ->subject('Registration OTP Verification');
            });

            return true;
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in mail send AdminUser : ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
