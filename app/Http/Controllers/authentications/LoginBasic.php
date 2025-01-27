<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;

class LoginBasic extends Controller
{
    public function index()
    {
        return view('content.authentications.auth-login-basic');
    }

    public function landingpage()
    {
        return view('content.landing_page.landing_page');
    }

    public function login(Request $request)
    {
        try {
            $this->checkTooManyFailedAttempts($request);

            $credentials = $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                $user = Auth::user();
                $role = $user->role;  // Assuming you have a 'role' column in your 'users' table

                if ($role == 'Admin') {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('dashboard-analytics'),  // Admin dashboard route
                    ]);
                } elseif ($role == 'Patient') {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('patient-dashboard'),  // Patient dashboard route
                    ]);
                } else if($role == 'Doctor'){
                    return response()->json([
                        'success' => true,
                        'redirect' => route('doctor-dashboard'),  // Doctor dashboard route
                    ]);
                } else if($role == 'Dentist'){
                    return response()->json([
                        'success' => true,
                        'redirect' => route('dentist-dashboard'),  // Dental dashboard route
                    ]);
                } else if($role == 'Nurse'){
                    return response()->json([
                        'success' => true,
                        'redirect' => route('nurse-dashboard'),  // Nurse dashboard Nurse 
                    ]);
                }  else if($role == 'Midwife'){
                    return response()->json([
                        'success' => true,
                        'redirect' => route('midwife-dashboard'),  // Nurse dashboard Nurse 
                    ]);
                } else if($role == 'Staff'){
                    return response()->json([
                        'success' => true,
                        'redirect' => route('staff-dashboard'),  // Staff dashboard route
                    ]);
                }
            }

            $this->handleFailedLogin($request->email);
        } catch (Exception $error) {
            $attemptsLeft = RateLimiter::remaining($this->throttleKey($request->email), 5);
            $seconds = RateLimiter::availableIn($this->throttleKey($request->email));

            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
                'attemptsLeft' => $attemptsLeft,
                'timeLeft' => $seconds,
                'gmail' => $request->email,
            ]);
        }
    }

    protected function checkTooManyFailedAttempts(Request $request)
    {
        $key = $this->throttleKey($request->email);
        if (!RateLimiter::tooManyAttempts($key, 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);
        throw new Exception('Too many login attempts. Try again in ' . gmdate('H:i:s', $seconds));
    }

    protected function handleFailedLogin($email)
    {
        $key = $this->throttleKey($email);
        RateLimiter::hit($key, $seconds = 3600);
        throw new Exception('Invalid Credentials');
    }

    protected function throttleKey($email)
    {
        return Str::lower($email) . '|' . request()->ip();
    }

    public function sso(Request $request) 
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
    
        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
    
            $role = $user->role;  // Assuming you have a 'role' column in your 'users' table
    
            // Send the response with the correct redirect URL based on the role
            if ($role == 'Admin') {
                return response()->json([
                    'status_code' => 0,
                    'redirect' => route('dashboard-analytics'),  // Admin dashboard route
                ]);
            } elseif ($role == 'Patient') {
                return response()->json([
                    'status_code' => 0,
                    'redirect' => route('patient-dashboard'),  // Patient dashboard route
                ]);
            } elseif ($role == 'Doctor') {
                return response()->json([
                    'status_code' => 0,
                    'redirect' => route('doctor-dashboard'),  // Doctor dashboard route
                ]);
            } elseif ($role == 'Dentist') {
                return response()->json([
                    'status_code' => 0,
                    'redirect' => route('dentist-dashboard'),  // Dentist dashboard route
                ]);
            }elseif($role == 'Nurse'){
                return response()->json([
                    'status_code' => 0,
                    'redirect' => route('nurse-dashboard'),  // Nurse dashboard Nurse 
                ]);
            } elseif($role == 'Midwife'){
                return response()->json([
                    'status_code' => 0,
                    'redirect' => route('midwife-dashboard'),  // Nurse dashboard Nurse 
                ]);
            } elseif ($role == 'Staff') {
                return response()->json([
                    'status_code' => 0,
                    'redirect' => route('staff-dashboard'),  // Staff dashboard route
                ]);
            } else {
                return response()->json([
                    'status_code' => 1,  // Unrecognized role
                    'message' => 'User role not recognized.',
                ]);
            }
        } else {
            return response()->json([
                'status_code' => 1,  // Return error if user not found
                'message' => 'User not found',
            ]);
        }
    }
    
    
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['status' => 'success', 'message' => 'Logged out successfully']);
    }
}