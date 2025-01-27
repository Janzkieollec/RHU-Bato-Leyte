<?php 
namespace App\Imports;

use App\Models\Patient;
use App\Models\Address;
use App\Models\Gender;
use App\Models\Province;
use App\Models\CityMunicipality;
use App\Models\Barangay;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PatientsImport implements ToModel, WithHeadingRow, WithStartRow
{
    private $rowCount = 0;

    public function startRow(): int
    {
        return 6; // Skip the first 5 rows
    }

    public function model(array $row)
    {
        $gender = Gender::where('gender_name', $row['gender'] ?? null)->first();
        $gender_id = $gender->gender_id ?? null;

        $provinceRecord = Province::where('provDesc', $row['province'] ?? null)->first();
        $province_id = $provinceRecord->provCode ?? null;
        $region_id = $provinceRecord->regCode ?? null;

        $municipalityRecord = CityMunicipality::where('provCode', $province_id)->where('citymunDesc', $row['municipality'] ?? null)->first();
        $municipal_id = $municipalityRecord->citymunCode ?? null;

        $barangayRecord = Barangay::where('citymunCode', $municipal_id)->where('brgyDesc', $row['barangay'] ?? null)->first();
        $barangay_id = $barangayRecord->brgyCode ?? null;

        // Insert or update patient and address
        $patient = Patient::updateOrCreate(
            [
                'first_name' => $row['first_name'] ?? null,
                'last_name' => $row['last_name'] ?? null,
                'birth_date' => $row['birth_date'] ?? null,
            ],
            [
                'family_number' => $row['family_number'] ?? null,
                'middle_name' => $row['middle_name'] ?? null,
                'suffix_name' => $row['suffix_name'] ?? null,
                'age' => $row['age'] ?? null,
                'gender_id' => $gender_id,
                'contact' => '0' . $row['contact'] ?? null,
            ]
        );

        Address::updateOrCreate(
            ['patient_id' => $patient->patient_id],
            [
                'barangay_id' => $barangay_id,
                'municipality_id' => $municipal_id,
                'province_id' => $province_id,
                'region_id' => $region_id,
            ]
        );

        $this->rowCount++;
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }
}