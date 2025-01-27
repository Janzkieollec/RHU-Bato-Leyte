<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Population;
use App\Models\DeceasedPatient;
use App\Models\TreatmentRecord;
use App\Models\Diagnosis;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Analytics extends Controller
{
  public function index()
  {
    return view('content.dashboard.dashboards-analytics');
  }

  public function getPatientsCount()
  {
    $totalPatient = Patient::count();
    $totalPopulation = Population::orderBy('created_at', 'desc')->value('total_population');
    $totalNurse = User::where('role', 'Nurse')->count();
    $totalStaff = User::where('role', 'Staff')->count();
    $totalMidwife = User::where('role', 'Midwife')->count();
    $totalDoctor = User::where('role', 'Doctor')->count();
    $totalDentist = User::where('role', 'Dentist')->count();

  
    return response()->json([
        'total_patient' => $totalPatient,
        'total_population' => $totalPopulation,
        'totalNurse' => $totalNurse,
        'totalStaff' => $totalStaff,
        'totalMidwife' => $totalMidwife,
        'totalDoctor' => $totalDoctor,
        'totalDentist' => $totalDentist
    ]);
  }

}