<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Diagnosis;
use App\Models\DiagnosisAnalytics;
use App\Models\DentalAnalytics;
use App\Models\ConsultationQueue;
use App\Models\Dental;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use DB;

class DentistDashboard extends Controller
{
    //
    public function index()
    {
        return view('content.dashboard.dentist-dashboard');
    }

    public function getDiagnosisData(Request $request)
    {
        $year = $request->input('year');
        $barangayId = $request->input('barangay');
        $selectedDiagnosis = $request->input('diagnosis');
        $startMonth = $request->input('startMonth');
        $endMonth = $request->input('endMonth');
    
        $data = DiagnosisAnalytics::with('diagnosis')
            ->join('diagnosis', 'diagnosis_analytics.diagnosis_id', '=', 'diagnosis.diagnosis_id')
            ->selectRaw('diagnosis.diagnosis_name, MONTH(diagnosis_analytics.created_at) as month, COUNT(diagnosis_analytics.id) as count')
            ->when($year, function ($query, $year) {
                return $query->whereYear('diagnosis_analytics.created_at', $year);
            })
            ->when($startMonth && $endMonth, function ($query) use ($startMonth, $endMonth) {
                return $query->whereBetween(DB::raw('MONTH(diagnosis_analytics.created_at)'), [$startMonth, $endMonth]);
            })
            ->when($barangayId, function ($query, $barangayId) {
                return $query->where('diagnosis_analytics.barangay_name', $barangayId); // Ensure this matches the correct field
            })
            ->when($selectedDiagnosis, function ($query, $selectedDiagnosis) {
                return $query->where('diagnosis.diagnosis_id', Crypt::decrypt($selectedDiagnosis));
            })
            ->where('diagnosis.diagnosis_type', '2') // Filter for diagnosis_type of consultation
            ->groupBy('diagnosis.diagnosis_name', 'month')
            ->orderBy('diagnosis.diagnosis_name', 'asc')
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

    public function getHighestCases(Request $request)
    {
        $year = $request->input('year');
        $selectedDiagnosis = $request->input('diagnosis');
        $selectedBarangay = $request->input('barangay');
        $startMonth = $request->input('startMonth');
        $endMonth = $request->input('endMonth');
        
        // Base query to get cases with optional filters
        $query = DiagnosisAnalytics::with('diagnosis')
            ->join('diagnosis', 'diagnosis_analytics.diagnosis_id', '=', 'diagnosis.diagnosis_id')
            ->selectRaw('diagnosis_analytics.barangay_name, diagnosis.diagnosis_name, COUNT(diagnosis.diagnosis_id) as case_count')
            ->when($year, function ($query) use ($year) {
                return $query->whereYear('diagnosis_analytics.created_at', $year);
            })
            ->when($startMonth && $endMonth, function ($query) use ($startMonth, $endMonth) {
                return $query->whereMonth('diagnosis_analytics.created_at', '>=', $startMonth)
                             ->whereMonth('diagnosis_analytics.created_at', '<=', $endMonth);
            })
            ->when($selectedDiagnosis, function ($query) use ($selectedDiagnosis) {
                return $query->where('diagnosis.diagnosis_id', Crypt::decrypt($selectedDiagnosis));
            })
            ->when($selectedBarangay, function ($query) use ($selectedBarangay) {
                return $query->where('diagnosis_analytics.barangay_name', $selectedBarangay);
            })
            ->where('diagnosis.diagnosis_type', 2) // Filter for diagnosis_type 1
            ->groupBy('diagnosis_analytics.barangay_name', 'diagnosis.diagnosis_name')
            ->orderBy('case_count', 'desc'); // Sort by case_count in descending order
        
        // Execute the query to get case counts
        $caseCounts = $query->get();
        
        // Classify cases into categories
        $classifiedCases = $caseCounts->map(function ($item) {
            if ($item->case_count >= 20) {
                $caseCategory = 'High';
            } elseif ($item->case_count >= 10) {
                $caseCategory = 'Moderate';
            } else {
                $caseCategory = 'Lower';
            }
    
            return [
                'barangay_name' => $item->barangay_name,
                'count' => $item->case_count,
                'diagnosis_name' => $item->diagnosis_name,
                'category' => $caseCategory,
            ];
        });
    
        // Separate classified cases into groups
        $highCases = $classifiedCases->where('category', 'High')->take(5);
        $moderateCases = $classifiedCases->where('category', 'Moderate')->take(5);
        $lowCases = $classifiedCases->where('category', 'Lower')->take(5);
    
        // Merge cases into a single collection
        $result = $highCases->concat($moderateCases)->concat($lowCases);
    
        // If a specific barangay is selected, find the highest case count within that barangay
        if ($selectedBarangay) {
            $highestCase = $result->filter(function ($item) use ($selectedBarangay) {
                return $item['barangay_name'] === $selectedBarangay;
            })->first();
    
            // Return the highest case only for the selected barangay
            return response()->json($highestCase ? [$highestCase] : []);
        }
    
        return response()->json($result->values());
    }    
       
    public function getAgeDistributionData(Request $request)
    {
        // Retrieve input data from the request
        $year = $request->input('year');
        $barangayId = $request->input('barangay');
        $selectedDiagnosis = $request->input('diagnosis'); 
        $startMonth = $request->input('startMonth');
        $endMonth = $request->input('endMonth');
    
        // Base query to fetch patients and their addresses through diagnosis_analytics
        $query = DiagnosisAnalytics::with('diagnosis')
            ->join('patients', 'diagnosis_analytics.patient_id', '=', 'patients.patient_id') // Join with patients directly
            ->join('diagnosis', 'diagnosis_analytics.diagnosis_id', '=', 'diagnosis.diagnosis_id') // Join with diagnosis
            ->select('diagnosis_analytics.barangay_name', 'diagnosis_analytics.age', 'diagnosis.diagnosis_id'); // Ensure to select 'diagnosis_analytics.age'
    
          // Apply year, start month, and end month filters if year is provided
          if ($year) {
            $query->whereYear('diagnosis_analytics.created_at', $year);
    
            if ($startMonth && $endMonth) {
                $query->whereMonth('diagnosis_analytics.created_at', '>=', $startMonth)
                      ->whereMonth('diagnosis_analytics.created_at', '<=', $endMonth);
            }
        } else {
            // Only apply month range filter if year is not specified
            if ($startMonth && $endMonth) {
                $query->whereMonth('diagnosis_analytics.created_at', '>=', $startMonth)
                      ->whereMonth('diagnosis_analytics.created_at', '<=', $endMonth);
            }
        }
    
        // Add barangay filter
        if ($barangayId) {
            $query->where('diagnosis_analytics.barangay_name', $barangayId); // Fixed table name
        }
    
        // Add diagnosis filter if selected
        if ($selectedDiagnosis) {
            $diagnosisId = Crypt::decrypt($selectedDiagnosis);
            $query->where('diagnosis_analytics.diagnosis_id', $diagnosisId);
        }
    
        // Ensure the diagnosis type is '2'
        $query->where('diagnosis.diagnosis_type', '2');
    
        // Group patients by age range
        $ageGroups = [
            '0-9' => 0,
            '10-19' => 0,
            '20-59' => 0,
            '60+' => 0,
        ];
    
        // Fetch patients based on the query
        $patients = $query->get();
    
        // Count patients in age groups
        foreach ($patients as $patient) {
            if ($patient->age <= 9) {
                $ageGroups['0-9']++;
            } elseif ($patient->age <= 19) {
                $ageGroups['10-19']++;
            } elseif ($patient->age <= 59) {
                $ageGroups['20-59']++;
            } else {
                $ageGroups['60+']++;
            }
        }
    
        // Prepare the data for response
        $data = [
            'series' => array_values($ageGroups),
            'labels' => array_keys($ageGroups),
        ];
    
        return response()->json($data);
    }    
    

    public function getGenderDistributionData(Request $request)
    {
        // Get inputs from the request
        $year = $request->input('year');
        $barangayId = $request->input('barangay');
        $selectedDiagnosis = $request->input('diagnosis');
        $startMonth = $request->input('startMonth');
        $endMonth = $request->input('endMonth');
    
        $query = DiagnosisAnalytics::select('diagnosis_analytics.barangay_name', 'diagnosis_analytics.age', 'diagnosis.diagnosis_id', 'patients.gender_id')
            ->join('patients', 'diagnosis_analytics.patient_id', '=', 'patients.patient_id')
            ->join('diagnosis', 'diagnosis_analytics.diagnosis_id', '=', 'diagnosis.diagnosis_id');
    
        // Apply year, start month, and end month filters if year is provided
        if ($year) {
            $query->whereYear('diagnosis_analytics.created_at', $year);
    
            if ($startMonth && $endMonth) {
                $query->whereMonth('diagnosis_analytics.created_at', '>=', $startMonth)
                      ->whereMonth('diagnosis_analytics.created_at', '<=', $endMonth);
            }
        } else {
            // Only apply month range filter if year is not specified
            if ($startMonth && $endMonth) {
                $query->whereMonth('diagnosis_analytics.created_at', '>=', $startMonth)
                      ->whereMonth('diagnosis_analytics.created_at', '<=', $endMonth);
            }
        }
    
        // Barangay filter if provided
        if ($barangayId) {
            $query->where('diagnosis_analytics.barangay_name', $barangayId);
        }
    
        // Diagnosis filter if selected
        if ($selectedDiagnosis) {
            $diagnosisId = Crypt::decrypt($selectedDiagnosis);
            $query->where('diagnosis_analytics.diagnosis_id', $diagnosisId);
        }
    
        // Ensure the diagnosis type is 2
        $query->where('diagnosis.diagnosis_type', '2');
    
        // Count patients by gender
        $genderCounts = [
            'Male' => 0,
            'Female' => 0,
        ];
    
        $patients = $query->get();
    
        foreach ($patients as $patient) {
            if ($patient->gender_id == 1) {
                $genderCounts['Male']++;
            } elseif ($patient->gender_id == 2) {
                $genderCounts['Female']++;
            }
        }
    
        $data = [
            'series' => array_values($genderCounts),
            'labels' => array_keys($genderCounts),
        ];
    
        return response()->json($data);
    }  

    public function getDiagnosisAnalytics(Request $request)
    {
        // Get the year from the request, default to current year if not provided
        $selectedYear = $request->input('year');
    
        // Query to get the top 10 diagnosis by count for the selected year, or all years if no year is selected
        $dataQuery = DiagnosisAnalytics::with('diagnosis')
            ->join('diagnosis', 'diagnosis_analytics.diagnosis_id', '=', 'diagnosis.diagnosis_id')
            ->selectRaw('diagnosis.diagnosis_name, COUNT(diagnosis_analytics.id) as count')
            ->where('diagnosis.diagnosis_type', 2) // Ensure the diagnosis type is '1'
            ->groupBy('diagnosis_analytics.diagnosis_id', 'diagnosis.diagnosis_name');
    
        // Apply the year filter if a year is selected
        if ($selectedYear) {
            $dataQuery->whereYear('diagnosis_analytics.created_at', $selectedYear);
        }
    
        $dataQuery = $dataQuery->orderByDesc('count')
            ->limit(10)
            ->get();
    
        // Format the data to send to the frontend
        $categories = $dataQuery->pluck('diagnosis_name')->toArray();
        $series = $dataQuery->pluck('count')->toArray();
    
        return response()->json([
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Top 10 Diagnosis',
                    'data' => $series
                ]
            ]
        ]);
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

