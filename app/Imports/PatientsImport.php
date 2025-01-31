<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PatientsImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'patient_records' => new PatientRecord(),
            'consultation_records' => new ConsultationRecord(),
            'consultation_diagnosis' => new ConsultationDiagnosisRecord(),
            'dental_records' => new DentalRecord(),
            'dental_diagnosis' => new DentalDiagnosisRecord(),
        ];
    }
}