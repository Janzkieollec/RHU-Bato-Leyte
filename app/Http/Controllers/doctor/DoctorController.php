<?php

namespace App\Http\Controllers\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Consultation;
use Illuminate\Support\Facades\Crypt;
use App\Models\Diagnosis;
use App\Models\Address;
use App\Models\ConsultationDiagnosis;
use App\Models\DiagnosisAnalytics;
use App\Models\Medicines;
use App\Models\ConsultationQueue;
use App\Models\PrescribeMedicines;
use Carbon\Carbon;
use DB;

class DoctorController extends Controller
{
    public function getConsultationPatientsQueue()
    {
        return view('content.doctor.consultation-queue-patient');
    }

    public function getDiagnosisPatients()
    {
        return view('content.doctor.diagnosis.diagnosis');
        
    }

    public function consultationQueueing(Request $request)
    {
        $pageSize = $request->input('pageSize', 10); // Default to 10 if not provided
        $searchFamilyNumber = $request->input('searchFamilyNumber'); // Get the searchFamilyNumber from the request
    
        try {
            // Fetch all patient_ids from the consultation_queueing table where created_at is today
            $patientIds = ConsultationQueue::whereDate('created_at', Carbon::today()) // Ensure only today's records are considered
                ->pluck('patient_id'); // Retrieve all patient_ids
    
            // Modify the query to prioritize emergency consultations
            $query = Patient::select(
                'patients.patient_id',
                'patients.first_name',
                'patients.middle_name',
                'patients.last_name',
                'patients.suffix_name',
                'genders.gender_name as gender',
                'patients.birth_date',
                'patients.contact',
                'patients.family_number',
                'refbrgy.brgyDesc as barangay_name',
                'refcitymun.citymunDesc as municipality_name',
                'refprovince.provDesc as province_name',
                'refregion.regDesc as region_name',
                'consultations.emergency_purposes'
            )
            ->whereIn('patients.patient_id', $patientIds) // Filter patients based on patient_ids from consultation_queueing
            ->leftJoin('addresses', 'patients.patient_id', '=', 'addresses.patient_id')
            ->leftJoin('refbrgy', 'addresses.barangay_id', '=', 'refbrgy.brgyCode')
            ->leftJoin('refcitymun', 'addresses.municipality_id', '=', 'refcitymun.citymunCode')
            ->leftJoin('refprovince', 'addresses.province_id', '=', 'refprovince.provCode')
            ->leftJoin('refregion', 'addresses.region_id', '=', 'refregion.regCode')
            ->leftJoin('genders', 'patients.gender_id', '=', 'genders.gender_id')
            ->leftJoin('consultations', 'patients.patient_id', '=', 'consultations.patient_id')
            ->whereDate('consultations.created_at', Carbon::today())
            ->leftJoin('consultation_queueing', 'patients.patient_id', '=', 'consultation_queueing.patient_id')
            ->whereDate('consultation_queueing.created_at', Carbon::today()) // Ensure filtering by today's records
            ->orderByRaw('consultations.emergency_purposes DESC',) // Prioritize emergency consultations
            ->orderBy('consultation_queueing.created_at', 'desc'); // Order by latest created_at for consultation_queueing
    
            // Apply search filter if searchFamilyNumber is provided
            if ($searchFamilyNumber) {
                $query->where('patients.family_number', 'like', '%' . $searchFamilyNumber . '%');
            }
    
            // Apply pagination to the query
            $patients = $query->paginate($pageSize);
    
            // Process patient data for middle name and encryption
            foreach ($patients as $patient) {
                if ($patient->middle_name) {
                    $patient->middle_name = substr($patient->middle_name, 0, 1) . '.'; // Shorten middle name
                }
                $patient->encrypted_id = Crypt::encrypt($patient->patient_id); // Encrypt the patient ID
            }
    
            // Return the processed patient data as JSON
            return response()->json(['patient' => $patients]);
    
        } catch (DecryptException $e) {
            return response()->json(['status' => 400, 'message' => 'Invalid Encryption'], 400);
        }
    }
    

    
    public function getConsultationDiagnosis($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);

