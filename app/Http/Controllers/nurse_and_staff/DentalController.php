<?php

namespace App\Http\Controllers\nurse_and_staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use App\Models\PatientLimit;
use App\Models\Dental;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Events\DentalQueueing;
use App\Models\DentalQueue;
use App\Models\DentalAnalytics;
use Carbon\Carbon;

class DentalController extends Controller
{
    public function index()
    {
         // Fetch the users who are Doctors and Dentists
         $doctorDentistData = User::whereIn('role', ['Dentist'])
         ->select('id', 'username', 'role')
         ->get();
 
     // Fetch the most recent patient limit data for Doctors and Dentists
     $patientLimits = PatientLimit::whereIn('user_id', $doctorDentistData->pluck('id'))
         ->whereDate('created_at', Carbon::today())
         ->latest() // Get the latest record based on created_at or the ID
         ->first(); // Only fetch the first record (latest)
        return view('content.nurse_and_staff.dental.dental', [
            'patientLimits' => $patientLimits
        ]);
    }

    public function getPatientDental($encryptedId)
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
                    'refprovince.provDesc as province_name'
                )->findOrFail($id);

            $birthDate = new \DateTime($patient->birth_date);
            $currentDate = new \DateTime();
            $age = $currentDate->diff($birthDate)->y;

            $encryptedId = Crypt::encrypt($patient->patient_id);

            return view('content.nurse_and_staff.dental.addDental', [
                'patient' => $patient,
                'encryptedId' => $encryptedId,
                'age' => $age,
            ]);
        } catch (DecryptException $e) {
            return response()->json(['status' => 400, 'message' => 'Invalid Encryption'], 400);
        }
    }

    public function getDentalRecords(Request $request, $encryptedPatientId)
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
            )->findOrFail($patientId);

        $patient->birth_date = Carbon::parse($patient->birth_date)->format('F j, Y');

        $birthDate = new \DateTime($patient->birth_date);
        $currentDate = new \DateTime();
        $age = $currentDate->diff($birthDate)->y;

        $encryptedId = Crypt::encrypt($patient->patient_id);

        return view('content.nurse_and_staff.dental.viewDentalRecords', [
            'patient' => $patient,
            'age' => $age,
            'encryptedId' => $encryptedId,
        ]);
    }

    public function viewDentalRecords(Request $request, $encryptedPatientId)
    {
        $patientId = Crypt::decrypt($encryptedPatientId);
    
        // Set the number of entries per page, default to 10
        $perPage = $request->input('pageSize', 10);
    
        // Fetch consultation records for the patient with pagination and order by created_at ascending
        $dentalRecords = Dental::where('dentals.patient_id', $patientId)
            ->with('patient')
            ->orderBy('created_at', 'asc') // Order by created_at in ascending order
            ->paginate($perPage);
    
        // Process patient data (for middle name and encryption)
        foreach ($dentalRecords as $dentalRecord) {
            $dentalRecord->formattedDate = Carbon::parse($dentalRecord->created_at)->format('F j, Y'); // Format created_at
            $dentalRecord->encrypted_id = Crypt::encrypt($dentalRecord->id); // Encrypt the patient ID
        }
        
        return response()->json([
            'dentalRecords' => $dentalRecords,
        ]);
    }

    public function fetchPatientsDental(Request $request)
    {
        $pageSize = $request->input('pageSize', 10); // Default to 10 if not provided
        $searchFamilyNumber = $request->input('searchFamilyNumber'); // get the searchFamilyNumber in data fetchPatients

        // Start building the query
        $query = Patient::select(
            'patients.patient_id', 
            'patients.first_name', 
            'patients.middle_name', 
            'patients.last_name', 
            'patients.contact', 
            'genders.gender_name as gender', 
            'patients.birth_date', 'patients.family_number', 
            'refbrgy.brgyDesc as barangay_name', 
            'refcitymun.citymunDesc as municipality_name', 
            'refprovince.provDesc as province_name', 
            'refregion.regDesc as region_name')
        ->leftJoin('genders', 'patients.gender_id', '=', 'genders.gender_id')
        ->leftJoin('addresses', 'patients.patient_id', '=', 'addresses.patient_id')
        ->leftJoin('refbrgy', 'addresses.barangay_id', '=', 'refbrgy.brgyCode')
        ->leftJoin('refcitymun', 'addresses.municipality_id', '=', 'refcitymun.citymunCode')
        ->leftJoin('refprovince', 'addresses.province_id', '=', 'refprovince.provCode')
        ->leftJoin('refregion', 'addresses.region_id', '=', 'refregion.regCode')
        ->orderBy('patients.first_name', 'ASC') // Order by first_name ascending
        ->orderBy('patients.middle_name', 'ASC') // Order by middle_name ascending
        ->orderBy('patients.last_name', 'ASC'); // Order by last_name ascending

        // If search parameter is provided, add a where condition
        if ($searchFamilyNumber) {
            $query->where('patients.family_number', 'like', '%' . $searchFamilyNumber . '%');
        }

        // Apply pagination after the search filter
        $patients = $query->paginate($pageSize);

        // Process patient data
        foreach ($patients as $patient) {
            if ($patient->middle_name) {
                $patient->middle_name = substr($patient->middle_name, 0, 1) . '.'; // Format middle name
            }
            $patient->encrypted_id = Crypt::encrypt($patient->patient_id); // Encrypt patient id
        }

        // Return JSON response
        return response()->json([
            'patient' => $patients,
        ]);
    }

    public function addDental(Request $request, $encryptedId)
    {
        $validatedData = $request->validate([
            'bloodPressure' => 'nullable|string|max:50',
            'bodyTemperature' => 'nullable|string|max:50',
            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'chiefComplaintsType' => 'nullable|array',
            'numberOfDays1' => 'nullable',
            'numberOfDays2' => 'nullable',
            'numberOfDays3' => 'nullable',
            'numberOfDays4' => 'nullable',
            'numberOfDays5' => 'nullable',
            'numberOfDays6' => 'nullable',
            'numberOfDays7' => 'nullable',
            'numberOfDays8' => 'nullable',
            'numberOfDays9' => 'nullable',
            'numberOfDays10' => 'nullable',
            'otherDental' => 'nullable|array',
            'otherDentalDays' => 'nullable|array',
            'age' => 'nullable|integer',
            'barangay' => 'nullable|string',
            'emergencyDetails' => 'nullable|string',
        ]);

        try {
            $id = Crypt::decrypt($encryptedId);

            // Check if the patient is already in the PatientQueueing table
            $existingPatientQueue = DentalQueue::where('patient_id', $id)->whereDate('created_at', Carbon::today())->first();

            $existingDental = Dental::where('patient_id', $id)->whereDate('created_at', now())->first();
            
            if ($existingPatientQueue) {
                // If patient is already queued, return a response without saving consultation or broadcasting
                return response()->json(['status' => 422, 'error' => 'Patient already in queue, dental consultation not created.']);
            } elseif ($existingDental) {
                return response()->json(['status' => 422, 'error' => 'This patient already has a dental consultation for today.']);
            }
            
            // Fetch the users who are Doctors and Dentists
            $doctorDentistData = User::whereIn('role', ['Dentist'])
                ->select('id', 'username', 'role')
                ->get();

            // Fetch the most recent patient limit data for Doctors and Dentists
            $patientLimit = PatientLimit::whereIn('user_id', $doctorDentistData->pluck('id'))
                ->whereDate('created_at', Carbon::today())
                ->latest() // Get the latest record based on created_at or the ID
                ->first(); // Only fetch the first record (latest)

            if (!$patientLimit) {
                return response()->json(['status' => 422, 'error' => 'Patient limit not set for this user.']);
            }

            // Check if emergency purpose is provided
            $emergencyPurpose = isset($validatedData['emergencyDetails']) && !empty($validatedData['emergencyDetails']);

            if (!$emergencyPurpose && $patientLimit->current_patients >= $patientLimit->max_patients) {
                return response()->json(['status' => 422, 'error' => 'Maximum patient limit reached.']);
            }

              // If emergency purpose is provided, skip patient limit check
            if ($patientLimit->current_patients < $patientLimit->max_patients || $emergencyPurpose) {
                // Increment current_patients only if emergency purpose is not provided
                if (!$emergencyPurpose) {
                    $patientLimit->increment('current_patients');
                }
                
                $complaintDaysMapping = [
                    'Toothache' => 'numberOfDays1',
                    'Gum Swelling/Bleeding' => 'numberOfDays2',
                    'Cavities' => 'numberOfDays3',
                    'Broken/Chipped Tooth' => 'numberOfDays4',
                    'Sensitivity' => 'numberOfDays5',
                    'Bad Breath (Halitosis)' => 'numberOfDays6',
                    'Loose Tooth' => 'numberOfDays7',
                    'Jaw Pain' => 'numberOfDays8',
                    'Dry Mouth (Xerostomia)' => 'numberOfDays9',
                    'Discoloration of Teeth' => 'numberOfDays10',
                ];

                foreach ($validatedData['chiefComplaintsType'] as $complaint) {
                    $chiefComplaint = $complaint;
                    $numberOfDays = null;

                    // Handle "Others" for consultation
                    if ($complaint === 'Others') {
                        foreach ($validatedData['otherDental'] as $key => $otherDental) {
                            $numberOfDays = $validatedData['otherDentalDays'][$key] ?? null; // Get the corresponding number of days
                            $dataToSave = [
                                'patient_id' => $id,
                                'blood_pressure' => $validatedData['bloodPressure'],
                                'body_temperature' => $validatedData['bodyTemperature'],
                                'height' => $validatedData['height'],
                                'weight' => $validatedData['weight'],
                                'chief_complaints' => $otherDental,
                                'number_of_days' => $numberOfDays,
                                'emergency_purposes' => $validatedData['emergencyDetails'],
                            ];

                            // Save dental data
                            $dental = Dental::create($dataToSave);
                            $dentalAnalytics = [
                                'patient_id' => $id,
                                'dental_id' => $dental->id,
                                'barangay_name' => $validatedData['barangay'],
                                'age' => $validatedData['age'],
                            ];
                            DentalAnalytics::create($dentalAnalytics);
                        }
                    } else {
                        // Handle predefined complaints
                        $numberOfDays = $validatedData[$complaintDaysMapping[$complaint] ?? ''] ?? null;
                        if ($chiefComplaint) {
                            $dataToSave = [
                                'patient_id' => $id,
                                'blood_pressure' => $validatedData['bloodPressure'],
                                'body_temperature' => $validatedData['bodyTemperature'],
                                'height' => $validatedData['height'],
                                'weight' => $validatedData['weight'],
                                'chief_complaints' => $chiefComplaint,
                                'number_of_days' => $numberOfDays,
                                'emergency_purposes' => $validatedData['emergencyDetails'],
                            ];

                            $dental = Dental::create($dataToSave);
                            $dentalAnalytics = [
                                'patient_id' => $id,
                                'dental_id' => $dental->id,
                                'barangay_name' => $validatedData['barangay'],
                                'age' => $validatedData['age'],
                            ];
                            DentalAnalytics::create($dentalAnalytics);
                        }
                    }
                }
            }

            // Retrieve patient with address details
            $patientWithAddress = Patient::with('address')
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
                ->where('patients.patient_id', $id)->first();
                
            $encryptedPatientId = Crypt::encrypt($id);

            broadcast(new DentalQueueing(
                $patientWithAddress, 
                $encryptedPatientId,
                $validatedData['emergencyDetails'] ?? null // Pass emergency purposes
            ));

            $patientQueue = [
                'patient_id' => $id,
            ];

            DentalQueue::create($patientQueue);

            return response()->json(['status' => 200, 'message' => 'Dental created successfuly']);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => 'Failed to create patient']);
        }
    }
}