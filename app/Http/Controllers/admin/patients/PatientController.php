<?php

namespace App\Http\Controllers\admin\patients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use App\Models\Gender;
use App\Models\Barangay;
use App\Models\Consultation;
use App\Models\Dental;
use App\Models\CityMunicipality;
use App\Models\Province;
use App\Models\Region;
use App\Models\BloodType;
use App\Models\CivilStatus;
use App\Models\EducationalAttainment;
use App\Models\EmploymentStatus;
use App\Models\FamilyMember;
use App\Models\PhilHealthStatusType;
use App\Models\PhilHealthCategories;
use App\Models\Address;
use App\Models\DeceasedPatient;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Events\ConsultationQueueing;
use App\Events\DentalQueueing;
use App\Models\ConsultationQueue;
use App\Models\DentalQueue;
use Carbon\Carbon;
use App\Models\ConsultationAnalytics;
use App\Models\DentalAnalytics;
use App\Models\Log;
use App\Models\UserProfile;


class PatientController extends Controller
{
    public function index()
    {
        return view('content.admin.patients.patients');
    }

    public function getAddPatients()
    {
        return view('content.admin.patients.addPatients');
    }

    public function getUpdatePatients(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);

            $patient = Patient::with('address')->findOrFail($id);
            $gender = Gender::all();
            // $region = Region::all();

            // $regionId = $request->input('region_id', $patient->address->region_id);
            // $provinceId = $request->input('province_id', $patient->address->province_id);
            // $citymunicipalityId = $request->input('municipality_id', $patient->address->municipality_id);

            // $province = Province::where('regCode', $regionId)->get();
            // $cityMunicipality = CityMunicipality::where('provCode', $provinceId)->get();
            // $barangay = Barangay::where('citymunCode', $citymunicipalityId)->get();

            // Retrieve all user profiles
            $userProfiles = UserProfile::all();

            // Extract the municipality IDs from the user profiles
            $municipalityIds = $userProfiles->pluck('municipality_id')->toArray();

            // Fetch barangays that belong to the extracted municipality IDs
            $barangay = Barangay::whereIn('citymunCode', $municipalityIds)->get();

            // Encrypt the patient_id for use in the view
            $encryptedPatientId = Crypt::encrypt($patient->patient_id);

