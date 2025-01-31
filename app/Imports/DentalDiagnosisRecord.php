<?php

namespace App\Imports;

use App\Models\DentalDiagnosis;
use App\Models\Diagnosis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DentalDiagnosisRecord implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 6; // Skip the first 5 rows
    }

    public function model(array $row)
    {
        $createdAt = Date::excelToDateTimeObject($row[3])->format('Y-m-d H:i:s');
      
        $diagnosis = Diagnosis::where('diagnosis_name', $row[1] ?? null)->first();
        $diagnosis_id = $diagnosis->diagnosis_id ?? null;
        
        return new DentalDiagnosis([
            'patient_id' => $row[0],
            'diagnosis_id' => $diagnosis_id,
            'description' => $row[2],
            'created_at' => $createdAt,
        ]);
    }
}