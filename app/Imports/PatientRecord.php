<?php

namespace App\Imports;

use App\Models\Patient;
use App\Models\Gender;
use App\Models\Address;
use App\Models\Barangay; 
use App\Models\CityMunicipality; 
use App\Models\Province; 
use App\Models\Region; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PatientRecord implements ToModel, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 6; // Skip the first 5 rows
    }

    public function model(array $row)
    {
        // Convert Excel date to PHP DateTime format
        $birthDate = Date::excelToDateTimeObject($row[7])->format('Y-m-d');
        $createdAt = Date::excelToDateTimeObject($row[11])->format('Y-m-d H:i:s');

        $gender = Gender::where('gender_name', $row[9] ?? null)->first();
        $gender_id = $gender->gender_id ?? null;

        // Create Patient record
        $patient = Patient::create([
            'patient_id' => $row[0], 
            'family_number' => $row[1],
            'first_name' => $row[2],
            'middle_name' => $row[3],
            'last_name' => $row[4],
            'suffix_name' => $row[5],
            'birth_date' => $birthDate,
            'age' => $row[8],
            'gender_id' => $gender_id,
            'created_at' => $createdAt,
        ]);

        // Extract address parts
        $addressParts = isset($row[6]) ? explode(',', $row[6]) : [];
        $barangay = trim($addressParts[0] ?? '');
        $municipality = trim($addressParts[1] ?? '');
        $province = trim($addressParts[2] ?? '');

        // Get province details
        $provinceRecord = Province::where('provDesc', $province)->first();
        $province_id = $provinceRecord->provCode ?? null;
        $region_id = $provinceRecord->regCode ?? null;

        // Get municipality based on province_id
        $municipalityRecord = CityMunicipality::where('provCode', $province_id)->where('citymunDesc', $municipality)->first();
        $municipal_id = $municipalityRecord->citymunCode ?? null;

        // Get barangay details
        $barangayRecord = Barangay::where('citymunCode', $municipal_id)->where('brgyDesc', $barangay)->first();
        $barangay_id = $barangayRecord->brgyCode ?? null;

        // Create Address record only if patient is created
        if ($patient) {
            Address::create([
                'patient_id' => $row[0], // Use the actual created patient's ID
                'barangay_id' => $barangay_id,
                'municipality_id' => $municipal_id,
                'province_id' => $province_id,
                'region_id' => $region_id,
                'created_at' => $createdAt,
            ]);
        }

        return $patient; // Return the patient model
    }
}