            return view('content.admin.patients.updatePatients', [
                'patient' => $patient,
                'gender' => $gender,
                'barangay' => $barangay,
                'encryptedPatientId' => $encryptedPatientId,
            ]);
        } catch (DecryptException $e) {
            //return redirect()->route('error.page')->with('message', 'Invalid Encryption');
        }
    }

    public function fetchPatients(Request $request)
    {
        $pageSize = $request->input('pageSize', 10); // Default to 10
        $search = $request->input('search'); // Search input
    
        // Base query for patients with joins
        $query = Patient::select(
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
            ->orderBy('patients.first_name', 'ASC')
            ->orderBy('patients.middle_name', 'ASC')
            ->orderBy('patients.last_name', 'ASC');
    
        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('patients.family_number', 'like', '%' . $search . '%')
                    ->orWhere('patients.first_name', 'like', '%' . $search . '%')
                    ->orWhere('patients.middle_name', 'like', '%' . $search . '%')
                    ->orWhere('patients.last_name', 'like', '%' . $search . '%')
                    ->orWhere('patients.suffix_name', 'like', '%' . $search . '%');
            });
        }
    
        // Paginate results
        $patients = $query->paginate($pageSize);
    
        // Process patient data
        foreach ($patients as $patient) {
            if ($patient->middle_name) {
                $patient->middle_name = substr($patient->middle_name, 0, 1) . '.'; // Shorten middle name
            }
            $patient->encrypted_id = Crypt::encrypt($patient->patient_id); // Encrypt the patient ID
        }
    
        return response()->json([
            'patient' => $patients,
            'userRole' => Auth::user()->role,
        ]);
    }
    

    // Get Patient
    public function getPatients($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $patient = Patient::with('address')
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

            if ($patient) {
                return response()->json([
                    'status' => 200,
                    'patient' => $patient,
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Patient not found',
                ]);
            }
        } catch (DecryptException $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid Encryption',
            ]);
        }
    }

    public function addPatient(Request $request)
    {
        // Validate the input data
        $validatedData = $request->validate([
            'familyNumber' => 'required|string|max:20',
            'firstName' => 'required|string|max:150',
            'lastName' => 'required|string|max:150',
            'middleName' => 'nullable|string|max:150',
            'suffixName' => 'nullable|string|max:50',
            'birthDate' => 'required|date',
            'gender' => 'required',
            'bloodPressure' => 'nullable|string|max:50',
            'bodyTemperature' => 'nullable|string|max:50',
            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'contact' => 'required|string|max:50',
            'chiefComplaintsType' => 'nullable|array',
            'barangay' => 'nullable',
            'selectedBarangay' => 'nullable|string',
        ]);

        // Check if a patient with the same attributes already exists
        $existingPatient = Patient::where('first_name', $validatedData['firstName'])
            ->where('last_name', $validatedData['lastName'])
            ->where('gender_id', $validatedData['gender'])
            ->where('birth_date', $validatedData['birthDate'])
            ->where('family_number', $validatedData['familyNumber'])
            ->first();

        if ($existingPatient) {
            // Patient already exists, return an error response
            return response()->json(['status' => 422, 'error' => 'Patient already enrolled in this RHU.']);
        }

        // Calculate age based on birth_date
        $birthDate = Carbon::parse($validatedData['birthDate']);
        $age = $birthDate->diffInYears(Carbon::now());

        try {
            // Save patient data
            $patient = Patient::create([
                'family_number' => $validatedData['familyNumber'],
                'first_name' => $validatedData['firstName'],
                'last_name' => $validatedData['lastName'],
                'middle_name' => $validatedData['middleName'],
                'suffix_name' => $validatedData['suffixName'],
                'birth_date' => $validatedData['birthDate'],
                'age' => $age,
                'gender_id' => $validatedData['gender'],
                'contact' => $validatedData['contact'],
            ]);

            // Fetch user profiles
            $userProfiles = UserProfile::all();

            // Extract the necessary fields
            $regionId = $userProfiles->pluck('region_id')->first() ?? '08'; // Default to '08' if no region_id found
            $provinceId = $userProfiles->pluck('province_id')->first() ?? '0837'; // Default to '0837' if no province_id found
            $municipalityId = $userProfiles->pluck('municipality_id')->first() ?? '083707'; // Default to '083707' if no municipality_id found
  
            // Save address data
            $addressData = [
                'patient_id' => $patient->patient_id,
                'barangay_id' => $validatedData['barangay'] ?? null,
                'municipality_id' =>  $municipalityId, // Fixed values for demonstration
                'province_id' => $provinceId,
                'region_id' => $regionId,
            ];


            //save to address
            Address::create($addressData);

            $role = Auth::user()->role;

            Log::create([
                'role' => $role,
                'action' => 'Added',
                'description' => "Patient {$patient->first_name} {$patient->last_name} was added to the system.",
            ]);
            
            return response()->json(['status' => 200, 'message' => 'Patient created successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'error' => 'Failed to create patient', 'exception' => $e->getMessage()]);
        }
    }

    // Update Patient
    public function updatePatient(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);

        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'firstName' => 'required|string|max:150',
                'lastName' => 'required|string|max:150',
                'middleName' => 'nullable|string|max:150',
                'suffixName' => 'nullable|string|max:50',
                'gender' => 'required|integer', // Ensuring integer value
                'birthDate' => 'required|date',
                'barangay_id' => 'nullable',
                'contact' => 'required|string|max:50',
            ]);

            // Update patient data
            $patient = Patient::findOrFail($id);

            // Calculate age based on birth_date
            $birthDate = Carbon::parse($validatedData['birthDate']);
            $age = $birthDate->diffInYears(Carbon::now());

            $patient->update([
                'first_name' => $validatedData['firstName'],
                'last_name' => $validatedData['lastName'],
                'middle_name' => $validatedData['middleName'],
                'suffix_name' => $validatedData['suffixName'],
                'gender_id' => $validatedData['gender'],
                'birth_date' => $validatedData['birthDate'],
                'contact' => $validatedData['contact'],
                'age' => $age,
            ]);

            // Check if address already exists for the patient
            $address = Address::where('patient_id', $patient->patient_id)->first();

            // Fetch user profiles
            $userProfiles = UserProfile::all();

            // Extract the necessary fields
            $regionId = $userProfiles->pluck('region_id')->first() ?? '08'; // Default to '08' if no region_id found
            $provinceId = $userProfiles->pluck('province_id')->first() ?? '0837'; // Default to '0837' if no province_id found
            $municipalityId = $userProfiles->pluck('municipality_id')->first() ?? '083707'; // Default to '083707' if no municipality_id found

            if ($address) {
                // Update existing address
                $address->update([
                    'barangay_id' => $validatedData['barangay_id'] ?? null,
                    'municipality_id' => $municipalityId,
                    'province_id' => $provinceId,
                    'region_id' => $regionId,
                ]);
            } else {
                // Create new address if none exists
                Address::create([
                    'patient_id' => $patient->patient_id,
                    'barangay_id' => $validatedData['barangay_id'] ?? null,
                    'municipality_id' => $municipalityId,
                    'province_id' => $provinceId,
                    'region_id' => $regionId,
                ]);
            }
            
            $role = Auth::user()->role;

            Log::create([
                'role' => $role,
                'action' => 'Updated',
                'description' => "Patient {$patient->first_name} {$patient->last_name} details were updated.",
            ]);

            return response()->json(['status' => 200, 'message' => 'Patient updated successfully']);
        } catch (\Exception $e) {
            Log::error('Error updating patient: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error']);
        }
    }

    // Add Patients Users
    public function addPatientsAccount(Request $request, $encryptedId)
    {
        // Decrypt the patient ID
        $patientId = Crypt::decrypt($encryptedId);

        // Validate the request
        $request->validate(
            [
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email, ' . $patientId,
                'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'],
            ],
            [
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least :min characters.',
                'password.regex' => 'Password should contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            ],
        );

        // Create a new user associated with the patient
        $user = User::create([
            'patient_id' => $patientId,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Patient',
            'status' => 'Active',
        ]);

        $patient = Patient::findOrFail($patientId);

        Log::create([
            'role' => 'Admin',
            'action' => 'Created Account',
            'description' => "You have succesfully created a patient account for {$patient->first_name} {$patient->last_name}.",
        ]);

        return response()->json($user);
    }

    // Fetch Genders
    public function fetchGenders()
    {
        $genders = Gender::all();
        return response()->json($genders);
    }

    // Fetch Regions in Select Element
    public function fetchRegions()
    {
        $regions = Region::all();
        return response()->json($regions);
    }

    // Fetch Provinces in Select Element
    public function fetchProvinces(Request $request)
    {
        $regionId = $request->input('region_id');
        $provinces = Province::where('regCode', $regionId)->get();

        return response()->json($provinces);
    }

    // Fetch Municipality in Select Element
    public function fetchCityMunicipality(Request $request)
    {
        $provinceId = $request->input('province_id');
        $cities = CityMunicipality::where('provCode', $provinceId)->get();

        return response()->json($cities);
    }

    // Fetch Barangays in Select Element
    public function fetchBarangays(Request $request)
    {
        // Retrieve all user profiles
        $userProfiles = UserProfile::all();

        // Extract the municipality IDs from the user profiles
        $municipalityIds = $userProfiles->pluck('municipality_id')->toArray();

        // Fetch barangays that belong to the extracted municipality IDs
        $barangays = Barangay::whereIn('citymunCode', $municipalityIds)->get();

        // Return the barangays as JSON response
        return response()->json($barangays);
    }

    public function getFamilyNumber($lastName, $middleName)
    {
        // Find all patients with the same last name and middle name
        $patients = Patient::where('last_name', $lastName)
                            ->where('middle_name', $middleName)
                            ->get();
    
        // If there are patients, return their family numbers
        if ($patients->count() > 0) {
            $familyNumbers = $patients->pluck('family_number');
            return response()->json(['family_numbers' => $familyNumbers]);
        } else {
            // If no patients are found, return a new random family number
            return response()->json(['family_numbers' => [$this->generateRandomID()]]);
        }
    }
    
    

    // Optionally, you can create a helper function to generate a random ID
    private function generateRandomID()
    {
        return rand(100000, 999999);
    }
}