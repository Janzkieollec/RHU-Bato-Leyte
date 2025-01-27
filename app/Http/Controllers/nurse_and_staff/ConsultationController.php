<?php

namespace App\Http\Controllers\nurse_and_staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use App\Models\Consultation;
use App\Models\Address;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Events\ConsultationQueueing;
use App\Models\ConsultationQueue;
use App\Models\ConsultationAnalytics;
use Carbon\Carbon;
use  App\Models\PatientLimit;
use Illuminate\Support\Facades\Http;
// use GuzzleHttp\Client;
use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\ApiException;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Illuminate\Support\Str;
use DB;

class ConsultationController extends Controller
{
    public function index()
    {
        // Fetch the users who are Doctors and Dentists
        $doctorDentistData = User::whereIn('role', ['Doctor'])
            ->select('id', 'username', 'role')
            ->get();
    
        // Fetch the most recent patient limit data for Doctors and Dentists
        $patientLimits = PatientLimit::whereIn('user_id', $doctorDentistData->pluck('id'))
            ->whereDate('created_at', Carbon::today())
            ->latest() // Get the latest record based on created_at or the ID
            ->first(); // Only fetch the first record (latest)
    
        return view('content.nurse_and_staff.consultation.consultation', [
            'patientLimits' => $patientLimits, // Passing the most recent consultation data
        ]);
    }

     // Fetch Patient
     public function fetchPatientsConsultation(Request $request)
     {
         $pageSize = $request->input('pageSize', 10); // Default to 10 if not provided
         $search = $request->input('search'); // get the searchFamilyNumber in data fetchPatients
 
         // Fetch patients with their related data and paginate
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
 
         // If searchTerm is provided, add a where condition for all relevant fields
         if ($search) {
             $query->where(function($q) use ($search) {
                 $q->where('patients.family_number', 'like', '%' . $search . '%')
                 ->orWhere('patients.first_name', 'like', '%' . $search . '%')
                 ->orWhere('patients.middle_name', 'like', '%' . $search . '%')
                 ->orWhere('patients.last_name', 'like', '%' . $search . '%')
                 ->orWhere('patients.suffix_name', 'like', '%' . $search . '%');
             });
         }
 
 
         // Apply pagination after the search filter
         $patients = $query->paginate($pageSize);
 
         // Process patient data (for middle name and encryption)
         foreach ($patients as $patient) {
             if ($patient->middle_name) {
                 $patient->middle_name = substr($patient->middle_name, 0, 1) . '.'; // Shorten middle name
             }
             $patient->encrypted_id = Crypt::encrypt($patient->patient_id); // Encrypt the patient ID
         }
 
         // Return JSON response with paginated data
         return response()->json([
             'patient' => $patients, // Paginated data
         ]);
     }

    public function getPatientConsultation($encryptedId)
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

