<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class LoginController extends AppBaseController
{
    public function login(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            // Rate limiting to prevent brute-force attacks
            $key = 'login_attempts:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many login attempts. Please try again later.',
                ], 429);
            }

            DB::beginTransaction();

            // Find user by username or email
            $user = User::where('username', $request->username)->first();

            // Validate user and password
            if (!$user || !Hash::check($request->password, $user->password)) {
                RateLimiter::hit($key, 60); // Increase failed login count (lockout for 1 minute)
                return $this->sendError('The provided credentials are incorrect.', 401);
            }

            // Reset login attempts after successful login
            RateLimiter::clear($key);

            // If OTP is enabled, send OTP
            if ($user->otp_enabled) {
                try {
                    $user->sendOtp();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Error sending OTP: ' . $e->getMessage(), [
                        'user_id' => $user->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to send OTP. Please try again.',
                    ], 500);
                }

                DB::commit();
                return $this->sendSuccess('OTP sent to your phone number. Please verify.');
            }

            // Generate API token immediately if OTP is not enabled
            $token = $user->createToken('API Token')->plainTextToken;

            DB::commit();

            return $this->sendResponse(['token' => $token], 'Login successful.');
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error during login: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while logging in. Please try again later.',
            ], 500);
        }
    }


    public function verifyOtp(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'username' => 'required|string',
                'otp' => 'required|digits:6',
            ]);

            // Apply rate limiting to prevent brute-force OTP attacks
            $key = 'otp_verify_attempts:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many OTP attempts. Please try again later.',
                ], 429);
            }

            DB::beginTransaction();

            // Find user by username and OTP
            $user = User::where('username', $request->username)
                ->where('otp', $request->otp)
                ->first();

            // Validate OTP
            if (!$user) {
                RateLimiter::hit($key, 60); // Increment failed attempts (lockout for 1 min)
                return $this->sendError('Invalid OTP code.', 401);
            }

            // Clear OTP after successful verification
            $user->update(['otp' => null]);

            // Reset OTP attempts after success
            RateLimiter::clear($key);

            // Generate API token
            $token = $user->createToken('API Token')->plainTextToken;

            DB::commit();

            return $this->sendResponse(['token' => $token], 'OTP verified successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error during OTP verification: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying the OTP.',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Ensure the user is authenticated
            if (!$request->user()) {
                return $this->sendError('Unauthorized', 401);
            }

            DB::beginTransaction();

            // Delete all tokens for the authenticated user
            $request->user()->tokens()->delete();

            DB::commit();

            return $this->sendSuccess('Logged out successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error during logout: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while logging out.',
            ], 500);
        }
    }
}
