<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register a new student user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'student_id' => 'required|string|unique:users',
            'course' => 'required|string',
            'year_level' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'address' => 'required|string',
            'contact_number' => 'required|string',
            'emergency_contact_name' => 'required|string',
            'emergency_contact_relationship' => 'required|string',
            'emergency_contact_number' => 'required|string',
            'marital_status' => 'nullable|string',
            'occupation' => 'nullable|string',
            'nationality' => 'nullable|string',
            'emergency_contact_email' => 'nullable|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => 'student',
            'student_id' => $request->student_id,
            'course' => $request->course,
            'year_level' => $request->year_level,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'marital_status' => $request->marital_status,
            'occupation' => $request->occupation,
            'nationality' => $request->nationality,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            'emergency_contact_number' => $request->emergency_contact_number,
            'emergency_contact_email' => $request->emergency_contact_email,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Student registered successfully',
            'user' => $user,
            'access_token' => $token,
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Debug incoming request
        \Illuminate\Support\Facades\Log::info('Login request received', [
            'email' => $request->email,
            'has_password' => !empty($request->password)
        ]);

        // Define validation rules
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            \Illuminate\Support\Facades\Log::warning('Login validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Make email lowercase for consistency
        $email = strtolower($request->email);
        
        // Check if the user exists
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 401);
        }

        // Attempt authentication
        if (!Auth::attempt(['email' => $email, 'password' => $request->password])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // After successful authentication, handle role checking on the frontend
        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;
        
        \Illuminate\Support\Facades\Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'role' => $user->role
        ]);
        
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'access_token' => $token,
        ]);
    }

    /**
     * Logout user (revoke token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Revoke all tokens
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user' => $request->user()
        ]);
    }
    public function mobileRegister(Request $request)
{
    return $this->registerStudent($request);
}

public function mobileLogin(Request $request)
{
    return $this->login($request);
}

}