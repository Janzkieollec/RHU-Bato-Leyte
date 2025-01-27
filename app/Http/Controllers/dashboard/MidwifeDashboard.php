<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Implant;
use App\Models\FamilyPlanning;

class MidwifeDashboard extends Controller
{
    //
    public function index()
    {
        return view('content.dashboard.midwife-dashboard');
    }

    public function getYearlyPatientData()
    {
        // Query the consultations and group by year
        $patientData = Patient::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
                                        ->groupBy('year')
                                        ->orderBy('year', 'asc')
                                        ->get();

        // Format the data to return
        $years = $patientData->pluck('year')->toArray();
        $patientCounts = $patientData->pluck('total')->toArray();

        // Return the data to be used in the view
        return response()->json([
            'labels' => $years,       // Years for x-axis
            'data' => $patientCounts  // Total consultations per year
        ]);
    }

    public function getPlanningImplantCount()
    {
        // Count the total number of patients 
        $totalPatients = Patient::count();
    
        $totalImplant = Implant::count();
        $totalFamilyPlanning = FamilyPlanning::count();
       
        return response()->json([
            'totalPatient' => $totalPatients,
            'totalImplant' => $totalImplant,
            'totalFamilyPlanning' => $totalFamilyPlanning
        ]);
    }
    
}