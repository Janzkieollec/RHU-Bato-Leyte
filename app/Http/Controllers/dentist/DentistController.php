<?php

namespace App\Http\Controllers\dentist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Dental;
use App\Models\Diagnosis;
use App\Models\Medicines;
use App\Models\DentalDiagnosis;
use App\Models\DentalQueue;
use App\Models\PrescribeMedicines;
use App\Models\Address;
use App\Models\DiagnosisAnalytics;
use Illuminate\Support\Facades\Crypt;
use DB;
use Carbon\Carbon;


class DentistController extends Controller
{
    public function getDentalPatientsQueue()
    {
        return view('content.dentist.dental-queue-patient');
    }
    public function getDiagnosisPatients()
    {
        return view('content.dentist.diagnosis.diagnosis');
    }

    public function dentalQueueing(Request $request)
    {
        $pageSize = $request->input('pageSize', 10); // Default to 10 if not provided
        $searchFamilyNumber = $request->input('searchFamilyNumber'); // Get the searchFamilyNumber from the request
    
        try {
            // Fetch all patient_ids from the dental_queueing table
            $patientIds = DentalQueue::pluck('patient_id'); // Retrieve all patient_ids
    
            // Start the query to fetch patient information for those patient_ids using relationships
            $query = Patient::select(
                    'patients.patient_id',
                    'patients.first_name',
                    'patients.middle_name',
                    'patients.last_name',
                    'patients.suffix_name',
                    'genders.gender_name as gender',
                    'patients.birth_date',
                    'patients.family_number',
                    'patients.contact',
                    'refbrgy.brgyDesc as barangay_name',
                    'refcitymun.citymunDesc as municipality_name',
                    'refprovince.provDesc as province_name',
                    'refregion.regDesc as region_name',
                    'dentals.emergency_purposes'
                )
                ->whereIn('patients.patient_id', $patientIds) // Filter patients based on patient_ids from dental_queueing
                ->leftJoin('addresses', 'patients.patient_id', '=', 'addresses.patient_id')
                ->leftJoin('refbrgy', 'addresses.barangay_id', '=', 'refbrgy.brgyCode')
                ->leftJoin('refcitymun', 'addresses.municipality_id', '=', 'refcitymun.citymunCode')
                ->leftJoin('refprovince', 'addresses.province_id', '=', 'refprovince.provCode')
                ->leftJoin('refregion', 'addresses.region_id', '=', 'refregion.regCode')
                ->leftJoin('genders', 'patients.gender_id', '=', 'genders.gender_id')
                ->leftJoin('dentals', 'patients.patient_id', '=', 'dentals.patient_id')
                ->leftJoin('dental_queueing', 'patients.patient_id', '=', 'dental_queueing.patient_id')
                ->orderByRaw('dentals.emergency_purposes DESC') 
                ->orderBy('dental_queueing.created_at', 'asc')
                ->whereDate('dental_queueing.created_at', Carbon::today()) // Sort by created_at in ascending order from dental_queueing
                ->whereDate('dentals.created_at', Carbon::today()); // Sort by created_at in ascending order from dental_queueing
    
            // If search parameter is provided, apply the search filter
            if ($searchFamilyNumber) {
                $query->where('patients.family_number', 'like', '%' . $searchFamilyNumber . '%');
            }
    
            // Apply pagination to the query
            $patients = $query->paginate($pageSize);
    
            // Process the patient data (shorten middle name and encrypt patient ID)
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

    public function getDiagnosisRecords(Request $request, $encryptedPatientId)
    {
        $patientId = Crypt::decrypt($encryptedPatientId);
    
        // Set the number of entries per page, default to 10
        $perPage = $request->input('pageSize', 10);
    
        // Fetch consultation records for the patient with pagination
        $dentalDiagnosisRecords = Dental::where('dentals.patient_id', $patientId)
            ->leftJoin('dentals_diagnosis', function($join) {
                $join->on('dentals_diagnosis.patient_id', '=', 'dentals.patient_id')
                     ->on(DB::raw('DATE(dentals.created_at)'), '=', DB::raw('DATE(dentals_diagnosis.created_at)'));
            })
            ->leftJoin('diagnosis', 'diagnosis.diagnosis_id', '=', 'dentals_diagnosis.diagnosis_id')
            ->select(
                'dentals.created_at as dentals_created_at',
                'diagnosis.diagnosis_name',
                'dentals_diagnosis.description',
                'dentals.blood_pressure',
                'dentals.body_temperature',
                'dentals.height',
                'dentals.weight',
                DB::raw('DATE(dentals.created_at) as date'), // Extract date only
                DB::raw('GROUP_CONCAT(dentals.chief_complaints SEPARATOR ", ") as chief_complaints') // Combine complaints
            )
            ->whereNotNull('dentals_diagnosis.created_at') // Ensure the diagnosis exists
            ->groupBy('date', 'dentals.created_at', 'diagnosis.diagnosis_name', 'dentals_diagnosis.description', 'dentals.blood_pressure', 'dentals.body_temperature', 'dentals.height', 'dentals.weight')
            ->orderBy('date', 'asc')
            ->paginate($perPage);
    
        // Process patient data (for middle name and encryption)
        foreach ($dentalDiagnosisRecords as $dentalDiagnosisRecord) {
            $dentalDiagnosisRecord->formattedDate = Carbon::parse($dentalDiagnosisRecord->dentals_created_at)->format('F j, Y'); // Format created_at
            $dentalDiagnosisRecord->encrypted_id = Crypt::encrypt($dentalDiagnosisRecord->id); // Encrypt the patient ID
        }
    
        return response()->json([
            'dentalDiagnosisRecords' => $dentalDiagnosisRecords,
        ]);
    }
    

    public function getDiagnosis($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);

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

            // Get all consultations for the patient
            $dentals = Dental::select(
                'chief_complaints', 
                'blood_pressure', 
                'body_temperature', 
                'height', 
                'weight',
                'number_of_days', 
            )->where('patient_id', $id)
            ->whereDate('created_at', now()) // Only get today's dental
            ->get(); // Get all records
            
            $birthDate = new \DateTime($patient->birth_date);
            $currentDate = new \DateTime();
            $age = $currentDate->diff($birthDate)->y;
                        
            $encryptedId = Crypt::encrypt($patient->patient_id);

            return view('content.dentist.diagnosis.addDiagnosis', [
                'patient' => $patient,
                'dentals' => $dentals,
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
        ->join('dentals_diagnosis', 'patients.patient_id', '=', 'dentals_diagnosis.patient_id')
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

       // Fetch consultation records for the patient with pagination
        $dentalDiagnosisRecords = DentalDiagnosis::where('dentals_diagnosis.patient_id', $patientId)
        ->leftJoin('diagnosis', 'diagnosis.diagnosis_id', '=', 'dentals_diagnosis.diagnosis_id') 
        ->select(
            'dentals_diagnosis.created_at',
            'diagnosis.diagnosis_name', 
            'dentals_diagnosis.description' 
        )
        ->get(); 



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

        return view('content.dentist.diagnosis.viewDiagnosis', [
            'dentalDiagnosisRecords' => $dentalDiagnosisRecords,
            'patient' => $patient,
            'age' => $age,
            'encryptedId' => $encryptedId,
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
                            'medicine_type' => 2,
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
                DentalDiagnosis::create([
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
    
            DentalQueue::where('patient_id', $id)
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

            if (!empty($validatedData['followUpDate'])) {
                // Parse the date to ensure it has the correct time format
                $followUpDateTime = Carbon::parse($validatedData['followUpDate']);
            
                DentalQueue::create([
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
    
        // Fetch prescribed medicines with correct filter for today's date and join consultations_diagnosis by patient_id
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
    
        return view('content.dentist.diagnosis.prescribeMedicines', compact('patient', 'medicines', 'patientWithAddress'));
    }   
}