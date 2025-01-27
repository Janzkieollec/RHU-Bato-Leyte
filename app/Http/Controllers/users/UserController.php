<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Email;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UserController extends Controller
{
    public function index()
    {
        return view('content.admin.users.users');
    }

    // Add User
    public function store(Request $request)
    {
        $request->validate(
            [
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'role' => 'required',
                'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'],
                'status' => 'required|string',
            ],
            [
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least :min characters.',
                'password.regex' => 'Password should contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            ],
        );

        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->status = $request->status;

        // Mail::to($request['email'])->send(new Email([$request['name']]));

        $user->save();

        $role = Auth::user()->role;

        Log::create([
            'role' => $role,
            'action' => 'Added',
            'description' => "You added {$user->username} as a {$user->role}.",
        ]);

        return response()->json($user);
    }

    // Fetch User
    public function fetchUser()
    {
        // Fetch users with role 'Patient' or 'User', along with the patient details
        $users = User::whereIn('role', ['Patient', 'Doctor', 'Dentist', 'Nurse', 'Staff', 'Midwife'])
                    // ->with('patient:patient_id,last_name,middle_name,first_name')
                    ->get();

        // Encrypt the id for each user
        foreach ($users as $user) {
            $user->encrypted_id = Crypt::encrypt($user->id);
        }

        // Return the filtered users with patient details as JSON
        return response()->json([
            'users' => $users,
        ]);
    }



    // Get User
    public function getUser($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $user = User::find($id);

            if ($user) {
                return response()->json([
                    'status' => 200,
                    'user' => $user,
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'User Not Found',
                ]);
            }
        } catch (DecryptException $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid',
            ]);
        }
    }

    // Update User
    public function updateUser(Request $request, $encryptedId)
    {   
        $userId = Crypt::decrypt($encryptedId);

        $data = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|unique:users,email,' . $userId,
            'role' => 'required',
            'status' => 'required|string',
        ]);

        $user = User::findOrFail($userId);

        $user->update($data);

        $role = Auth::user()->role;

        Log::create([
            'role' => $role,
            'action' => 'Updated',
            'description' => "{$user->role} {$user->username} details were updated.",
        ]);

        return response()->json(['success' => true]);
    }

    // Delete User
    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $user = User::where('id', $id)->first();

            $role = Auth::user()->role;

            $user->findOrFail($id);
            
            Log::create([
                'role' => $role,
                'action' => 'Delete',
                'description' => "You have succesfully deleted role {$user->role} {$user->username}.",
            ]);
            
            $user->delete();
            
            return response()->json(['success' => true ]);
        } catch (DecryptException $e) {
            return response()->json(['success' => false ]);
        }
    }
}