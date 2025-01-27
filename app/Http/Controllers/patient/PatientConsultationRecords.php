<?php

namespace App\Http\Controllers\patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth; 
use App\Models\Consultation;
use App\Models\Dental;
use App\Models\ConsultationDiagnosis;
use App\Models\DentalDiagnosis;

class PatientConsultationRecords extends Controller
{
    public function index()
    {
        return view('content.patient.patientConsultation');
    }

    public function dental()
    {
        return view('content.patient.patientDental');
    }

    public function getConsultationDiagnosis()
    {
        return view('content.patient.patientConsultationDiagnosis');
    }

    public function getDentalDiagnosis()
    {
        return view('content.patient.patientDentalDiagnosis');
    }
    
    public function viewConsultation(Request $request)
    {
        // Set the default page size (number of records per page)
        $pageSize = $request->input('pageSize', 10); 

        // Get the date input from the request (the date from date picker)
        $selectedDate = $request->input('getDate'); 

        // Get the current authenticated patient's ID
        $encryptedPatientId = Auth::user()->patient_id;

        // Fetch consultation records for the patient
        $query = Consultation::where('consultations.patient_id', $encryptedPatientId)
            ->orderBy('consultations.created_at', 'asc');

        // If a date is selected, filter the consultation records based on the date
        if ($selectedDate) {
            $query->whereDate('created_at', $selectedDate);
        }

        // Apply pagination to the query
        $consultationRecords = $query->paginate($pageSize);

        // Return the paginated result as JSON response
        return response()->json([
            'consultationRecords' => $consultationRecords
        ]);
    }

    public function viewDental(Request $request)
    {
        // Set the default page size (number of records per page)
        $pageSize = $request->input('pageSize', 10); 

        // Get the date input from the request (the date from date picker)
        $selectedDate = $request->input('getDate'); 

        // Get the current authenticated patient's ID
        $encryptedPatientId = Auth::user()->patient_id;

        // Fetch consultation records for the patient
        $query = Dental::where('dentals.patient_id', $encryptedPatientId)
            ->orderBy('dentals.created_at', 'asc');

        // If a date is selected, filter the consultation records based on the date
        if ($selectedDate) {
            $query->whereDate('created_at', $selectedDate);
        }

        // Apply pagination to the query
        $consultationRecords = $query->paginate($pageSize);

        // Return the paginated result as JSON response
        return response()->json([
            'consultationRecords' => $consultationRecords
        ]);
    }


    public function viewConsultationDiagnosis(Request $request)
    {
        // Set the default page size (number of records per page)
        $pageSize = $request->input('pageSize', 10); 

        // Get the date input from the request (the date from date picker)
        $selectedDate = $request->input('getDate'); 

        // Get the current authenticated patient's ID
        $encryptedPatientId = Auth::user()->patient_id;

        // Fetch consultation records for the patient with pagination
        $query = ConsultationDiagnosis::where('consultations_diagnosis.patient_id', $encryptedPatientId)
        ->leftJoin('diagnosis', 'diagnosis.diagnosis_id', '=', 'consultations_diagnosis.diagnosis_id') 
        ->select(
            'consultations_diagnosis.created_at',
            'diagnosis.diagnosis_name', 
            'consultations_diagnosis.description' 
        )->orderBy('consultations_diagnosis.created_at', 'asc');

        // If a date is selected, filter the consultation records based on the date
        if ($selectedDate) {
            $query->whereDate('consultations_diagnosis.created_at', $selectedDate);
        }

        // Apply pagination to the query
        $diagnosisRecords = $query->paginate($pageSize);

        // Return the paginated result as JSON response
        return response()->json([
            'diagnosisRecords' => $diagnosisRecords
        ]);
    }

    public function viewDentalDiagnosis(Request $request)
    {
        // Set the default page size (number of records per page)
        $pageSize = $request->input('pageSize', 10); 

        // Get the date input from the request (the date from date picker)
        $selectedDate = $request->input('getDate'); 

        // Get the current authenticated patient's ID
        $encryptedPatientId = Auth::user()->patient_id;

        // Fetch consultation records for the patient with pagination
        $query = DentalDiagnosis::where('dentals_diagnosis.patient_id', $encryptedPatientId)
        ->leftJoin('diagnosis', 'diagnosis.diagnosis_id', '=', 'dentals_diagnosis.diagnosis_id') 
        ->select(
            'dentals_diagnosis.created_at',
            'diagnosis.diagnosis_name', 
            'dentals_diagnosis.description' 
        )->orderBy('dentals_diagnosis.created_at', 'asc');

        // If a date is selected, filter the consultation records based on the date
        if ($selectedDate) {
            $query->whereDate('dentals_diagnosis.created_at', $selectedDate);
        }

        // Apply pagination to the query
        $diagnosisRecords = $query->paginate($pageSize);

        // Return the paginated result as JSON response
        return response()->json([
            'diagnosisRecords' => $diagnosisRecords
        ]);
    }
}