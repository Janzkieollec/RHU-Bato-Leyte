<?php

namespace App\Http\Controllers\nurse_and_staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Implant;
use App\Models\ImplantMethodUsed;
use App\Models\Address;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;


class ImplantController extends Controller
{
    public function index()
    {
        return view('content.nurse_and_staff.implant.implant');
    }


    public function getNewAddImplant(Request $request, $encryptedId)
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

        return view('content.nurse_and_staff.implant.addNewImplant', [
            'patient' => $patient,
            'age' => $age,
            'encryptedId' => $encryptedId,
        ]);
    
    }
    
    public function fetchImplant(Request $request)
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
        ->where('genders.gender_name', 'Female') // Filter for female gender
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
            'patient' => $patients, // Paginated data
        ]);
    }


    public function getImplant(Request $request, $encryptedId)
    {
        $implantId = Crypt::decrypt($encryptedId);
    
        // Fetch consultation records for the implant
        $implantRecords = ImplantMethodUsed::select(
            'subdermal_implant_method_used.patient_id',
            'subdermal_implant_method_used.created_at',
            'subdermal_implant_method_used.no_of_children',
            'subdermal_implant_method_used.name_of_provider',
            'subdermal_implant_method_used.type_of_provider',
            'subdermal_implant_method_used.fp_unmet_method_used',
            'subdermal_implant_method_used.previous_fp_method',
            'subdermal_implant_method_used.quantity'
        )
        ->join('patients', 'subdermal_implant_method_used.patient_id', '=', 'patients.patient_id')
        ->where('subdermal_implant_method_used.patient_id', $implantId)
        ->orderBy('subdermal_implant_method_used.created_at', 'ASC') // Order by last_name ascending
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
        ->where('patients.patient_id', $implantId)  // Ensure we're fetching the specific patient
        ->first();  // Use first() to get the specific patient
    
        // Format birth_date using Carbon
        if ($patient) {
            $patient->birth_date = Carbon::parse($patient->birth_date)->format('F j, Y');  // Format birth date
        }
    
        // Fetch implant age from subdermal_implant table
        $implant = Implant::where('patient_id', $implantId)->first();
        $age = $implant ? $implant->age : null; // Get the age from the Implant table if available
    
        // Format created_at for each implant record
        foreach ($implantRecords as $record) {
            $record->formattedDate = Carbon::parse($record->created_at)->format('F j, Y');  // Format created_at
            $record->fp_unmet_method_used = $record->fp_unmet_method_used == 1 ? 'Limiting' : 'Spacing';
        }
    
        return view('content.nurse_and_staff.implant.viewImplant', [
            'implantRecords' => $implantRecords,
            'patient' => $patient,
            'age' => $age,
            'encryptedId' => $encryptedId,
        ]);
    }
    
    
    public function addNewImplant(Request $request, $encryptedId)
    {
        // Validate input data
        $validatedData = $request->validate([
            'barangay' => 'nullable|string',
            'contact' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:15'],
            'no_of_children' => 'nullable|integer',
            'nameOfProvider' => 'required|string|max:150',
            'typeOfProvider' => 'required|string|max:150',
            'fpMUnmetMethodUsed' => 'required|boolean',
            'methodUsedType' => 'nullable|array',
            'dossage' => 'nullable|integer',
            'pack' => 'nullable|integer',
            'packs' => 'nullable|integer',
            'condom_packs' => 'nullable|integer',
            'age' => 'nullable|integer'
        ]);
    
        try {
            // Decrypt the ID
            $id = Crypt::decrypt($encryptedId);
    
            // Find or create the Implant record
            $implant = Implant::updateOrCreate(
                ['patient_id' => $id], // Matching condition
                ['age' => $validatedData['age']] // Update or insert data
            );
    
            // Update the patient's contact information
            $patient = Patient::findOrFail($id);
            $patient->update(['contact' => $validatedData['contact']]);
    
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
    
            // Map method types to input fields for previous family planning methods
            $methodMapping = [
                'DMPA' => 'dossage',
                'Pills-COC' => 'pack',
                'Pills-POP' => 'packs',
                'Condoms' => 'condom_packs',
            ];
    
            // Handle Implant Methods Used
            if (!empty($validatedData['methodUsedType'])) {
                foreach ($validatedData['methodUsedType'] as $method) {
                    ImplantMethodUsed::create([
                        'patient_id' => $id,
                        'no_of_children' => $validatedData['no_of_children'],
                        'name_of_provider' => $validatedData['nameOfProvider'],
                        'type_of_provider' => $validatedData['typeOfProvider'],
                        'fp_unmet_method_used' => $validatedData['fpMUnmetMethodUsed'],
                        'previous_fp_method' => $method,
                        'quantity' => $validatedData[$methodMapping[$method]] ?? null,
                    ]);
                }
            }
    
            // Return success response
            return response()->json(['status' => 200, 'message' => 'Implant created or updated successfully.']);
        } catch (\Exception $e) {
            // Return failure response with exception message
            return response()->json(['status' => 500, 'error' => 'Failed to save implant enrolment.', 'exception' => $e->getMessage()]);
        }
    }    

    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            Implant::where('patient_id', $id)->delete();
            ImplantMethodUsed::where('patient_id', $id)->delete();

            return response()->json(['success' => true ]);
        } catch (DecryptException $e) {
            return response()->json(['success' => false ]);
        }
    }
}