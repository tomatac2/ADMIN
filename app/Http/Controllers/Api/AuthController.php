<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Traits\MessageTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Exceptions\ExceptionHandler;
use App\Helpers\Response;
use App\Helpers\ResponseStatus;
use App\Http\Resources\Api\User\Auth\LoginResource;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    use MessageTrait;

    public function login(Request $request)
    {
        try {
            $user = $this->verifyLogin($request);
            if (!Hash::check($request->password, $user->password) || !$user->hasRole(RoleEnum::USER)) {
                return Response::respondError('auth.invalid_backend_credentials', ResponseStatus::NOT_FOUND);
            }
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::table('auth_tokens')->insert([
                'token' => $token,
                'phone' => '+' . $request->country_code . $request->phone,
                'created_at' => Carbon::now(),
            ]);

            return Response::respondSuccess('auth.login_success', LoginResource::make($user, $token));
        } catch (Exception $e) {
            return Response::respondError('auth.something_went_wrong', ResponseStatus::NOT_FOUND);
        }
    }

    public function adminLogin(Request $request)
    {
        try {
            $user = $this->verifyLogin($request);
            if (!Hash::check($request->password, $user->password) || !$user->hasRole(RoleEnum::ADMIN)) {
                throw new Exception(__('auth.invalid_backend_credentials'), 400);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $user->tokens()->update([
                'role_type' => $user->getRoleNames()->first(),
            ]);

            $user->update([
                'fcm_token' => $request?->fcm_token,
            ]);

            return [
                'access_token' => $token,
                'permissions' => $user->getAllPermissions(),
                'success' => true,
            ];
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verify_auth_token(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'country_code' => 'required',
                'phone' => 'required',
                'token' => 'required',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->messages()->first(), 422);
            }

            $verify_otp = DB::table('auth_tokens')
                ->where('token', $request->token)
                ->where('phone', '+' . $request->country_code . $request->phone)
                ->where('created_at', '>', Carbon::now()->subHours(1))
                ->first();

            if (!$verify_otp) {
                throw new Exception(__('static.auth.invalid_token'), 400);
            }

            $user = User::where('phone', (string) $request->phone)->first();
            $token = $user->createToken('auth_token')->plainTextToken;
            $user->tokens()->update([
                'role_type' => $user->getRoleNames()->first(),
            ]);

            return [
                'access_token' => $token,
                'permissions' => $user->getAllPermissions(),
                'success' => true,
            ];
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verifyLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['nullable', Rule::requiredIf(!$request->phone), 'email'],
                'password' => ['required', 'string'],
                'phone' => ['nullable', Rule::requiredIf(!$request->email)],
                'country_code' => ['nullable', Rule::requiredIf(!$request->email)],
            ]);

            if ($validator->fails()) {
                return Response::respondError($validator->messages()->first(), ResponseStatus::VALIDATION_ERROR);
            }

            $user = User::where('email', (string) $request->email)->orWhere('phone', (string) $request->phone)->first();
            if (!$user && isset($request->email)) {
                return Response::respondError('static.auth.no_linked_email', ResponseStatus::NOT_FOUND);
            }

            if (!$user && isset($request->phone)) {
                return Response::respondError('static.auth.no_linked_number', ResponseStatus::NOT_FOUND);
            }

            if (!$user->status) {
                return Response::respondError('static.auth.disabled_account', ResponseStatus::NOT_FOUND);
            }
            return $user;
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
                'password' => 'required|string|min:8',
                'country_code' => 'required',
                'phone' => 'required|min:9|unique:users,phone,NULL,id,deleted_at,NULL',
            ]);

            if ($validator->fails()) {
                return Response::respondError($validator->messages()->first(), ResponseStatus::VALIDATION_ERROR);
            }

            // Check if user already exists but not verified
            $existingUser = User::where('email', $request->email)->orWhere('phone', (string) $request->phone)->first();

            if ($existingUser) {
                return Response::respondError('auth.user_already_exists', ResponseStatus::CONFLICT);
            }

            // Generate OTP (default 1234 for now)
            $otp = '1234'; // TODO: Generate random OTP when SMS/Email service is available

            // Store registration data in session or temporary storage
            // For now, we'll use a simple approach with the auth_tokens table
            $tempToken = uniqid('reg_', true);

            DB::table('auth_tokens')->insert([
                'token' => $tempToken,
                'phone' => '+' . $request->country_code . $request->phone,
                'email' => $request->email,
                'registration_data' => json_encode([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'country_code' => $request->country_code,
                    'phone' => (string) $request->phone,
                    'referral_code' => $otp, // TODO: Generate referral code
                ]),
                'created_at' => Carbon::now(),
            ]);

            // TODO: Send OTP to email/phone

            DB::commit();
            return Response::respondSuccess('auth.otp_sent', [
                'token' => $tempToken,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return Response::respondError('auth.something_went_wrong');
        }
    }

    public function verifyRegister(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
                'otp' => 'required|string|size:4',
            ]);

            if ($validator->fails()) {
                return Response::respondError($validator->messages()->first(), ResponseStatus::VALIDATION_ERROR);
            }

            // Find the registration token
            $registrationToken = DB::table('auth_tokens')
                ->where('token', $request->token)
                ->where('created_at', '>', Carbon::now()->subHours(1)) // Token expires in 1 hour
                ->first();

            if (!$registrationToken) {
                return Response::respondError('auth.invalid_token', ResponseStatus::NOT_FOUND);
            }

            $registrationData = json_decode($registrationToken->registration_data, true);
            if (!$registrationData) {
                return Response::respondError('static.auth.invalid_registration_data', ResponseStatus::NOT_FOUND);
            }

            if ($request->otp !== $registrationData['referral_code']) {
                return Response::respondError('auth.invalid_otp', ResponseStatus::NOT_FOUND);
            }

            // Check if user already exists (double-check)
            $existingUser = User::where('email', $registrationData['email'])->orWhere('phone', $registrationData['phone'])->first();

            if ($existingUser) {
                return Response::respondError('auth.user_already_exists', ResponseStatus::VALIDATION_ERROR);
            }

            // Create the user
            $user = User::create([
                'name' => $registrationData['name'],
                'email' => $registrationData['email'],
                'password' => $registrationData['password'],
                'country_code' => $registrationData['country_code'],
                'phone' => $registrationData['phone'],
                'is_verified' => 1,
                'status' => 1,
                'email_verified_at' => Carbon::now(),
            ]);

            // Generate auth token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Clean up the temporary registration token
            DB::table('auth_tokens')->where('token', $request->token)->delete();

            DB::commit();

            return Response::respondSuccess('auth.registration_success', LoginResource::make($user, $token));
        } catch (Exception $e) {
            DB::rollback();
            return Response::respondError('auth.something_went_wrong');
        }
    }

    public function resendRegistrationOtp(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return Response::respondError($validator->messages()->first(), ResponseStatus::VALIDATION_ERROR);
            }

            // Find the registration token
            $registrationToken = DB::table('auth_tokens')
                ->where('token', $request->token)
                ->where('created_at', '>', Carbon::now()->subHours(1)) // Token expires in 1 hour
                ->first();

            if (!$registrationToken) {
                return Response::respondError('auth.invalid_token', ResponseStatus::NOT_FOUND);
            }

            // Generate new OTP (default 1234 for now)
            $otp = '1234'; // TODO: Generate random OTP when SMS/Email service is available

            // Parse existing registration data
            $registrationData = json_decode($registrationToken->registration_data, true);
            if (!$registrationData) {
                return Response::respondError('auth.invalid_registration_data', ResponseStatus::NOT_FOUND);
            }

            // Update the referral code with new OTP
            $registrationData['referral_code'] = $otp;

            // Update the token with new timestamp and updated registration data
            DB::table('auth_tokens')
                ->where('token', $request->token)
                ->update([
                    'created_at' => Carbon::now(),
                    'registration_data' => json_encode($registrationData)
                ]);

            // TODO: Send OTP to email/phone
            // For now, just return the OTP in response (remove this in production)

            DB::commit();
            return Response::respondSuccess('auth.otp_resent', [
                'token' => $request->token,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return Response::respondError('auth.something_went_wrong');
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'country_code' => 'required|string',
                'phone' => 'required|string|exists:users,phone',
            ]);

            if ($validator->fails()) {
                return Response::respondError($validator->messages()->first(), ResponseStatus::VALIDATION_ERROR);
            }

            // For development, use fixed OTP
            $token = '1234';
            
            DB::table('password_resets')->insert([
                'email' => null, // Keep for compatibility but set to null
                'phone' => '+' . $request->country_code . $request->phone,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            // TODO: Send SMS with OTP to phone
            // For now, just return the OTP in response (remove this in production)

            return Response::respondSuccess('auth.otp_sent');
        } catch (Exception $e) {
            return Response::respondError('auth.something_went_wrong');
        }
    }

    public function verifyToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'country_code' => 'required|string',
                'phone' => 'required|string',
            ]);

            if ($validator->fails()) {
                return Response::respondError($validator->messages()->first(), ResponseStatus::VALIDATION_ERROR);
            }

            $user = DB::table('password_resets')
                ->where('token', $request->token)
                ->where('phone', '+' . $request->country_code . $request->phone)
                ->where('created_at', '>', Carbon::now()->subHours(1))
                ->first();

            if (!$user) {
                return Response::respondError('auth.invalid_token', ResponseStatus::VALIDATION_ERROR);
            }

            return Response::respondSuccess('auth.verification_token');
        } catch (Exception $e) {
            return Response::respondError('auth.something_went_wrong');
        }
    }

    public function updatePassword(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'country_code' => 'required|string',
                'phone' => 'required|string|exists:users,phone',
                'password' => 'required|min:8|confirmed',
                'password_confirmation' => 'required',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->messages()->first(), 422);
            }

            $user = DB::table('password_resets')
                ->where('token', $request->token)
                ->where('phone', '+' . $request->country_code . $request->phone)
                ->where('created_at', '>', Carbon::now()->subHours(1))
                ->first();

            if (!$user) {
                throw new Exception(__('static.invalid_token'), 400);
            }

            User::where('phone', (string) $request->phone)->update(['password' => Hash::make($request->password)]);

            DB::table('password_resets')->where('phone', '+' . $request->country_code . $request->phone)->delete();
            DB::commit();

            return Response::respondSuccess('auth.change_password');
        } catch (Exception $e) {
            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function resendPasswordResetOtp(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'country_code' => 'required|string',
                'phone' => 'required|string|exists:users,phone',
            ]);

            if ($validator->fails()) {
                return Response::respondError($validator->messages()->first(), ResponseStatus::VALIDATION_ERROR);
            }

            // Check if there's an existing password reset token
            $existingToken = DB::table('password_resets')
                ->where('phone', '+' . $request->country_code . $request->phone)
                ->where('created_at', '>', Carbon::now()->subHours(1))
                ->first();

            if (!$existingToken) {
                return Response::respondError('auth.no_password_reset_request', ResponseStatus::VALIDATION_ERROR);
            }

            // Generate new OTP
            $token = '1234'; // TODO: Generate random OTP when SMS service is available

            // Update the existing token with new OTP and timestamp
            DB::table('password_resets')
                ->where('phone', '+' . $request->country_code . $request->phone)
                ->update([
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);

            // TODO: Send SMS with new OTP to phone
            // For now, just return the OTP in response (remove this in production)
            DB::commit();
            return Response::respondSuccess('auth.otp_resent');
        } catch (Exception $e) {
            DB::rollback();
            return Response::respondError('auth.something_went_wrong');
        }
    }

    public function logout(Request $request)
    {
        try {
            $token = PersonalAccessToken::findToken($request->bearerToken());
            if (!$token) {
                return Response::respondError('auth.invalid_token', ResponseStatus::VALIDATION_ERROR);
            }
            $token->delete();

            return Response::respondSuccess('auth.user_logout');
        } catch (Exception $e) {
            return Response::respondError('auth.something_went_wrong');
        }
    }

    public function checkUserValidation(Request $request)
    {
        try {
            $users = DB::table('users')
                ->whereNull('deleted_at')
                ->where(function ($query) use ($request) {
                    $query->where('username', $request->username)->orWhere('phone', $request->phone)->orWhere('email', $request->email);
                })
                ->exists();

            return 0;
        } catch (Exception $e) {
            return 1;
        }
    }
}