            return view('content.nurse_and_staff.consultation.addConsultation', [
                'patient' => $patient,
                'encryptedId' => $encryptedId,
                'age' => $age,
            ]);
        } catch (DecryptException $e) {
            return response()->json(['status' => 400, 'message' => 'Invalid Encryption'], 400);
        }
    }

    public function getConsultationRecords(Request $request, $encryptedPatientId)
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

        return view('content.nurse_and_staff.consultation.viewConsultationRecords', [
            'patient' => $patient,
            'age' => $age,
            'encryptedId' => $encryptedId,
        ]);
    }

    public function viewConsultationRecords(Request $request, $encryptedPatientId)
    {
        $patientId = Crypt::decrypt($encryptedPatientId);
    
        // Set the number of entries per page, default to 10
        $perPage = $request->input('pageSize', 10);
    
        // Fetch consultation records for the patient with pagination and order by created_at ascending
        $consultationRecords = Consultation::where('consultations.patient_id', $patientId)
            ->with('patient')
            ->orderBy('created_at', 'asc') // Order by created_at in ascending order
            ->paginate($perPage);
    
        // Process patient data (for middle name and encryption)
        foreach ($consultationRecords as $consultationRecord) {
            $consultationRecord->formattedDate = Carbon::parse($consultationRecord->created_at)->format('F j, Y'); // Format created_at
            $consultationRecord->encrypted_id = Crypt::encrypt($consultationRecord->id); // Encrypt the patient ID
        }
        
        return response()->json([
            'consultationRecords' => $consultationRecords,
        ]);
    }
    

    public function addConsulation(Request $request, $encryptedId)
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
            'numberOfDays11' => 'nullable',
            'numberOfDays12' => 'nullable',
            'otherComplaint' => 'nullable|array',
            'otherComplaintDays' => 'nullable|array',
            'age' => 'nullable|integer',
            'barangay' => 'nullable|string',
            'emergencyDetails' => 'nullable|string',
        ]);

        try {
            $id = Crypt::decrypt($encryptedId);

            // Check if the patient is already in the PatientQueueing table
            $existingPatientQueue = ConsultationQueue::where('patient_id', $id)->whereDate('created_at', Carbon::today())->first();
            $existingConsultation = Consultation::where('patient_id', $id)->whereDate('created_at', Carbon::today())->first();

            if ($existingPatientQueue) {
                // If patient is already queued, return a response without saving consultation or broadcasting
                return response()->json(['status' => 422, 'error' => 'Patient already in queue, consultation not created.']);
            } elseif ($existingConsultation) {
                return response()->json(['status' => 422, 'error' => 'This patient already has a consultation for today.']);
            }

            // Fetch the users who are Doctors and Dentists
            $doctorDentistData = User::whereIn('role', ['Doctor'])
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
                
                // Proceed with saving consultation data
                $complaintDaysMapping = [
                    'Chest Pain' => 'numberOfDays1',
                    'Shortness of Breath' => 'numberOfDays2',
                    'Headache' => 'numberOfDays3',
                    'Fever' => 'numberOfDays4',
                    'Abdominal Pain' => 'numberOfDays5',
                    'Cough' => 'numberOfDays6',
                    'Dizziness' => 'numberOfDays7',
                    'Fatigue' => 'numberOfDays8',
                    'Nausea and Vomiting' => 'numberOfDays9',
                    'Back Pain' => 'numberOfDays10',
                    'Joint Pain' => 'numberOfDays11',
                    'Chest Tightness' => 'numberOfDays12',
                ];

                foreach ($validatedData['chiefComplaintsType'] as $complaint) {
                    $chiefComplaint = $complaint;
                    $numberOfDays = null;

                    // Handle "Others" for consultation
                    if ($complaint === 'Others') {
                        foreach ($validatedData['otherComplaint'] as $key => $otherComplaint) {
                            $numberOfDays = $validatedData['otherComplaintDays'][$key] ?? null;
                            $dataToSave = [
                                'patient_id' => $id,
                                'blood_pressure' => $validatedData['bloodPressure'],
                                'body_temperature' => $validatedData['bodyTemperature'],
                                'height' => $validatedData['height'],
                                'weight' => $validatedData['weight'],
                                'chief_complaints' => $otherComplaint,
                                'number_of_days' => $numberOfDays,
                                'emergency_purposes' => $validatedData['emergencyDetails'],
                            ];

                            // Save consultation data
                            $consultation = Consultation::create($dataToSave);

                            $consultationAnalytics = [
                                'patient_id' => $id,
                                'consultation_id' => $consultation->id,
                                'barangay_name' => $validatedData['barangay'],
                                'age' => $validatedData['age'],
                            ];
                            ConsultationAnalytics::create($consultationAnalytics);
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

                            $consultation = Consultation::create($dataToSave);
                            $consultationAnalytics = [
                                'patient_id' => $id,
                                'consultation_id' => $consultation->id,
                                'barangay_name' => $validatedData['barangay'],
                                'age' => $validatedData['age'],
                            ];
                            ConsultationAnalytics::create($consultationAnalytics);
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

            broadcast(new ConsultationQueueing(
                $patientWithAddress, 
                $encryptedPatientId, 
                $validatedData['emergencyDetails'] ?? null // Pass emergency purposes
            ));
            
            $patientQueue = [
                'patient_id' => $id,
            ];

            ConsultationQueue::create($patientQueue);

            $patient = Patient::findOrFail($id);

            if ($patient) {
                $patient->update([
                    'age' => $validatedData['age'],
                ]);
            }

            return response()->json(['status' => 200, 'message' => 'Consultation created successfuly']);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => 'Failed to create patient']);
        }
    }

    // public function sendCloudSms(Request $request)
    // {
    //     $contact = $request->contact;
    //     $message = $request->message;
    
    //     // CloudSMS API credentials
    //     $appKey = "F36C9FAA2B2A4CD9ADBCBBB5FD547EB7";  // App Key
    //     $appSecret = "evBrP9We9VJNg9OmQ46QRbYjqzRxzgMyaHeMjqSQ";  // App Secret
    
    //     // Make the POST request to CloudSMS API
    //     $response = Http::withBasicAuth($appKey, $appSecret)->post("https://api.cloudsms.io/v1/messages", [
    //         "destination" => $contact,  // Contact number
    //         "message" => $message,      // Message to send
    //         "type" => "sms"             // Type of message, usually "sms" for standard text messages
    //     ]);
    
    //     // Check if the response is successful
    //     if ($response->ok()) {
    //         return response()->json(['status' => 'success', 'data' => $response->json()]);
    //     } else {
    //         return response()->json(['status' => 'error', 'message' => 'Failed to send message', 'error' => $response->json()]);
    //     }
    // }

    public function sendInfobipSMS(Request $request)
    {
        $validated = $request->validate([
            'contact' => 'required|string',
            'message' => 'required|string',
        ]);
    
        // Format the contact number to include the country code
        $contact = ltrim($validated['contact'], '0'); // Remove leading zero
        $formattedContact = '63' . $contact;
    
        $configuration = new Configuration(
            host: '4ej3kp.api.infobip.com',
            apiKey: '14ce4b208c7d7c737168ef7e189a8e04-3d909ea6-e767-489d-8407-12ae18eff443'
        );
    
        $sendSmsApi = new SmsApi(config: $configuration);
    
        $message = new SmsTextualMessage(
            destinations: [
                new SmsDestination(to: $formattedContact) // Use the formatted contact number
            ],
            from: 'RHU-Bato',
            text: $validated['message']
        );
    
        $request = new SmsAdvancedTextualRequest(messages: [$message]);
    
        try {
            $smsResponse = $sendSmsApi->sendSmsMessage($request);
            return response()->json(['status' => 'success', 'message' => 'Message sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}