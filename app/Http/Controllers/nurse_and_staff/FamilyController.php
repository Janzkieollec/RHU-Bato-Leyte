<?php

namespace App\Http\Controllers\nurse_and_staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FamilyPlanning;
use App\Models\Patient;
use App\Models\FamilyMethodUsed;
use App\Models\Address;
use App\Models\Gender;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;


class FamilyController extends Controller
{
    public function index(){

        return view('content.nurse_and_staff.planning.planning');

    }
    
    public function getAddPlanning()
    {
        return view('content.nurse_and_staff.planning.addPlanning');
    }

    public function addNewPlanning(Request $request, $encryptedId)
    {
        $patientId = Crypt::decrypt($encryptedId);
        
        // Fetch the patient information using the relationship
        $patient = Patient::select(
            'patients.*',
            'refbrgy.brgyDesc as barangay_name',
            'refcitymun.citymunDesc as municipality_name',
            'refprovince.provDesc as province_name',
        )
        ->leftJoin('addresses', 'patients.patient_id', '=', 'addresses.patient_id')
        ->leftJoin('refbrgy', 'addresses.barangay_id', '=', 'refbrgy.brgyCode')
        ->leftJoin('refcitymun', 'addresses.municipality_id', '=', 'refcitymun.citymunCode')
        ->leftJoin('refprovince', 'addresses.province_id', '=', 'refprovince.provCode')
        ->leftJoin('refregion', 'addresses.region_id', '=', 'refregion.regCode')
        ->findOrFail($patientId);

        $patient->birth_date = Carbon::parse($patient->birth_date)->format('F j, Y');

        $birthDate = new \DateTime($patient->birth_date);
        $currentDate = new \DateTime();
        $age = $currentDate->diff($birthDate)->y;
                  
        $encryptedId = Crypt::encrypt($patient->patient_id);

        return view('content.nurse_and_staff.planning.addNew', [
            'planning' => $patient,
            'age' => $age,
            'encryptedId' => $encryptedId,
        ]);
    
    }
    
    public function getPlanningRecords(Request $request, $encryptedId)
    {
        $planningId = Crypt::decrypt($encryptedId);
       
        // Fetch consultation records for the implant
        $planningRecords = FamilyMethodUsed::select(
                'family_method_used.patient_id',
                'family_method_used.created_at',
                'family_method_used.quantity',
                'family_method_used.fp_method_used'
            )
            ->join('patients', 'family_method_used.patient_id', '=', 'patients.patient_id')
            ->where('family_method_used.patient_id', $planningId)
            ->orderBy('family_method_used.created_at', 'ASC') // Order by last_name ascending
            ->get();
                
        // Fetch patient details
        $patient = Patient::select(
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
            'refregion.regDesc as region_name'
        )
        ->leftJoin('genders', 'patients.gender_id', '=', 'genders.gender_id')
        ->leftJoin('addresses', 'patients.patient_id', '=', 'addresses.patient_id')
        ->leftJoin('refbrgy', 'addresses.barangay_id', '=', 'refbrgy.brgyCode')
        ->leftJoin('refcitymun', 'addresses.municipality_id', '=', 'refcitymun.citymunCode')
        ->leftJoin('refprovince', 'addresses.province_id', '=', 'refprovince.provCode')
        ->leftJoin('refregion', 'addresses.region_id', '=', 'refregion.regCode')
        ->where('patients.patient_id', $planningId)  // Ensure we're fetching the specific patient
        ->first();  // Use first() to get the specific patient

        // Format birth_date using Carbon
        if ($patient) {
            $patient->birth_date = Carbon::parse($patient->birth_date)->format('F j, Y');  // Format birth date
        }

        // Fetch implant age from subdermal_implant table
        $planning = FamilyPlanning::where('patient_id', $planningId)->first();
        $age = $planning ? $planning->age : null; // Get the age from the Implant table if available

        return view('content.nurse_and_staff.planning.viewPlanning', [
            'planningRecords' => $planningRecords,
            'patient' => $patient,
            'age' => $age,
            'encryptedId' => $encryptedId
        ]);
    }
    
     // Fetch Patient
     public function fetchPlanning(Request $request)
     {
        // Default to 10 items per page if not provided
        $pageSize = $request->input('pageSize', 10); 
        $search = $request->input('search'); // Default search to an empty string if not provided

        // Build the query to fetch patients and related data
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
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('patients.family_number', 'like', '%' . $search . '%')
                ->orWhere('patients.first_name', 'like', '%' . $search . '%')
                ->orWhere('patients.middle_name', 'like', '%' . $search . '%')
                ->orWhere('patients.last_name', 'like', '%' . $search . '%')
                ->orWhere('patients.suffix_name', 'like', '%' . $search . '%');
            });
        }

        // Paginate the query
        $patients = $query->paginate($pageSize);

        // Process patient data for middle name and encryption
        $patients->getCollection()->transform(function ($patient) {
            if ($patient->middle_name) {
                $patient->middle_name = substr($patient->middle_name, 0, 1) . '.'; // Shorten middle name
            }
            $patient->encrypted_id = Crypt::encrypt($patient->patient_id); // Encrypt the patient ID
            return $patient;
        });

        // Return response as JSON
        return response()->json([
            'planning' => $patients, // Paginated data
        ]);
     }
     
    
    public function addNewPlannings(Request $request, $encryptedId)
    {
        $validatedData = $request->validate([
            'barangay' => 'nullable',
            'dswdNhts' => 'required|boolean',
            'methodUsedType' => 'nullable|array',
            'dossage' => 'nullable|integer',
            'pack' => 'nullable|integer',
            'packs' => 'nullable|integer',
            'condom-packs' => 'nullable|integer',
            'age' => 'nullable|integer',
        ]);
    
        
        try {

            $id = Crypt::decrypt($encryptedId);
            
            // Find or create the Implant record
            $implant = FamilyPlanning::updateOrCreate(
                ['patient_id' => $id], // Matching condition
                ['age' => $validatedData['age']] // Update or insert data
            );

            // Update or create the address record
            Address::updateOrCreate(
                ['patient_id' => $id], // Matching condition
                [
                    'barangay_id' => $validatedData['barangay'] ?? null,
                    'municipality_id' => "083707",
                    'province_id' => "0837",
                    'region_id' => "08",
                ]
            );
            
            // Map methods to input fields
            $methodMapping = [
                'DMPA' => 'dossage',
                'Pills-COC' => 'pack',
                'Pills-POP' => 'packs',
                'Condoms' => 'condom-packs',
            ];
    
            // Save Family Method Used for each selected method
            if (!empty($validatedData['methodUsedType'])) {
                foreach ($validatedData['methodUsedType'] as $method) {
                    FamilyMethodUsed::create([
                        'patient_id' => $id,
                        'fp_method_used' => $method,
                        'quantity' => $validatedData[$methodMapping[$method]] ?? null,
                        'nhts_non-nhts' => $validatedData['dswdNhts'], // Ensure this field is included
                    ]);
                }
            }
    
            return response()->json(['status' => 200, 'message' => 'Family Planning created successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'error' => 'Failed to create family planning', 'exception' => $e->getMessage()]);
        }
    } 

    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            FamilyPlanning::where('patient_id', $id)->delete();
            FamilyMethodUsed::where('patient_id', $id)->delete();

            return response()->json(['success' => true ]);
        } catch (DecryptException $e) {
            return response()->json(['success' => false ]);
        }
    }
}   