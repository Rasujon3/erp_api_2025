<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Http\Controllers\AppBaseController;


class ForgotPasswordController extends AppBaseController
{
    /**
     * Send Reset Request (OTP if enabled, otherwise email)
     */
    public function sendResetRequest(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        // Find user by username or email
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user) {
            return $this->sendError('User not found.', 404);
        }

        if ($user->otp_enabled) {
            // OTP-based reset
            $otp = rand(100000, 999999);
            $user->update(['otp' => $otp]);

            // Assume sendOtp() method sends OTP to the user
            $user->sendOtp();

            return $this->sendSuccess('OTP sent to your phone number.');
        } else {
            // Email-based reset
            $status = Password::sendResetLink(['email' => $user->email]);

            return $status === Password::RESET_LINK_SENT
                ? $this->sendSuccess('Password reset link sent to your email.')
                : $this->sendError('Failed to send reset link.', 500);
        }
    }

    /**
     * Verify OTP for Password Reset (if OTP Enabled)
     */
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('username', $request->username)
            ->where('otp', $request->otp)
            ->first();

        if (!$user) {
            return $this->sendError('Invalid OTP.', 401);
        }

        // Clear OTP after successful verification
        $user->update(['otp' => null]);

        return $this->sendSuccess('OTP verified. You can now reset your password.');
    }

    /**
     * Reset Password (OTP or Email)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('username', $request->username)
            ->first();

        if (!$user) {
            return $this->sendError('User not found.', 404);
        }

        // Update new password
        $user->update(['password' => Hash::make($request->password)]);

        return $this->sendSuccess('Password reset successfully.');
    }

    /**
     * Handle Password Reset via Email (Laravel's Built-in Flow)
     */
    public function resetPasswordWithToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->sendSuccess('Password reset successful.')
            : $this->sendError('Invalid token.', 400);
    }
}
