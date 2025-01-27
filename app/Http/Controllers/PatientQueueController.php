<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\PatientLimitSet ;
use  App\Models\PatientLimit;
use Carbon\Carbon;

class PatientQueueController extends Controller
{

    public function index()
    {
        // Fetch patient limits for today (where 'date' is today or after today)
        $patientLimits = PatientLimit::whereDate('created_at', Carbon::today())->get();
    
        // Return the view with the filtered patient limits
        return view('layouts.sections.navbar.navbar', ['patientLimits' => $patientLimits]);
    }

    public function setMaxPatients(Request $request)
    {
        $request->validate([
            'maxPatients' => 'required|integer|min:1',
        ]);

        $maxPatients = $request->input('maxPatients');
        $role = auth()->user()->role; // Assuming the user's role is stored in the `role` column.
        $userId = auth()->user()->id;
        $username = auth()->user()->username;

        $doctorName = $role . ' ' . $username;

        // Save or update the limit for the role
        $patientLimit = PatientLimit::create([
            'user_id' => $userId,
            'current_patients' => 0,
            'max_patients' => $maxPatients,
        ]);

        // Notify nurses with the updated patient limit
        $message = "The $doctorName has set the maximum number of patients to $maxPatients.";
        event(new PatientLimitSet($message, $patientLimit));

        return response()->json(['success' => true, 'message' => 'Maximum number of patients set successfully.']);
    }

    // public function setMaxPatients(Request $request)
    // {
    //     $request->validate([
    //         'maxPatients' => 'required|integer|min:1',
    //     ]);
    
    //     $maxPatients = $request->input('maxPatients');
    //     $role = auth()->user()->role; // Assuming the user's role is stored in the `role` column.
    //     $userId = auth()->user()->id;
    //     $username = auth()->user()->username;
        
    //     $doctorName = $role . ' ' . $username;
        
    //     // Save or update the limit for the role
    //     PatientLimit::updateOrCreate(
    //         ['user_id' => $userId], // Condition to find the record
    //         ['max_patients' => $maxPatients] // Values to update or create
    //     );
    
    //     // Notify nurses
    //     $message = "The $doctorName has set the maximum number of patients to $maxPatients.";
    //     event(new PatientLimitSet($message));
    
    //     return response()->json(['success' => true, 'message' => 'Maximum number of patients set successfully.']);
    // }    
    
    
}