        try {
            // $id = Crypt::decrypt($encryptedId);

            $patient = Patient::with('gender', 'address')
                ->leftJoin('addresses', 'patients.patient_id', '=', 'addresses.patient_id')
                ->leftJoin('refbrgy', 'addresses.barangay_id', '=', 'refbrgy.brgyCode')
                ->leftJoin('refcitymun', 'addresses.municipality_id', '=', 'refcitymun.citymunCode')
                ->leftJoin('refprovince', 'addresses.province_id', '=', 'refprovince.provCode')
                ->leftJoin('refregion', 'addresses.region_id', '=', 'refregion.regCode')
                ->select(
                    'patients.*',
                    'refbrgy.brgyDesc as barangay_name',
                    'refcitymun.citymunDesc as municipality_name',
                    'refprovince.provDesc as province_name',
                )->findOrFail($id);

                
                $consultations = Consultation::select(
                    'chief_complaints', 
                    'blood_pressure', 
                    'body_temperature', 
                    'height', 'weight', 
                    'number_of_days', 
                    'created_at'
                    )->where('patient_id', $id)
                    ->whereDate('created_at', now()) // Only get today's consultations
                    ->get();
                
            
            $birthDate = new \DateTime($patient->birth_date);
            $currentDate = new \DateTime();
            $age = $currentDate->diff($birthDate)->y;
                        
            $encryptedId = Crypt::encrypt($patient->patient_id);
            
            return view('content.doctor.diagnosis.addDiagnosis', [
                'patient' => $patient,
                'consultations' => $consultations,
                'encryptedId' => $encryptedId,
                'age' => $age,
            ]);
        } catch (DecryptException $e){
            return response()->json(['status' => 400, 'message' => 'Invalid Encryption'], 400);
        }
    }

    public function getDiagnosisPatientsRecord(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
    
        $patients = Patient::select(
            'patients.patient_id',
            'patients.first_name',
            'patients.middle_name',
            'patients.last_name',
            'patients.suffix_name',
            'genders.gender_name as gender',
            'patients.birth_date',
            'patients.family_number',
            'refbrgy.brgyDesc as barangay_name',
            'refcitymun.citymunDesc as municipality_name',
            'refprovince.provDesc as province_name',
            'refregion.regDesc as region_name'
        )
        ->leftJoin('genders', 'patients.gender_id', '=', 'genders.gender_id')
        ->leftJoin('addresses', 'patients.patient_id', '=', 'addresses.patient_id')
        ->leftJoin('refbrgy', 'addresses.barangay_id', '=', 'refbrgy.brgyCode')
        ->leftJoin('refcitymun', 'addresses.municipality_id', '=', 'refcitymun.citymunCode')
        ->leftJoin('refprovince', 'addresses.province_id', '=', 'refprovince.provCode')
        ->leftJoin('refregion', 'addresses.region_id', '=', 'refregion.regCode')
        ->join('consultations_diagnosis', 'patients.patient_id', '=', 'consultations_diagnosis.patient_id')
        ->distinct('patients.patient_id') // Ensure unique patient IDs to ensure that duplicate patients are not shown.
        ->paginate($pageSize);
    
        // Process patient data (for middle name and encryption)
        foreach ($patients as $patient) {
            if ($patient->middle_name) {
                $patient->middle_name = substr($patient->middle_name, 0, 1) . '.'; // Shorten middle name
            }
            $patient->encrypted_id = Crypt::encrypt($patient->patient_id); // Encrypt the patient ID
        }
    
        // Return JSON response with paginated data
        return response()->json([
            'patient' => $patients,  // Paginated data
        ]);
    }

    public function getDiagnosisPatientsRecords(Request $request, $encryptedPatientId)
    {
        $patientId = Crypt::decrypt($encryptedPatientId);

        // Fetch the patient information using the relationship
        $patient = Patient::with('gender', 'address')
            ->leftJoin('addresses', 'patients.patient_id', '=', 'addresses.patient_id')
            ->leftJoin('refbrgy', 'addresses.barangay_id', '=', 'refbrgy.brgyCode')
            ->leftJoin('refcitymun', 'addresses.municipality_id', '=', 'refcitymun.citymunCode')
            ->leftJoin('refprovince', 'addresses.province_id', '=', 'refprovince.provCode')
            ->leftJoin('refregion', 'addresses.region_id', '=', 'refregion.regCode')
            ->select(
                'patients.*',
                'refbrgy.brgyDesc as barangay_name',
                'refcitymun.citymunDesc as municipality_name',
                'refprovince.provDesc as province_name'
            )
            ->findOrFail($patientId);

        $birthDate = new \DateTime($patient->birth_date);
        $currentDate = new \DateTime();
        $age = $currentDate->diff($birthDate)->y;

        $encryptedId = Crypt::encrypt($patient->patient_id);

        return view('content.doctor.diagnosis.viewDiagnosis', [
            'patient' => $patient,
            'age' => $age,
            'encryptedId' => $encryptedId,
        ]);
    }
    
    public function getDiagnosisRecords(Request $request, $encryptedPatientId)
    {
        $patientId = Crypt::decrypt($encryptedPatientId);
    
        // Set the number of entries per page, default to 10
        $perPage = $request->input('pageSize', 10);
    
        // Fetch consultation records for the patient with pagination
        $consultationDiagnosisRecords = Consultation::where('consultations.patient_id', $patientId)
            ->leftJoin('consultations_diagnosis', function($join) {
                $join->on('consultations_diagnosis.patient_id', '=', 'consultations.patient_id')
                     ->on(DB::raw('DATE(consultations.created_at)'), '=', DB::raw('DATE(consultations_diagnosis.created_at)'));
            })
            ->leftJoin('diagnosis', 'diagnosis.diagnosis_id', '=', 'consultations_diagnosis.diagnosis_id')
            ->select(
                'consultations.created_at as consultation_created_at',
                'diagnosis.diagnosis_name',
                'consultations_diagnosis.description',
                'consultations.blood_pressure',
                'consultations.body_temperature',
                'consultations.height',
                'consultations.weight',
                DB::raw('DATE(consultations.created_at) as date'), // Extract date only
                DB::raw('GROUP_CONCAT(consultations.chief_complaints SEPARATOR ", ") as chief_complaints') // Combine complaints
            )
            ->whereNotNull('consultations_diagnosis.created_at') // Ensure the diagnosis exists
            ->groupBy('date', 'consultations.created_at', 'diagnosis.diagnosis_name', 'consultations_diagnosis.description', 'consultations.blood_pressure', 'consultations.body_temperature', 'consultations.height', 'consultations.weight')
            ->orderBy('date', 'asc')
            ->paginate($perPage);
    
        // Process patient data (for middle name and encryption)
        foreach ($consultationDiagnosisRecords as $consultationDiagnosisRecord) {
            $consultationDiagnosisRecord->formattedDate = Carbon::parse($consultationDiagnosisRecord->consultation_created_at)->format('F j, Y'); // Format created_at
            $consultationDiagnosisRecord->encrypted_id = Crypt::encrypt($consultationDiagnosisRecord->id); // Encrypt the patient ID
        }
    
        return response()->json([
            'consultationDiagnosisRecords' => $consultationDiagnosisRecords,
        ]);
    }

    
    public function addDiagnosis(Request $request, $encryptedId)
    {
        $validatedData = $request->validate([
            'diagnosis' => 'nullable|array',
            'diagnosis.*' => 'nullable|string|max:255',
            'diagnosis_id' => 'nullable|array',
            'diagnosis_id.*' => 'nullable|string|max:255',
            'icdCode' => 'nullable|array',
            'icdCode.*' => 'nullable|string|max:10',
            'medicines' => 'nullable|array',
            'medicines.*' => 'nullable|string|max:255',
            'medicine_id' => 'nullable|array',
            'medicine_id.*' => 'nullable|string|max:2048',
            'medicineType' => 'nullable|array',
            'medicineType.*' => 'nullable|string|max:255',
            'dosage' => 'nullable|array',
            'dosage.*' => 'nullable|string|max:255',
            'quantity' => 'nullable|array',
            'quantity.*' => 'nullable|string|max:255',
            'frequency' => 'nullable|array',
            'frequency.*' => 'nullable|string|max:255',
            'duration' => 'nullable|array',
            'duration.*' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'followUpDate' => 'nullable'
        ]);
    
        try {
            // Decrypt patient ID
            $id = Crypt::decrypt($encryptedId);
            $diagnosisIds = [];
            $medicinesIds = [];
    
            // Process Diagnosis
            if (!empty($validatedData['diagnosis'])) {
                foreach ($validatedData['diagnosis'] as $index => $diagnosisName) {
                    $diagnosis = Diagnosis::firstOrCreate(
                        ['diagnosis_name' => $diagnosisName],
                        [
                            'diagnosis_code' => $validatedData['icdCode'][$index] ?? null,
                            'diagnosis_type' => 1,
                        ]
                    );
                    $diagnosisIds[] = $diagnosis->diagnosis_id;
                }
            }
    
            // Process Medicines
            if (!empty($validatedData['medicines'])) {
                foreach ($validatedData['medicines'] as $index => $medicineName) {
                    $medicine = Medicines::firstOrCreate(
                        ['medicines_name' => $medicineName],
                        [
                            'medicine_type' => 1,
                        ]
                    );
                    $medicinesIds[] = [
                        'medicine_id' => $medicine->id,
                        'type' => $validatedData['medicineType'][$index] ?? null,
                        'dosage' => $validatedData['dosage'][$index] ?? null,
                        'quantity' => $validatedData['quantity'][$index] ?? null,
                        'frequency' => $validatedData['frequency'][$index] ?? null,
                        'duration' => $validatedData['duration'][$index] ?? null,
                    ];
                }
            }
    
            // Save Diagnosis to ConsultationDiagnosis
            foreach ($diagnosisIds as $diagnosisId) {
                ConsultationDiagnosis::create([
                    'patient_id' => $id,
                    'diagnosis_id' => $diagnosisId,
                    'description' => $validatedData['description'],
                ]);
            }
    
            // Save Medicines to PrescribeMedicines
            foreach ($medicinesIds as $med) {
                PrescribeMedicines::create([
                    'medicine_id' => $med['medicine_id'],
                    'patient_id' => $id,
                    'medication_type' => $med['type'],
                    'dosage' => $med['dosage'],
                    'quantity' => $med['quantity'],
                    'frequency' => $med['frequency'],
                    'duration' => $med['duration'],
                ]);
            }
    
            // Remove patient from queue
            ConsultationQueue::where('patient_id', $id)
                ->whereDate('created_at', Carbon::today()) ->delete();
    
            // Save to DiagnosisAnalytics
            foreach ($diagnosisIds as $diagnosisId) {
                DiagnosisAnalytics::create([
                    'patient_id' => $id,
                    'diagnosis_id' => $diagnosisId,
                    'barangay_name' => $validatedData['barangay'],
                    'age' => $validatedData['age'],
                ]);
            }

            // Schedule the patient for follow-up if a date is provided
            if (!empty($validatedData['followUpDate'])) {
                 // Parse the date to ensure it has the correct time format
                 $followUpDateTime = Carbon::parse($validatedData['followUpDate']);
                 
                ConsultationQueue::create([
                    'patient_id' => $id,
                    'created_at' => $followUpDateTime,
                    'updated_at' => $followUpDateTime,
                ]);
            }
                
            return response()->json(['status' => 200, 'message' => 'Diagnosis and medication successfully added!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'An error occurred while processing the request.']);
        }
    }
    
     
    // Fetch Diagnosis in Select Element
    public function getDiagnosis(Request $request)
    {
        $fetchDiagnosis = Diagnosis::orderBy('diagnosis_name', 'asc')->get();
    
        // Encrypt diagnosis_id for each diagnosis
        $fetchDiagnosis->each(function ($diagnosis) {
            $diagnosis->encrypted_id = Crypt::encrypt($diagnosis->diagnosis_id);
        });
    
        // Return the data directly
        return response()->json($fetchDiagnosis);
    }
    

   //fetch Medicine
   public function fetchMedicine()
   {
        $fetchMedicines = Medicines::all();

        $fetchMedicines->each(function ($medicines) {
            $medicines->encrypted_id =  Crypt::encrypt($medicines->id); // Add encrypted_id
        });

        return response()->json($fetchMedicines);
   }
   
   public function getPrescribedMedicines($patient_id)
    { 
        $id = Crypt::decrypt($patient_id);

        $medicines = DB::table('prescribe_medicines')
            ->join('medicines', 'prescribe_medicines.medicine_id', '=', 'medicines.id')
            ->where('prescribe_medicines.patient_id', $id)
            ->whereDate('prescribe_medicines.created_at', '=', now()->toDateString()) // Filters for today's date
            ->select(
                'medicines.medicines_name',
                'prescribe_medicines.medication_type'
            )
            ->get();
    
        if ($medicines->isEmpty()) {
            return response()->json(['status' => 404, 'message' => 'No medicines found for this patient today.']);
        }
    
        return response()->json(['status' => 200, 'data' => $medicines]);
    }    

    public function printPrescription($patient_id)
    {
        $id = Crypt::decrypt($patient_id);
        
        // Fetch the patient
        $patient = Patient::findOrFail($id);
        
        // Fetch prescribed medicines with correct filter for today's date
        $medicines = DB::table('prescribe_medicines')
            ->join('medicines', 'prescribe_medicines.medicine_id', '=', 'medicines.id')
            ->where('prescribe_medicines.patient_id', $id)
            ->whereDate('prescribe_medicines.created_at', '=', now()->toDateString())
            ->select(
                'prescribe_medicines.medicine_id',
                'medicines.medicines_name',
                'prescribe_medicines.medication_type',
                'prescribe_medicines.dosage',
                'prescribe_medicines.quantity',
                'prescribe_medicines.frequency',
                'prescribe_medicines.duration',
                'prescribe_medicines.created_at'
            )
            ->get();
        
        // Retrieve patient with address details
        $patientWithAddress = Patient::with('address')
            ->leftJoin('addresses', 'patients.patient_id', '=', 'addresses.patient_id')
            ->leftJoin('refbrgy', 'addresses.barangay_id', '=', 'refbrgy.brgyCode')
            ->leftJoin('refcitymun', 'addresses.municipality_id', '=', 'refcitymun.citymunCode')
            ->leftJoin('refprovince', 'addresses.province_id', '=', 'refprovince.provCode')
            ->leftJoin('refregion', 'addresses.region_id', '=', 'refregion.regCode')
            ->select('patients.*', 'refbrgy.brgyDesc as barangay_name', 'refcitymun.citymunDesc as municipality_name', 'refprovince.provDesc as province_name')
            ->where('patients.patient_id', $id)
            ->first();
        
        return view('content.doctor.diagnosis.prescribeMedicines', compact('patient', 'medicines', 'patientWithAddress'));
    }

}   