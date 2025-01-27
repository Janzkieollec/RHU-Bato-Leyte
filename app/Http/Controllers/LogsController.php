<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class LogsController extends Controller
{
    public function index()
    {
        return view('content.pages.logs');
    }

    public function fetchLogs(Request $request)
    {
        $pageSize = $request->input('pageSize', 10); // Default to 10
        
        // Get the logged-in user's role
        $user = Auth::user();
        $rolename = null;

        // Set diagnosis type based on role
        if ($user->role === 'Nurse') {
            $rolename = 'Nurse'; 
        } elseif ($user->role === 'Staff') {
            $rolename = 'Staff'; 
        } elseif ($user->role === 'Doctor') {
            $rolename = 'Doctor'; 
        } elseif ($user->role === 'Dentis') {
            $rolename = 'Dentist'; 
        } elseif ($user->role === 'Admin') {
            $rolename = 'Admin'; 
        }

         // Base query for patients with joins
        $query = Log::select(
            'logs.id',
            'logs.description',
            'logs.created_at',
        )
        ->where('logs.role', $rolename)
        ->orderBy('created_at', 'DESC');
        // Apply search filter
       
        // Paginate results
        $logs = $query->paginate($pageSize);
    
        // Process patient data
        foreach ($logs as $log) {

            $log->encrypted_id = Crypt::encrypt($log->id); // Encrypt the patient ID
            $log->formattedDate = Carbon::parse($log->created_at)->format('Y-m-d H:i:s'); // Format created_at
 
        }
    
        return response()->json([
            'log' => $logs,
        ]);
    }
}