    public function getDiagnosisCount()
    {
        $totalDiagnosis = DiagnosisAnalytics::join('diagnosis', 'diagnosis_analytics.diagnosis_id', '=', 'diagnosis.diagnosis_id')
            ->where('diagnosis.diagnosis_type', 2)
            ->count();
    
        $totalDiagnosisToday = DiagnosisAnalytics::join('diagnosis', 'diagnosis_analytics.diagnosis_id', '=', 'diagnosis.diagnosis_id')
            ->where('diagnosis.diagnosis_type', 2)
            ->whereDate('diagnosis_analytics.created_at', Carbon::today())
            ->count();
    
        $totalPatientsQueue = ConsultationQueue::count();    
        
        return response()->json([
            'totalDiagnosis' => $totalDiagnosis,
            'totalDiagnosisToday' => $totalDiagnosisToday,
            'totalPatientsQueue' => $totalPatientsQueue

        ]);
    }

    public function getChiefDental(Request $request)
    {
        // Retrieve input values from the request
        $year = $request->input('year');
        $barangayId = $request->input('barangay');
        $selectedComplaint = $request->input('complaint');
        $startMonth = $request->input('startMonth');
        $endMonth = $request->input('endMonth');
    
        // Fetch consultation data with a join on the consultation table to get chief complaints
        $data = DentalAnalytics::join('dentals', 'dental_analytics.dental_id', '=', 'dentals.id')
            ->selectRaw("dentals.chief_complaints, MONTH(dental_analytics.created_at) as month, COUNT(dentals.id) as complaints_count")
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
}