<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ConsultationAnalytics;
use App\Models\DentalAnalytics;
use App\Models\Consultation;
use App\Models\DiagnosisAnalytics;
use App\Models\Diagnosis;
use App\Models\Dental;
// use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth; 

class PatientsDashboard extends Controller
{
    public function index()
    {
      return view('content.dashboard.patient-dashboard');
    }

    public function getChiefConsultation(Request $request)
    {
        // Retrieve input values from the request
        $year = $request->input('year');
        $barangayId = $request->input('barangay');
        $selectedComplaint = $request->input('complaint');
        $startMonth = $request->input('startMonth');
        $endMonth = $request->input('endMonth');

        // Get the encrypted patient ID from the authenticated user
        $encryptedPatientId = Auth::user()->patient_id;

        // Fetch consultation data with a join on the consultation table to get chief complaints
        $data = ConsultationAnalytics::join('consultations', 'consultation_analytics.consultation_id', '=', 'consultations.id')
            ->selectRaw("consultations.chief_complaints, MONTH(consultation_analytics.created_at) as month, COUNT(consultation_analytics.id) as complaints_count")
            ->when($year, function ($query) use ($year) {
                return $query->whereYear('consultation_analytics.created_at', $year); // Filter by year
            })
            ->when($startMonth && $endMonth, function ($query) use ($startMonth, $endMonth) {
                return $query->whereBetween(DB::raw('MONTH(consultation_analytics.created_at)'), [$startMonth, $endMonth]);
            })
            ->when($barangayId, function ($query) use ($barangayId) {
                return $query->where('consultation_analytics.barangay_name', $barangayId); // Filter by barangay if provided
            })
            ->when($selectedComplaint, function ($query) use ($selectedComplaint) {
                return $query->where('consultations.chief_complaints', Crypt::decrypt($selectedComplaint)); 
            })
            ->when($encryptedPatientId, function ($query) use ($encryptedPatientId) {
                return $query->where('consultations.patient_id', $encryptedPatientId); // Filter by patient ID
            })
            ->groupBy('consultations.chief_complaints', 'month')
            ->orderBy('consultations.chief_complaints', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Transform data to a structure suitable for the frontend
        $result = [];
        foreach ($data as $item) {
            $complaintName = $item->chief_complaints;
            $month = $item->month;
            $count = $item->complaints_count;

            // Initialize the array for each complaint if it doesn't exist
            if (!isset($result[$complaintName])) {
                $result[$complaintName] = array_fill(1, 12, 0); // Initialize counts for all months
            }
            $result[$complaintName][$month] = $count; // Assign the count to the respective month
        }

        // Format the result for frontend
        $formattedResult = [
            'complaint_names' => array_keys($result),
            'series' => array_map(function ($complaintName, $counts) {
                return [
                    'name' => $complaintName,
                    'data' => array_values($counts) // Array of counts for each month
                ];
            }, array_keys($result), $result)
        ];

        return response()->json($formattedResult); // Return the formatted result as JSON
    }

  
  public function getChiefComplaintConsultation(Request $request)
  {
      // This method remains unchanged but will only deal with consultations now
      $chief_complaint = Consultation::select('chief_complaints')->distinct()->get();
      
       // Encrypt diagnosis_id for each diagnosis
       $chief_complaint->each(function ($chief_complaints) {
          $chief_complaints->encrypted_id = Crypt::encrypt($chief_complaints->chief_complaints);
      });
      
      // Return the chief complaints as JSON
      return response()->json($chief_complaint);
  }  


  public function getChiefDental(Request $request)
  {
      // Retrieve input values from the request
      $year = $request->input('year');
      $barangayId = $request->input('barangay');
      $selectedComplaint = $request->input('complaint');
      $startMonth = $request->input('startMonth');
      $endMonth = $request->input('endMonth');

      // Get the encrypted patient ID from the authenticated user
      $encryptedPatientId = Auth::user()->patient_id;

      // Fetch consultation data with a join on the consultation table to get chief complaints
      $data = DentalAnalytics::join('dentals', 'dental_analytics.dental_id', '=', 'dentals.id')
          ->selectRaw("dentals.chief_complaints, MONTH(dental_analytics.created_at) as month, COUNT(dental_analytics.id) as complaints_count")
          ->when($year, function ($query) use ($year) {
              return $query->whereYear('dental_analytics.created_at', $year); // Filter by year
          })
          ->when($startMonth && $endMonth, function ($query) use ($startMonth, $endMonth) {
              return $query->whereBetween(DB::raw('MONTH(dental_analytics.created_at)'), [$startMonth, $endMonth]);
          })
          ->when($barangayId, function ($query) use ($barangayId) {
              return $query->where('dental_analytics.barangay_name', $barangayId); // Filter by barangay if provided
          })
          ->when($selectedComplaint, function ($query) use ($selectedComplaint) {
              return $query->where('dentals.chief_complaints', Crypt::decrypt($selectedComplaint)); 
          })
          ->when($encryptedPatientId, function ($query) use ($encryptedPatientId) {
              return $query->where('dentals.patient_id', $encryptedPatientId); // Filter by patient ID
          })
          ->groupBy('dentals.chief_complaints', 'month')
          ->orderBy('dentals.chief_complaints', 'asc')
          ->orderBy('month', 'asc')
          ->get();

      // Transform data to a structure suitable for the frontend
      $result = [];
      foreach ($data as $item) {
          $complaintName = $item->chief_complaints;
          $month = $item->month;
          $count = $item->complaints_count;

          // Initialize the array for each complaint if it doesn't exist
          if (!isset($result[$complaintName])) {
              $result[$complaintName] = array_fill(1, 12, 0); // Initialize counts for all months
          }
          $result[$complaintName][$month] = $count; // Assign the count to the respective month
      }

      // Format the result for frontend
      $formattedResult = [
          'complaint_names' => array_keys($result),
          'series' => array_map(function ($complaintName, $counts) {
              return [
                  'name' => $complaintName,
                  'data' => array_values($counts) // Array of counts for each month
              ];
          }, array_keys($result), $result)
      ];

      return response()->json($formattedResult); // Return the formatted result as JSON
  }
  
  public function getChiefComplaintsDental(Request $request)
    {
        // This method remains unchanged but will only deal with consultations now
        $chief_complaint = Dental::select('chief_complaints')->distinct()->get();
    
           // Encrypt diagnosis_id for each diagnosis
        $chief_complaint->each(function ($chief_complaints) {
            $chief_complaints->encrypted_id = Crypt::encrypt($chief_complaints->chief_complaints);
        });
        
        
        // Return the chief complaints as JSON
        return response()->json($chief_complaint);
    }  

    public function getDiagnosisData(Request $request)
    {
        $year = $request->input('year');
        $selectedDiagnosis = $request->input('diagnosis');
        $startMonth = $request->input('startMonth');
        $endMonth = $request->input('endMonth');
    
        // Get the encrypted patient ID from the authenticated user
        $encryptedPatientId = Auth::user()->patient_id;
    
        $data = DiagnosisAnalytics::with('diagnosis')
            ->join('diagnosis', 'diagnosis_analytics.diagnosis_id', '=', 'diagnosis.diagnosis_id')
            ->selectRaw('diagnosis.diagnosis_name, YEAR(diagnosis_analytics.created_at) as year, MONTH(diagnosis_analytics.created_at) as month, COUNT(diagnosis_analytics.id) as count')
            ->when($year, function ($query, $year) {
                return $query->whereYear('diagnosis_analytics.created_at', $year);
            })
            ->when($startMonth && $endMonth, function ($query) use ($startMonth, $endMonth) {
                return $query->whereBetween(DB::raw('MONTH(diagnosis_analytics.created_at)'), [$startMonth, $endMonth]);
            })
            ->when($selectedDiagnosis, function ($query, $selectedDiagnosis) {
                return $query->where('diagnosis.diagnosis_id', Crypt::decrypt($selectedDiagnosis));
            })
            ->where('diagnosis_analytics.patient_id', $encryptedPatientId) // Filter by the encrypted patient ID
            ->where('diagnosis.diagnosis_type', '1') // Filter for diagnosis_type of consultation
            ->groupBy('diagnosis.diagnosis_name', 'year', 'month')
            ->orderBy('diagnosis.diagnosis_name', 'asc')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    
        $result = [];
        foreach ($data as $item) {
            $diagnosisName = $item->diagnosis_name;
            $month = $item->month;
            $count = $item->count;
    
            if (!isset($result[$diagnosisName])) {
                $result[$diagnosisName] = array_fill(1, 12, 0); // Initialize 12 months with 0 counts
            }
            $result[$diagnosisName][$month] = $count; // Assign the count to the respective month
        }
    
        $formattedResult = [
            'diagnosis_names' => array_keys($result),
            'series' => array_map(function($diagnosisName, $counts) {
                return [
                    'name' => $diagnosisName,
                    'data' => array_values($counts) // Array of counts for each month
                ];
            }, array_keys($result), $result)
        ];
    
        return response()->json($formattedResult); // Return the formatted result as JSON
    }
    
  
    public function getDiagnosis(Request $request)
    {
        $fetchDiagnosis = Diagnosis::all();
    
        // Encrypt diagnosis_id for each diagnosis
        $fetchDiagnosis->each(function ($diagnosis) {
            $diagnosis->encrypted_id = Crypt::encrypt($diagnosis->diagnosis_id);
        });
    
        // Return the data directly
        return response()->json($fetchDiagnosis);
    }

    public function get_DiagnosisData(Request $request)
    {
        $year = $request->input('year');
        $selectedDiagnosis = $request->input('diagnosis');
        $startMonth = $request->input('startMonth');
        $endMonth = $request->input('endMonth');
    
        // Get the encrypted patient ID from the authenticated user
        $encryptedPatientId = Auth::user()->patient_id;
    
        $data = DiagnosisAnalytics::with('diagnosis')
            ->join('diagnosis', 'diagnosis_analytics.diagnosis_id', '=', 'diagnosis.diagnosis_id')
            ->selectRaw('diagnosis.diagnosis_name, YEAR(diagnosis_analytics.created_at) as year, MONTH(diagnosis_analytics.created_at) as month, COUNT(diagnosis_analytics.id) as count')
            ->when($year, function ($query, $year) {
                return $query->whereYear('diagnosis_analytics.created_at', $year);
            })
            ->when($startMonth && $endMonth, function ($query) use ($startMonth, $endMonth) {
                return $query->whereBetween(DB::raw('MONTH(diagnosis_analytics.created_at)'), [$startMonth, $endMonth]);
            })
            ->when($selectedDiagnosis, function ($query, $selectedDiagnosis) {
                return $query->where('diagnosis.diagnosis_id', Crypt::decrypt($selectedDiagnosis));
            })
            ->where('diagnosis_analytics.patient_id', $encryptedPatientId) // Filter by the encrypted patient ID
            ->where('diagnosis.diagnosis_type', '2') // Filter for diagnosis_type of consultation
            ->groupBy('diagnosis.diagnosis_name', 'year', 'month')
            ->orderBy('diagnosis.diagnosis_name', 'asc')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    
        $result = [];
        foreach ($data as $item) {
            $diagnosisName = $item->diagnosis_name;
            $month = $item->month;
            $count = $item->count;
    
            if (!isset($result[$diagnosisName])) {
                $result[$diagnosisName] = array_fill(1, 12, 0); // Initialize 12 months with 0 counts
            }
            $result[$diagnosisName][$month] = $count; // Assign the count to the respective month
        }
    
        $formattedResult = [
            'diagnosis_names' => array_keys($result),
            'series' => array_map(function($diagnosisName, $counts) {
                return [
                    'name' => $diagnosisName,
                    'data' => array_values($counts) // Array of counts for each month
                ];
            }, array_keys($result), $result)
        ];
    
        return response()->json($formattedResult); // Return the formatted result as JSON
    }
    
  
    public function get_Diagnosis(Request $request)
    {
        $fetchDiagnosis = Diagnosis::all();
    
        // Encrypt diagnosis_id for each diagnosis
        $fetchDiagnosis->each(function ($diagnosis) {
            $diagnosis->encrypted_id = Crypt::encrypt($diagnosis->diagnosis_id);
        });
    
        // Return the data directly
        return response()->json($fetchDiagnosis);
    }
}