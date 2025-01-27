<?php

namespace App\Http\Controllers\nurse_and_staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Patient;
use App\Models\Address;
use App\Models\ImportLog; 
use App\Models\Consultation; 
use App\Models\Dental; 
use App\Models\Gender; 
use App\Models\Diagnosis; 
use App\Models\ConsultationDiagnosis; 
use App\Models\ConsultationQueue; 
use App\Models\ConsultationAnalytics; 
use App\Models\DentalDiagnosis; 
use App\Models\DentalAnalytics; 
use App\Models\DentalQueue; 
use App\Models\DiagnosisAnalytics; 
use App\Models\FamilyPlanning; 
use App\Models\FamilyMethodUsed; 
use App\Models\Barangay; 
use App\Models\CityMunicipality; 
use App\Models\Province; 
use App\Models\Region; 
use App\Models\User; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportPatients extends Controller
{
    public function index()
    {
        return view('content.nurse_and_staff.import.import_patient');
    }

    public function import(Request $request)
    {
        // Validate the file
        $request->validate([
           'importFile' => 'required|mimes:xlsx,xlsm',
        ]);
    
        if ($request->hasFile('importFile')) {
            $file = $request->file('importFile');
            
            if (!$file || !$file->isValid()) {
                return response()->json(['message' => 'Invalid file upload.'], 400);
            }
    
            $filePath = $file->getRealPath();
            $fileName = $file->getClientOriginalName();
    
            if (!file_exists($filePath) || filesize($filePath) === 0) {
                return response()->json(['message' => 'The uploaded file is empty or inaccessible.'], 400);
            }
    
    
            $patients = [];
            $addresses = [];
            $consultations = [];
            $consultationsDiagnosis = [];
            $followUpconsultationsDiagnosis = [];
            $dentals = [];
            $dentalsDiagnosis = [];
            $followUpdentalsDiagnosis = [];
            $rowCount = 0;
    
            try {
                 // Detect the type and load the spreadsheet
            $readerType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($readerType);
            $spreadsheet = $reader->load($filePath);

            $patientsSheet = $spreadsheet->getSheetByName('patients_records');
            if (!$patientsSheet) {
                return response()->json(['message' => 'Sheet "patients_records" not found.'], 400);
            }
                if ($patientsSheet) {
                    foreach ($patientsSheet->getRowIterator() as $row) {
                        if ($row->getRowIndex() <= 5) { // Skip rows 1 to 5
                            continue;
                        }
    
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false); 
    
                        $patientData = [];
                        foreach ($cellIterator as $cell) {
                            $patientData[] = $cell->getValue();
                        }
    
                        if (empty($patientData[0])) {
                            continue; // Skip row if patient_id is empty
                        }
    
                        $gender = Gender::where('gender_name', $patientData[9] ?? null)->first();
                        $gender_id = $gender->gender_id ?? null;

                        // Prepare patient data
                        $patients[] = [
                            'patient_id' => $patientData[0] ?? null,
                            'family_number' => $patientData[1] ?? null,
                            'first_name' => $patientData[2] ?? null,
                            'middle_name' => $patientData[3] ?? null,
                            'last_name' => $patientData[4] ?? null,
                            'suffix_name' => $patientData[5] ?? null,
                            'birth_date' => $patientData[7] ? Date::excelToDateTimeObject($patientData[7]) : null,
                            'age' => $patientData[8] ?? null,
                            'gender_id' => $gender_id,
                            'contact' => '0'. $patientData[10] ?? null,
                            'created_at' => $patientData[11] ? Date::excelToDateTimeObject($patientData[11]) : null,
                            'updated_at' => $patientData[11] ? Date::excelToDateTimeObject($patientData[11]) : null,
                        ];

    
                        // Parse address information
                        $addressParts = isset($patientData[6]) ? explode(',', $patientData[6]) : [];
                        $barangay = trim($addressParts[0] ?? null);
                        $municipality = trim($addressParts[1] ?? null);
                        $province = trim($addressParts[2] ?? null);

    
                        $provinceRecord = Province::where('provDesc', $province)->first();
                        $province_id = $provinceRecord->provCode ?? null;
                        $region_id = $provinceRecord->regCode ?? null;

                        // Get municipality based on province_id
                        $municipalityRecord = CityMunicipality::where('provCode', $province_id)->where('citymunDesc', $municipality)->first();
                        $municipal_id = $municipalityRecord->citymunCode ?? null;

                        // Get brgyCode, municipal_id, province_id, and regCode
                        $barangayRecord = Barangay::where('citymunCode', $municipal_id)->where('brgyDesc', $barangay)->first();
                        $barangay_id = $barangayRecord->brgyCode ?? null;
                     

                        // Prepare address data
                        $addresses[] = [
                            'patient_id' => $patientData[0] ?? null, // Foreign key to link with patient
                            'barangay_id' => $barangay_id,
                            'municipality_id' => $municipal_id,
                            'province_id' => $province_id,
                            'region_id' => $region_id,
                            'created_at' => $patientData[11] ? Date::excelToDateTimeObject($patientData[11]) : null,
                            'updated_at' => $patientData[11] ? Date::excelToDateTimeObject($patientData[11]) : null,
                        ];
                    }
                }


                // Get the consultation_records sheet
                $consultationSheet = $spreadsheet->getSheetByName('consultation_records');
                if ($consultationSheet) {
                    foreach ($consultationSheet->getRowIterator() as $row) {
                        if ($row->getRowIndex() <= 5) { // Skip rows 1 to 5
                            continue;
                        }
    
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);
    
                        $consultationData = [];
                        foreach ($cellIterator as $cell) {
                            $consultationData[] = $cell->getValue();
                        }
    
                        if (empty($consultationData[0])) {
                            continue; // Skip row if consultation_id is empty
                        }
    
                        // Prepare consultation data
                        $consultations[] = [
                            'id' => $consultationData[0] ?? null,
                            'patient_id' => $consultationData[1] ?? null,
                            'blood_pressure' => $consultationData[2] ?? null,
                            'body_temperature' => $consultationData[3] ?? null,
                            'height' => $consultationData[4] ?? null,
                            'weight' => $consultationData[5] ?? null,
                            'chief_complaints' => $consultationData[6] ?? null,
                            'number_of_days' => $consultationData[7] ?? null,
                            'emergency_purposes' => $consultationData[8] ?? null,
                            'created_at' => $consultationData[9] ? Date::excelToDateTimeObject($consultationData[9]) : null,
                            'updated_at' => $consultationData[9] ? Date::excelToDateTimeObject($consultationData[9]) : null,
                        ];
                    }
                }
                

                 // Get the consultation_records sheet
                 $consultationDiagnosisSheet = $spreadsheet->getSheetByName('consultation_diagnosis');
                 if ($consultationDiagnosisSheet) {
                     foreach ($consultationDiagnosisSheet->getRowIterator() as $row) {
                         if ($row->getRowIndex() <= 5) { // Skip rows 1 to 5
                             continue;
                         }
     
                         $cellIterator = $row->getCellIterator();
                         $cellIterator->setIterateOnlyExistingCells(false);
     
                         $consultationDiagnosisData = [];
                         foreach ($cellIterator as $cell) {
                             $consultationDiagnosisData[] = $cell->getValue();
                         }
     
                         if (empty($consultationDiagnosisData[0])) {
                             continue; // Skip row if consultation_id is empty
                         }

                           
                        $diagnosis = Diagnosis::where('diagnosis_name', $consultationDiagnosisData[2] ?? null)->first();
                        $diagnosis_id = $diagnosis->diagnosis_id ?? null;

     
                         // Prepare consultation data
                         $consultationsDiagnosis[] = [
                             'id' => $consultationDiagnosisData[0] ?? null,
                             'patient_id' => $consultationDiagnosisData[1] ?? null,
                             'diagnosis_id' => $diagnosis_id,
                             'description' => $consultationDiagnosisData[3] ?? null,
                             'created_at' => $consultationDiagnosisData[5] ? Date::excelToDateTimeObject($consultationDiagnosisData[5]) : null,
                             'updated_at' => $consultationDiagnosisData[5] ? Date::excelToDateTimeObject($consultationDiagnosisData[5]) : null,
                         ];

                        $followUpconsultationsDiagnosis[] = [
                            'patient_id' => $consultationDiagnosisData[1] ?? null,
                            'created_at' => $consultationDiagnosisData[4] ? Date::excelToDateTimeObject($consultationDiagnosisData[4]) : null,
                            'updated_at' => $consultationDiagnosisData[4] ? Date::excelToDateTimeObject($consultationDiagnosisData[4]) : null,
                        ];
                     }
                 }

                // Get the consultation_records sheet
                $dentalSheet = $spreadsheet->getSheetByName('dental_records');
                if ($dentalSheet) {
                    foreach ($dentalSheet->getRowIterator() as $row) {
                        if ($row->getRowIndex() <= 5) { // Skip rows 1 to 5
                            continue;
                        }
    
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);
    
                        $dentalData = [];
                        foreach ($cellIterator as $cell) {
                            $dentalData[] = $cell->getValue();
                        }
    
                        if (empty($dentalData[0])) {
                            continue; // Skip row if consultation_id is empty
                        }
    
                        // Prepare consultation data
                        $dentals[] = [
                            'id' => $dentalData[0] ?? null,
                            'patient_id' => $dentalData[1] ?? null,
                            'blood_pressure' => $dentalData[2] ?? null,
                            'body_temperature' => $dentalData[3] ?? null,
                            'height' => $dentalData[4] ?? null,
                            'weight' => $dentalData[5] ?? null,
                            'chief_complaints' => $dentalData[6] ?? null,
                            'number_of_days' => $dentalData[7] ?? null,
                            'emergency_purposes' => $dentalData[8] ?? null,
                            'created_at' => $dentalData[9] ? Date::excelToDateTimeObject($dentalData[9]) : null,
                            'updated_at' => $dentalData[9] ? Date::excelToDateTimeObject($dentalData[9]) : null,
                        ];
                    }
                }

                 // Get the consultation_records sheet
                 $dentalsDiagnosisSheet = $spreadsheet->getSheetByName('dental_diagnosis');
                 if ($dentalsDiagnosisSheet) {
                     foreach ($dentalsDiagnosisSheet->getRowIterator() as $row) {
                         if ($row->getRowIndex() <= 5) { // Skip rows 1 to 5
                             continue;
                         }
     
                         $cellIterator = $row->getCellIterator();
                         $cellIterator->setIterateOnlyExistingCells(false);
     
                         $dentalDiagnosisData = [];
                         foreach ($cellIterator as $cell) {
                             $dentalDiagnosisData[] = $cell->getValue();
                         }
     
                         if (empty($dentalDiagnosisData[0])) {
                             continue; // Skip row if consultation_id is empty
                         }

                           
                        $diagnosis = Diagnosis::where('diagnosis_name', $dentalDiagnosisData[2] ?? null)->first();
                        $diagnosis_id = $diagnosis->diagnosis_id ?? null;

     
                         // Prepare consultation data
                         $dentalsDiagnosis[] = [
                             'id' => $dentalDiagnosisData[0] ?? null,
                             'patient_id' => $dentalDiagnosisData[1] ?? null,
                             'diagnosis_id' => $diagnosis_id,
                             'description' => $dentalDiagnosisData[3] ?? null,
                             'created_at' => $dentalDiagnosisData[5] ? Date::excelToDateTimeObject($dentalDiagnosisData[5]) : null,
                             'updated_at' => $dentalDiagnosisData[5] ? Date::excelToDateTimeObject($dentalDiagnosisData[5]) : null,
                         ];

                        $followUpdentalsDiagnosis[] = [
                            'patient_id' => $dentalDiagnosisData[1] ?? null,
                            'created_at' => $dentalDiagnosisData[4] ? Date::excelToDateTimeObject($dentalDiagnosisData[4]) : null,
                            'updated_at' => $dentalDiagnosisData[4] ? Date::excelToDateTimeObject($dentalDiagnosisData[4]) : null,
                        ];
                     }
                  }

                    // Insert or update patients, addresses, consultations, and consultations diagnosis in the database
                    DB::transaction(function () use ($patients, $addresses, $consultations, $consultationsDiagnosis, $followUpconsultationsDiagnosis, $dentals, $dentalsDiagnosis, $followUpdentalsDiagnosis, &$rowCount) {
                        foreach ($patients as $key => $patient) {
                            // Check if the patient already exists
                            $existingPatient = Patient::where('first_name', $patient['first_name'])
                                ->where('last_name', $patient['last_name'])
                                ->where('gender_id', $patient['gender_id'])
                                ->where('birth_date', $patient['birth_date'])
                                ->where('family_number', $patient['family_number'])
                                ->first();
                        
                            if ($existingPatient) {
                                // Update the existing patient record
                                $existingPatient->update($patient);
                        
                                // Check if the address for this patient exists
                                $existingAddress = Address::where('patient_id', $existingPatient->patient_id)->first();
                        
                                if ($existingAddress) {
                                    // Update the existing address
                                    $existingAddress->update($addresses[$key]);
                                } else {
                                    // If no address exists, create a new one
                                    Address::create(array_merge(['patient_id' => $existingPatient->patient_id], $addresses[$key]));
                                }
                            } else {
                                // No existing patient found, create a new record
                                $newPatient = Patient::create($patient);
                                $rowCount++;
                        
                                // Save or create address for the new patient
                                Address::updateOrCreate(
                                    ['patient_id' => $newPatient->patient_id], // Match on patient_id
                                    $addresses[$key]
                                );
                            }
                        }
                        
                        
                        // Insert consultation data
                        foreach ($consultations as $consultation) {
                            Consultation::updateOrCreate(
                                ['id' => $consultation['id']], // Match on consultation_id
                                $consultation
                            );
                        }

                        // Insert consultation diagnosis data
                        foreach ($consultationsDiagnosis as $key => $consultationDiagnosis) {
                            ConsultationDiagnosis::updateOrCreate(
                                ['id' => $consultationDiagnosis['id']], // Match on consultation_id
                                $consultationDiagnosis
                            );

                            ConsultationQueue::updateOrCreate(
                                ['patient_id' => $consultationDiagnosis['patient_id']], // Match on consultation_id
                                $followUpconsultationsDiagnosis[$key]
                            );
                        }

                        // Insert dental data
                        foreach ($dentals as $dental) {
                            Dental::updateOrCreate(
                                ['id' => $dental['id']], // Match on consultation_id
                                $dental
                            );
                        }

                        // Insert dental diagnosis data
                        foreach ($dentalsDiagnosis as $key => $dentalDiagnosis) {
                            DentalDiagnosis::updateOrCreate(
                                ['id' => $dentalDiagnosis['id']], // Match on consultation_id
                                $dentalDiagnosis
                            );

                            DentalQueue::updateOrCreate(
                                ['patient_id' => $dentalDiagnosis['patient_id']], // Match on consultation_id
                                $followUpdentalsDiagnosis[$key]
                            );
                        }

                        // Insert Consultation and Dental Analytics data
                        foreach ($patients as $key => $patient) {
                            $patientId = $patient['patient_id'];
                            $age = $patient['age'];

                            // Get barangay name from the address
                            $barangayName = $addresses[$key]['barangay_id'] ?? null;
                            // Get brgyCode, municipal_id, province_id, and regCode
                            $barangayRecord = Barangay::where('brgyCode', $barangayName)->first();
                            $barangay_id = $barangayRecord->brgyDesc ?? null;

                            // Fetch all consultation records for the patient
                            $consultations = Consultation::where('patient_id', $patientId)->get();
                            foreach ($consultations as $consultation) {
                                ConsultationAnalytics::updateOrCreate(
                                    ['patient_id' => $patientId, 'consultation_id' => $consultation->id],
                                    [
                                        'patient_id' => $patientId,
                                        'consultation_id' => $consultation->id,
                                        'barangay_name' => $barangay_id,
                                        'age' => $age,
                                    ]
                                );
                            }

                            // Fetch all dental records for the patient
                            $dentals = Dental::where('patient_id', $patientId)->get();
                            foreach ($dentals as $dental) {
                                DentalAnalytics::updateOrCreate(
                                    ['patient_id' => $patientId, 'dental_id' => $dental->id],
                                    [
                                        'patient_id' => $patientId,
                                        'dental_id' => $dental->id,
                                        'barangay_name' => $barangay_id,
                                        'age' => $age,
                                    ]
                                );
                            }

                            // Fetch consultation diagnosis records
                            $consultationDiagnoses = ConsultationDiagnosis::where('patient_id', $patientId)->get();
                            foreach ($consultationDiagnoses as $consultationDiagnosis) {
                                $consultationDiagnosisId = $consultationDiagnosis->diagnosis_id;
                                if ($consultationDiagnosisId) {
                                    DiagnosisAnalytics::updateOrCreate(
                                        [
                                            'patient_id' => $patientId,
                                            'diagnosis_id' => $consultationDiagnosisId,
                                        ],
                                        [
                                            'patient_id' => $patientId,
                                            'diagnosis_id' => $consultationDiagnosisId,
                                            'barangay_name' => $barangay_id,
                                            'age' => $age,
                                        ]
                                    );
                                }
                            }

                            // Fetch dental diagnosis records
                            $dentalDiagnoses = DentalDiagnosis::where('patient_id', $patientId)->get();
                            foreach ($dentalDiagnoses as $dentalDiagnosis) {
                                $dentalDiagnosisId = $dentalDiagnosis->diagnosis_id;
                                if ($dentalDiagnosisId) {
                                    DiagnosisAnalytics::updateOrCreate(
                                        [
                                            'patient_id' => $patientId,
                                            'diagnosis_id' => $dentalDiagnosisId,
                                        ],
                                        [
                                            'patient_id' => $patientId,
                                            'diagnosis_id' => $dentalDiagnosisId,
                                            'barangay_name' => $barangay_id,
                                            'age' => $age,
                                        ]
                                    );
                                }
                            }
                        }


                    });


    
                $user = Auth::user();
    
                // Log the import
                ImportLog::create([
                    'user_id' => $user->id,
                    'file_name' => $fileName,
                    'total_records' => $rowCount,
                    'created_at' => Carbon::now(),
                ]);
    
                return response()->json([
                    'status' => 200,
                    'message' => "{$rowCount} patient records imported successfully."
                ]);
            } catch (\Exception $e) {
                // Catch any exceptions and log them
                return response()->json([
                    'status' => 500,
                    'message' => 'An error occurred while importing the file: ' . $e->getMessage(),
                ]);
            }
        }
    
        return response()->json([
            'status' => 400,
            'message' => 'No file was uploaded.',
        ]);
    }
    
    public function fetchImport(Request $request)
    {
        $pageSize = $request->input('pageSize', 10); // Default to 10 if not provided
        $searchFileName = $request->input('searchFileName'); // Get the searchFileName from the request
      
        $user = Auth::user();
        
        // Fetch ImportLog data with the required columns
        $query = ImportLog::select('import_log.id', 'import_log.file_name', 'import_log.total_records', 'import_log.created_at')
        ->where('import_log.user_id', $user->id)
        ->orderBy('import_log.created_at', 'asc');
       
        // If search parameter is provided, add a 'where' condition
        if ($searchFileName) {
            $query->where('import_log.file_name', 'like', '%' . $searchFileName . '%');
        }
       
        // Apply pagination after the search filter
        $importPatient = $query->paginate($pageSize);
        
        // Process the data: encrypt ID and format the created_at date
        foreach ($importPatient as $importPatients) {
            $importPatients->encrypted_id = Crypt::encrypt($importPatients->id);
            $importPatients->created_at_formatted = Carbon::parse($importPatients->created_at)->format('F j, Y'); 
        }
    
        // Return JSON response
        return response()->json([
            'importPatient' => $importPatient,
        ]);
    }
}